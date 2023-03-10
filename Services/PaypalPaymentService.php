<?php

namespace Modules\PaymentGatewayManagement\Services;

use Modules\PaymentGatewayManagement\Repositories\PaymentRepository;
use Illuminate\Support\Facades\Log;

class PaypalPaymentService
{
    protected $payment;

    public function __construct(PaymentRepository $payment)
    {
        $this->payment = $payment;
    }

    public function handleEvent($event)
    {
        // Handle the event
        switch ($event['event_type']) {
            case 'PAYMENT.CAPTURE.COMPLETED':
                $this->handlePaymentCaptureCompleted($event);
                break;
            case 'PAYMENT.CAPTURE.DENIED':
                $this->handlePaymentCaptureDenied($event);
                break;
            case 'PAYMENT.CAPTURE.PENDING':
                $this->handlePaymentCapturePending($event);
                break;
            case 'PAYMENT.CAPTURE.REFUNDED':
                $this->handlePaymentCaptureRefunded($event);
                break;
            case 'PAYMENT.CAPTURE.REVERSED':
                $this->handlePaymentCaptureReversed($event);
                break;
            default:
                $this->handleOtherEvent($event);
                // Handle other events
                break;
        }
    }

    /**
     * Handle other events.
     *
     * @param  \Paypal\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePaymentCaptureCompleted($event)
    {
        $transaction = $this->payment->store([
            'type' => 'paypal',
            'user_id' => $event['resource']['custom_id'] ?? null,
            'order_id' => $event['resource']['invoice_id'] ?? null,
            'transaction_id' => $event['resource']['id'] ?? null,
            'payment_id' => $event['resource']['parent_payment'] ?? null,
            'amount' => $event['resource']['amount']['total'] ?? null,
            'currency' => $event['resource']['amount']['currency'] ?? null,
            'status' => 'completed',
            'description' => $event['resource']['description'] ?? null,
            'customer_id' => $event['resource']['payer']['payer_id'] ?? null,
            'card_last_four' => null,
            'card_brand' => null,
            'refunded_at' => null,
            'failure_code' => null,
            'failure_message' => null,
            'metadata' => json_encode($event),
            'created_at' => now(),
        ]);

        return true;
    }

    /**
     * Handle other events.
     *
     * @param  \Paypal\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePaymentCaptureDenied($event)
    {
        $transaction = $this->payment->store([
            'type' => 'paypal',
            'user_id' => $event['resource']['custom_id'] ?? null,
            'order_id' => $event['resource']['invoice_id'] ?? null,
            'transaction_id' => $event['resource']['id'] ?? null,
            'payment_id' => $event['resource']['parent_payment'] ?? null,
            'amount' => $event['resource']['amount']['total'] ?? null,
            'currency' => $event['resource']['amount']['currency'] ?? null,
            'status' => 'denied',
            'description' => $event['resource']['description'] ?? null,
            'customer_id' => $event['resource']['payer']['payer_id'] ?? null,
            'card_last_four' => null,
            'card_brand' => null,
            'refunded_at' => null,
            'failure_code' => $event['resource']['reason_code'] ?? null,
            'failure_message' => $event['resource']['reason_description'] ?? null,
            'metadata' => json_encode($event),
            'created_at' => now(),
        ]);

        return true;
    }

    /**
     * Handle other events.
     *
     * @param  \Paypal\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePaymentCapturePending($event)
    {
        $transaction = $this->payment->store([
            'type' => 'paypal',
            'user_id' => $event['resource']['custom_id'] ?? null,
            'order_id' => $event['resource']['invoice_id'] ?? null,
            'transaction_id' => $event['resource']['id'] ?? null,
            'payment_id' => $event['resource']['parent_payment'] ?? null,
            'amount' => $event['resource']['amount']['total'] ?? null,
            'currency' => $event['resource']['amount']['currency'] ?? null,
            'status' => 'pending',
            'description' => $event['resource']['description'] ?? null,
            'customer_id' => $event['resource']['payer']['payer_id'] ?? null,
            'card_last_four' => null,
            'card_brand' => null,
            'refunded_at' => null,
            'failure_code' => $event['resource']['reason_code'] ?? null,
            'failure_message' => $event['resource']['reason_description'] ?? null,
            'metadata' => json_encode($event),
            'created_at' => now(),
        ]);

        return true;
    }

    /**
     * Handle other events.
     *
     * @param  \Paypal\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePaymentCaptureRefunded($event)
    {
        $transaction = $this->payment->store([
            'type' => 'paypal',
            'user_id' => $event['resource']['custom_id'] ?? null,
            'order_id' => $event['resource']['invoice_id'] ?? null,
            'transaction_id' => $event['resource']['id'] ?? null,
            'payment_id' => $event['resource']['parent_payment'] ?? null,
            'amount' => $event['resource']['amount']['total'] ?? null,
            'currency' => $event['resource']['amount']['currency'] ?? null,
            'status' => 'refunded',
            'description' => $event['resource']['description'] ?? null,
            'customer_id' => $event['resource']['payer']['payer_id'] ?? null,
            'card_last_four' => null,
            'card_brand' => null,
            'refunded_at' => null,
            'failure_code' => null,
            'failure_message' => null,
            'metadata' => json_encode($event),
            'created_at' => now(),
        ]);

        return true;
    }

    /**
     * Handle other events.
     *
     * @param  \Paypal\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePaymentCaptureReversed($event)
    {
        $transaction = $this->payment->store([
            'type' => 'paypal',
            'user_id' => $event['resource']['custom_id'] ?? null,
            'order_id' => $event['resource']['invoice_id'] ?? null,
            'transaction_id' => $event['resource']['id'] ?? null,
            'payment_id' => $event['resource']['parent_payment'] ?? null,
            'amount' => $event['resource']['amount']['total'] ?? null,
            'currency' => $event['resource']['amount']['currency'] ?? null,
            'status' => 'reversed',
            'description' => $event['resource']['description'] ?? null,
            'customer_id' => $event['resource']['payer']['payer_id'] ?? null,
            'card_last_four' => null,
            'card_brand' => null,
            'refunded_at' => null,
            'failure_code' => null,
            'failure_message' => null,
            'metadata' => json_encode($event),
            'created_at' => now(),
        ]);

        return true;
    }

    /**
     * Handle other events.
     *
     * @param  \Paypal\Event  $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleOtherEvent($event)
    {
        // Log the event
        Log::info('Unhandled Paypal webhook event: ' . $event['event_type']);

        return true;
    }

    protected function getGatewayOptions()
    {
        return [
            'testMode' => env('PAYPAL_SANDBOX'), // set to true if using PayPal sandbox environment
            'username' => env('PAYPAL_USERNAME'), // PayPal API username
            'password' => env('PAYPAL_PASSWORD'), // PayPal API password
            'signature' => env('PAYPAL_SIGNATURE'), // PayPal API signature
        ];
    }

}
