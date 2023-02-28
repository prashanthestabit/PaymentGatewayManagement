<?php
use Illuminate\Support\Facades\Route;
use Modules\PaymentGatewayManagement\Http\Controllers\ApiController;
use Modules\PaymentGatewayManagement\Http\Controllers\RazorpayController;
use Modules\PaymentGatewayManagement\Http\Controllers\StripeController;

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
Route::get('payment-success/{id}',[StripeController::class,'paymentSuccess'])->name('paymentSuccess');
// Route::post('payment-verify',[RazorpayController::class,'razorpayTransaction'])->name('payment.verify');
