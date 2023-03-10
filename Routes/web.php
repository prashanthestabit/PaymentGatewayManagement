<?php
use Illuminate\Support\Facades\Route;
use Modules\PaymentGatewayManagement\Http\Controllers\BraintreeController;
use Modules\PaymentGatewayManagement\Http\Controllers\PaypalController;
use Modules\PaymentGatewayManagement\Http\Controllers\PaypalWebhookController;
use Modules\PaymentGatewayManagement\Http\Controllers\StripeWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('braintree/webhook', [ BraintreeController::class,'handleWebhook'])->name('braintree.webhook');

Route::post('stripe/webhook', [ StripeWebhookController::class,'handleWebhook'])->name('stripe.webhook');


Route::post('paypal/webhook', [ PaypalWebhookController::class,'handleWebhook'])->name('paypal.webhook');
