<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use DateTime;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Modules\PaymentGatewayManagement\Entities\Transaction;

/**
 * Before use run php artisan vendor:publish --tag="cashier-config"
 * also add STRIPE_WEBHOOK_SECRET on .env file
 */
class StripeWebhookController extends CashierController
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        Log:info($payload);

        $event = json_decode($payload, true);

        // Handle the event
        switch ($event['type']) {
            case 'customer.subscription.updated':
                $this->handleCustomerSubscriptionUpdated($event);
                // Handle the subscription status update event
                break;
            case 'customer.updated':
                $this->handleCustomerUpdated($event);
                // Handle the customer email update event
                break;
            case 'charge.succeeded':
                $this->handleChargeSucceeded($event);
                $status = $event['data']['object']['status'];
                // Handle the succeeded statusevent
                break;
            case 'charge.refunded':
                $this->handleChargeRefunded($event);
                // Handle the refunded status event
                break;
            case 'payment_intent.created':
               $this->handlePaymentIntentCreated($event);
               //Handle the requires_payment_method status event
            default:
                $this->handleOtherEvent($event);
                // Handle other events
                break;
        }
        return response()->json(['success' => true]);
    }

    /**
     * Handle the charge refunded event.
     *
     * @param  \Stripe\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleChargeRefunded($event)
    {
        $charge = $event['data']['object'];

        $transaction = new Transaction([
            'type' => 'stripe',
            'user_id' => isset($charge['metadata']['user_id']) ? $charge['metadata']['user_id'] : null,
            'order_id' => isset($charge['metadata']['order_id']) ? $charge['metadata']['order_id'] : null,
            'transaction_id' => isset($charge['id']) ? $charge['id'] : null,
            'payment_id' => isset($charge['payment_intent']) ? $charge['payment_intent'] : null,
            'amount' => isset($charge['amount_refunded']) ? $charge['amount_refunded'] : null,
            'currency' => isset($charge['currency']) ? $charge['currency'] : null,
            'status' => 'refunded',
            'description' => isset($charge['description']) ? $charge['description'] : null,
            'customer_id' => isset($charge['customer']) ? $charge['customer'] : null,
            'card_last_four' => isset($charge['payment_method_details']['card']['last4']) ? $charge['payment_method_details']['card']['last4'] : null,
            'card_brand' => isset($charge['payment_method_details']['card']['brand']) ? $charge['payment_method_details']['card']['brand'] : null,
            'failure_code' => isset($charge['failure_code']) ? $charge['failure_code'] : null,
            'failure_message' => isset($charge['failure_message']) ? $charge['failure_message'] : null,
            'metadata' => json_encode(isset($charge['metadata']) ? $charge['metadata'] : null),
            'created_at' => isset($charge['created']) ? (new DateTime())->setTimestamp($charge['created'])->format('Y-m-d H:i:s'): now(),
        ]);
        $transaction->save();

        return $this->successMethod();
    }

    /**
     * Handle the subscription updated event.
     *
     * @param  \Stripe\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionUpdated($event)
    {

        $subscription = $event['data']['object'];

        // Create a new Transaction object with the extracted data.
        $transaction = new Transaction([
            'type' => 'stripe',
            'user_id' => $subscription['metadata']['user_id'] ?? null,
            'order_id' => $subscription['metadata']['order_id'] ?? null,
            'transaction_id' => $subscription['id'],
            'payment_id' => $subscription['latest_invoice'] ?? null,
            'amount' => $subscription['plan']['amount'],
            'currency' => $subscription['plan']['currency'],
            'status' => $subscription['status'],
            'description' => 'Subscription updated',
            'customer_id' => $subscription['customer'],
            'card_last_four' => $subscription['default_payment_method']['card']['last4'] ?? null,
            'card_brand' => $subscription['default_payment_method']['card']['brand'] ?? null,
            'metadata' => $subscription['metadata'] ?? null,
            'created_at' => now(),
        ]);

        // Save the Transaction object to the database.
        $transaction->save();

        return $this->successMethod();
    }

    /**
     * Handle the customer updated event.
     *
     * @param  \Stripe\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerUpdated($event)
    {
        $email = $event['data']['object']['email'];

        $transaction = new Transaction([
            'type' => 'stripe', // stripe payment gateway type
            'user_id' => $event['data']['object']['metadata']['user_id'] ?? null,
            'customer_id' => $event['data']['object']['id'] ?? null,
            'updated_fields' => implode(',', array_keys($event['data']['previous_attributes'])) ?? null,
            'metadata' => json_encode($event['data']['object']['metadata']) ?? null,
            'created_at' => now(),
        ]);

        return $this->successMethod();
    }

    /**
     * Handle the charge succeeded event.
     *
     * @param  \Stripe\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleChargeSucceeded($event)
    {
        $charge = $event['data']['object'];

        $transaction = new Transaction([
            'type' => 'stripe', // or other payment gateway type
            'user_id' => $charge['metadata']['user_id'] ?? null,
            'order_id' => $charge['metadata']['order_id'] ?? null,
            'transaction_id' => $charge['id'],
            'payment_id' => $charge['payment_intent'] ?? null,
            'amount' => $charge['amount'] / 100, // convert from cents to dollars
            'currency' => $charge['currency'],
            'status' => $charge['status'],
            'description' => $charge['description'] ?? null,
            'customer_id' => $charge['customer'] ?? null,
            'card_last_four' => $charge['payment_method_details']['card']['last4'] ?? null,
            'card_brand' => $charge['payment_method_details']['card']['brand'] ?? null,
            'metadata' => $charge['metadata'] ?? null,
            'created_at' => date('Y-m-d H:i:s', $charge['created']),
        ]);

        $transaction->save();

        return $this->successMethod();
    }

    /**
     * Handle the payment intent created event.
     *
     * @param  \Stripe\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePaymentIntentCreated($event)
    {
        $paymentIntent = $event['data']['object'];

        // create a new transaction record
        $transaction = new Transaction([
            'type' => 'stripe',
            'user_id' => $paymentIntent['metadata']['user_id'] ?? null,
            'order_id' => $paymentIntent['metadata']['order_id'] ?? null,
            'transaction_id' => $paymentIntent['id'],
            'payment_id' => $paymentIntent['id'],
            'amount' => $paymentIntent['amount'] / 100, // convert to dollars or whatever currency
            'currency' => $paymentIntent['currency'],
            'status' => $paymentIntent['status'],
            'description' => $paymentIntent['description'] ?? null,
            'customer_id' => $paymentIntent['customer'] ?? null,
            'card_last_four' => $paymentIntent['card']['last4'] ?? null,
            'card_brand' => $paymentIntent['card']['brand'] ?? null,
            'created_at' => date('Y-m-d H:i:s', $paymentIntent['created']),
        ]);

        $transaction->save();

        return $this->successMethod();
    }

    /**
     * Handle other events.
     *
     * @param  \Stripe\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleOtherEvent($event)
    {
        // Log the event
        Log::info('Unhandled Stripe webhook event: ' . $event['type']);

        return $this->successMethod();
    }
}
