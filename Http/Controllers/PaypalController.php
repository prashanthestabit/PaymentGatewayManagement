<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PaymentGatewayManagement\Http\Requests\Paypal\PaymentCreateRequest;
use Omnipay\Omnipay;
use Modules\PaymentGatewayManagement\Repositories\PaymentRepository;

/**
 * Class PaypalController
 * This class handles the payment flow for PayPal Express Checkout
 * add packege composer require omnipay/paypal for use this
 * @property Omnipay\Common\AbstractGateway $gateway The gateway for PayPal Express Checkout
 */
class PaypalController extends Controller
{
    /*
    * The gateway instance for PayPal Express Checkout
    *  @var Omnipay\Common\AbstractGateway
    */
    protected $gateway;

    protected $payment;

    /**
     * Create a new instance of the controller
     * @return void
     */
    public function __construct(PaymentRepository $payment)
    {
        $this->payment = $payment;

        // Set up the gateway instance for PayPal Express Checkout
        $this->gateway = Omnipay::create('PayPal_Express');
        $this->gateway->setUsername(env('PAYPAL_USERNAME'));
        $this->gateway->setPassword(env('PAYPAL_PASSWORD'));
        $this->gateway->setSignature(env('PAYPAL_SIGNATURE'));
        $this->gateway->setTestMode(env('PAYPAL_SANDBOX'));
    }

    /**
     * Create a new payment request and give link for redirect to PayPal
     * @param Request $request The incoming request object
     * @return JsonResponse The JSON response indicating success or failure of the request
     */
    public function createPayment(PaymentCreateRequest $request)
    {
        // Set the amount and currency
        $params = [
            'amount' => $request->input('amount'),
            'currency' => 'USD',
            'returnUrl' => route('paypal.executePayment'),
            'cancelUrl' => route('paypal.cancelPayment'),
        ];

        // Send the purchase request
        $response = $this->gateway->purchase($params)->send();

        // Check if the response is a redirect
        if ($response->isRedirect()) {
             // Get the transaction ID (token) from the response
            $token = $response->getTransactionReference();

            $this->payment->savePayment([
                'type' => 'paypal',
                'token' => $token,
                'amount' => $request->input('amount')
            ]);

            // Get the redirect URL
            $redirectUrl = $response->getRedirectUrl();
            return response()->json(['success' => true, 'redirect_url' => $redirectUrl]);
        } else {
            // Payment failed, get the error message
            $errorMessage = $response->getMessage();
            return response()->json(['success' => false, 'error' => $errorMessage]);
        }
    }

    /**
     * Execute a payment after user approval
     * @param Request $request The incoming request object
     * @return JsonResponse The JSON response indicating success or failure of the request
     */
    public function executePayment(Request $request)
    {
        $token = $request->input('token');

        // Get the payment record associated with the transaction ID
        $payment = $this->payment->getPayment(['token'=> $token]);

        if (!$payment) {
            return response()->json(['success' => false, 'error' => __('paymentgatewaymanagement::messages.payment.invalid_transaction')]);
        }

        $amount = $payment->amount;

        $response = $this->gateway->completePurchase([
            'payerId' => $request->input('PayerID'),
            'transactionReference' => $request->input('token'),
            'amount' => $amount,
        ])->send();

        if ($response->isSuccessful()) {
            // Payment was successful
            return response()->json(['success' => true, 'data' => $response->getData()]);
        } else {
            // Payment failed
            return response()->json(['success' => false, 'error' => $response->getMessage()]);
        }
    }

    /**
     * Handle a cancelled payment
     * @return JsonResponse The JSON response indicating that the payment was cancelled
     */
    public function cancelPayment()
    {
        // Payment is cancelled
        return response()->json(['success' => false, 'error' => __('paymentgatewaymanagement::messages.payment.cancelled')]);
    }

}
