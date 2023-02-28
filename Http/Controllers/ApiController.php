<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Razorpay\Api\Api;

class ApiController extends Controller
{
    const PENDING   = 1;
    const INPROCESS = 2;
    const COMPLETED = 3;
    const FAILED    = 4;



    public function paymentVerify(Request $request){
        dd($request->all());
    }

    public function processTransaction(Request $request,$payment_type)
    {
        $aResponse = [];
        try{
            switch($payment_type){
                case "stripe" :
                    $aResponse = StripeController::stripeTransaction($request->all());
                    break;
                case "paypal" :
                    $aResponse = [];
                    break;
            }
            return $aResponse;
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }

    public function sucessTransaction(Request $request,$payment_type,$transaction_id = null){
        $aResponse = [];
        try{
            switch($payment_type){
                case "stripe" :
                    $aResponse = StripeController::paymentSuccess($request->all(),$transaction_id);
                    break;
            }
            return $aResponse;
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }

    public function transactionById()
    {
    }
}
