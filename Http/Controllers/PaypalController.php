<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Facades\PayPal;


/**
 * 1. composer require "paypal/rest-api-sdk-php:*"
 * 2. php artisan module:make-provider PayPalServiceProvider PaymentGatewayManagement
 */
class PaypalController extends Controller
{

private $apiContext;

public function __construct()
{
    $this->apiContext = new ApiContext(
        new OAuthTokenCredential(
            env('PAYPAL_CLIENT_ID'),
            env('PAYPAL_SECRET')
        )
    );
}


public function createPayment(Request $request)
{

    $accessToken = $this->apiContext->getCredential()->getAccessToken(
        [
            'mode' => 'sandbox', // Set the mode to 'live' or 'sandbox'
        ]
    );

        if (!$this->apiContext->getCredential() || !$accessToken) {
            echo 'Invalid API context: missing credentials';
        } else {
            echo 'API context is valid';
        }

    // Get the total amount of the payment
    $amount = $request->input('amount');

    // Create a Payer object
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');


    // Create an Item object
    $item = new Item();
    $item->setName('Product')
        ->setCurrency('USD')
        ->setQuantity(1)
        ->setSku("123123") // Similar to `item_number` in Classic API
        ->setPrice($amount);

    // Create an ItemList object
    $itemList = new ItemList();
    $itemList->setItems([$item]);

    // Create a Details object
    $details = new Details();
    $details->setSubtotal($amount);

    // Create an Amount object
    $totalAmount = new Amount();
    $totalAmount->setCurrency('USD')
        ->setTotal($amount)
        ->setDetails($details);

    // Create a Transaction object
    $transaction = new Transaction();
    $transaction->setAmount($totalAmount)
        ->setItemList($itemList)
        ->setDescription('Product description')
        ->setInvoiceNumber(uniqid());

    // Create a RedirectUrls object
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl(route('paypal.executePayment'))
                ->setCancelUrl(route('paypal.cancelPayment'));

    // Create a Payment object
    $payment = new Payment();
    $payment->setIntent('sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions([$transaction]);

    $request = clone $payment;


    try {
        $payment->create($this->apiContext);
        echo $payment;

        echo "\n\nRedirect user to approval_url: " . $payment->getApprovalLink() . "\n";
    }
    catch (\PayPal\Exception\PayPalConnectionException $ex) {
        // This will print the detailed information on the exception.
        //REALLY HELPFUL FOR DEBUGGING
        echo $ex->getData();
    }

    dd($this->apiContext);
    try {
        $payment->create($this->apiContext);
    } catch (Exception $ex) {
        Log::info("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $request, $ex);
    exit(1);
    }
    // Create the payment using the API context

    dump($payment);
    dd($payment->getApprovalLink()); die;
}

public function executePayment()
{
    $apiContext = app('PayPal\Client');

    $paymentId = request('paymentId');
    $payerId = request('PayerID');

    $payment = Payment::get($paymentId, $apiContext);

    $execution = new PaymentExecution();
    $execution->setPayerId($payerId);

    try {
        $result = $payment->execute($execution, $apiContext);
        // Payment is successful, do something here
    } catch (Exception $ex) {
        return '';
    }
}

public function cancelPayment()
{
    // Payment is cancelled, do something here
}

}
