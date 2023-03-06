<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\PaymentGatewayManagement\Http\Controllers\ApiController;
use Modules\PaymentGatewayManagement\Http\Controllers\BraintreeController;
use Modules\PaymentGatewayManagement\Http\Controllers\PaymentGatewayManagementController;
use Modules\PaymentGatewayManagement\Http\Controllers\PaymentHistoryController;
use Modules\PaymentGatewayManagement\Http\Controllers\PaypalController;
use Modules\PaymentGatewayManagement\Http\Controllers\StripeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::group(['middleware' => ['jwt.verify']], function() {
    /**
     * Get List of Payment Gateways
     */
    Route::get('payment-gateways',[PaymentGatewayManagementController::class,'index']);

    Route::post('stripe/payment', [ StripeController::class,'store'])->name('stripe.payment');

    Route::post('paypal/payment', [ PayPalController::class,'store'])->name('paypal.payment');

    Route::get('payments/history', [ PaymentHistoryController::class,'getPaymentHistory']);

});
