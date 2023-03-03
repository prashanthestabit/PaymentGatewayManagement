<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\PaymentGatewayManagement\Http\Controllers\ApiController;
use Modules\PaymentGatewayManagement\Http\Controllers\PaymentGatewayManagementController;
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

Route::post('paypal/webhook', [ PaypalController::class,'handleWebhook']);

Route::group(['middleware' => ['jwt.verify']], function() {

   // Route::post('{payment_type}/process-transaction',[ApiController::class,'processTransaction']);

    //Route::get('{payment_type}/transaction-sucess/{id}',[ApiController::class,'sucessTransaction']);

    //Route::get('transaction/{id}',[ApiController::class,'transactionById']);

    /**
     * Get List of Payment Gateways
     */
    Route::get('payment-gateways',[PaymentGatewayManagementController::class,'index']);

    Route::post('stripe/payment', [ PaymentGatewayManagementController::class,'store']);

    Route::post('paypal/payment', [ PaypalController::class,'store']);
});
