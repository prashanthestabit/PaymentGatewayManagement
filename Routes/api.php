<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\PaymentGatewayManagement\Http\Controllers\ApiController;
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
    Route::post('{payment_type}/process-transaction',[ApiController::class,'processTransaction']);

    Route::get('{payment_type}/transaction-sucess/{id}',[ApiController::class,'sucessTransaction']);

    Route::get('transaction/{id}',[ApiController::class,'transactionById']);
});
