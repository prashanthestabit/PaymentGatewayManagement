<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Braintree\Gateway;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\PaymentGatewayManagement\Entities\Transaction;
use Modules\PaymentGatewayManagement\Repositories\PaymentRepository;
use Braintree\WebhookNotification;
use Laravel\Cashier\Subscription;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * composer require braintree/braintree_php
 */
class braintreeController extends Controller
{

    protected $gateway;

    protected $payment;

    public function __construct(PaymentRepository $payment)
    {
        $this->payment = $payment;

        $this->gateway = new Gateway([
            'environment' => config('services.braintree.environment'),
            'merchantId' => config('services.braintree.merchant_id'),
            'publicKey' => config('services.braintree.public_key'),
            'privateKey' => config('services.braintree.private_key')
        ]);

    }

    public function store(Request $request)
    {
        try{
            $result = $this->gateway->transaction()->sale([
                'amount' => 0.10, //$request->input('amount'),
                'paymentMethodNonce' => 'fake-valid-nonce',   //$request->input('nonce'),
                'options' => [
                    'submitForSettlement' => true
                ]
            ]);

            if ($result->success) {
                 // Payment succeeded
                $responseData = [
                    'status' => true,
                    'transaction' => $result->transaction,
                ];

                $transaction = Transaction::create([
                    'type'           => 'braintree',
                    'user_id'        => auth()->user()->id,
                    'order_id'       => $result->transaction->orderId??'',
                    'transaction_id' => $result->transaction->id??'',
                    'payment_id'     => $result->transaction->id??'',
                    'amount'         => $result->transaction->amount??'',
                    'status'         => $result->transaction->status??'',
                    'currency'       => $result->transaction->currencyIsoCode??'',
                    'created_at'     => $result->transaction->createdAt??''
                ]);
                return $this->payment->responseMessage($responseData,Response::HTTP_OK);
            } else {
                // Payment failed
                $responseData = [
                    'status' => false,
                    'message' => $result->message,
                ];
                return $this->payment->responseMessage($responseData,Response::HTTP_BAD_REQUEST);
            }
        }catch(Exception $e)
        {
            // Payment failed
            Log::error("message: ". $e->getMessage());
            $responseData = [
                'status' => false,
                'message' => __('authwithjwt::messages.try_again'),
            ];
            return $this->payment->responseMessage($responseData,Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Handle a Braintree webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleWebhook(Request $request)
    {
        try {
            $webhook = $this->parseBraintreeNotification($request);
        } catch (Exception $e) {
            return;
        }

        $method = 'handle'.Str::studly(str_replace('.', '_', $webhook->kind));

        if (method_exists($this, $method)) {
            return $this->{$method}($webhook);
        }

        return $this->missingMethod();
    }

     /**
     * Parse the given Braintree webhook notification request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Braintree\WebhookNotification
     */
    protected function parseBraintreeNotification($request)
    {
        return WebhookNotification::parse($request->bt_signature, $request->bt_payload);
    }

      /**
     * Handle a subscription expiration notification from Braintree.
     *
     * @param  \Braintree\WebhookNotification  $webhook
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleSubscriptionExpired($webhook)
    {
        return $this->cancelSubscription($webhook->subscription->id);
    }

    /**
     * Handle a subscription cancellation notification from Braintree.
     *
     * @param  string  $subscriptionId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function cancelSubscription($subscriptionId)
    {
        $subscription = $this->getSubscriptionById($subscriptionId);

        if ($subscription && (! $subscription->cancelled() || $subscription->onGracePeriod())) {
            $subscription->markAsCancelled();
        }

        return new Response('Webhook Handled', 200);
    }

        /**
     * Get the model for the given subscription ID.
     *
     * @param  string  $subscriptionId
     * @return \Laravel\Cashier\Subscription|null
     */
    protected function getSubscriptionById($subscriptionId): ?Subscription
    {
        return Subscription::where('braintree_id', $subscriptionId)->first();
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function missingMethod(array $parameters = [])
    {
        return new Response;
    }

}
