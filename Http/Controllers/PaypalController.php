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

/**
 * composer require braintree/braintree_php
 */
class PaypalController extends Controller
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
                    'type'           => 2,
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

    public function handleWebhook(Request $request)
    {
        //Verify webhook authenticity using signature verification
        $payload = $request->getContent();
        $signature = $request->header('bt_signature');
        $publicKey = config('services.braintree.public_key');
        Log::info($request);
        $isValid = WebhookNotification::verify($signature, $payload, $publicKey);

        // Process webhook event
        if ($isValid) {
            $notification = WebhookNotification::parse($payload);
            $eventType = $notification->kind;

            switch ($eventType) {
                case WebhookNotification::TRANSACTION_SETTLEMENT_DECLINED:
                    // Handle transaction settlement declined event
                    break;
                case WebhookNotification::DISBURSEMENT_EXCEPTION:
                    // Handle disbursement exception event
                    break;
                // Add more cases for other events you want to handle
            }

            // Respond with a 200 OK status code to acknowledge receipt of the webhook
            return response('Webhook received', 200);
        } else {
            // Respond with a 403 Forbidden status code if signature verification fails
            return response('Invalid signature', 403);
        }
    }
}
