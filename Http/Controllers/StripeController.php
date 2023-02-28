<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe;
use Illuminate\Support\Str;
use Modules\PaymentGatewayManagement\Entities\Transaction;

class StripeController extends Controller
{

    const BASE_URL = 'https://api.stripe.com';

    public static function stripeTransaction($input)
    {
        try{
            $input['transaction_id'] = Str::random(18);
            $payment_url = self::BASE_URL.'/v1/payment_methods';
            $payment_data = [
                'type' => 'card',
                'card[number]' => $input['card_no'],
                'card[exp_month]' => $input['exp_month'],
                'card[exp_year]' => $input['exp_year'],
                'card[cvc]' => $input['cvc'],
                'billing_details[email]' => Auth::user()->email,
                'billing_details[name]' => Auth::user()->name,
                'billing_details[phone]' => "8130185366",
            ];

            $payment_payload = http_build_query($payment_data);

            $payment_headers = [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer '.env('STRIPE_SECRET')
            ];

            $payment_body = self::curlPost($payment_url, $payment_payload, $payment_headers);

            $payment_response = json_decode($payment_body, true);

            if (isset($payment_response['id']) && $payment_response['id'] != null) {

                $request_url = self::BASE_URL.'/v1/payment_intents';

                $request_data = [
                    'amount' => $input['amount'] * 100, // multiply amount with 100
                    'currency' => $input['currency'],
                    'payment_method_types[]' => 'card',
                    'payment_method' => $payment_response['id'],
                    'confirm' => 'true',
                    'capture_method' => 'automatic',
                    'return_url' => route('paymentSuccess', $input['transaction_id']),
                    'payment_method_options[card][request_three_d_secure]' => 'automatic',
                ];

                $request_payload = http_build_query($request_data);

                $request_headers = [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer '.env('STRIPE_SECRET')
                ];

                // another curl request
                $response_body = self::curlPost($request_url, $request_payload, $request_headers);

                $response_data = json_decode($response_body, true);

                $transaction = Transaction::create([
                    'type'           => 1,
                    'user_id'        => Auth::user()->id,
                    'order_id'       => rand(1,999999),
                    'transaction_id' => $input['transaction_id'],
                    'payment_id'     => $response_data['id'],
                    'amount'         => $input['amount'],
                    'currency'       => $input['currency'],
                ]);
                // transaction required 3d secure redirect
                if (isset($response_data['next_action']['redirect_to_url']['url']) && $response_data['next_action']['redirect_to_url']['url'] != null) {

                    return response()->json([
                        'status'  => true,
                        'message' => '3D Secure Link',
                        'data'    => $response_data['next_action']['redirect_to_url']['url']

                    ]);

                // transaction success without 3d secure redirect
                } elseif (isset($response_data['status']) && $response_data['status'] == 'succeeded') {
                    Transaction::find($transaction->id)->update(['status' => 2]);
                    $url = route('paymentSuccess',$input['transaction_id']);
                    return response()->json([
                        'status'  => true,
                        'message' => 'Payment Success',
                        'link'    => $url
                    ]);

                // transaction declined because of error
                } elseif (isset($response_data['error']['message']) && $response_data['error']['message'] != null) {
                    Transaction::find($transaction->id)->update(['status' => 3]);
                    return response()->json([
                        'status'  => false,
                        'message' => $response_data['error']['message']
                    ]);
                } else {
                    Transaction::find($transaction->id)->update(['status' => 3]);
                    return response()->json([
                        'status'  => false,
                        'message' => 'Something went wrong, please try again.'
                    ]);
                }
            // error in creating payment method
            } elseif (isset($payment_response['error']['message']) && $payment_response['error']['message'] != null) {
                return response()->json([
                    'status'  => false,
                    'message' => $payment_response['error']['message']
                ]);
            }
        }catch(Exception $e){
            Log::error($e);
            return response()->json([
                'status' => false,
                'message' => "Something went Wrong"
            ],Response::HTTP_GATEWAY_TIMEOUT);
        }
    }

    private function curlPost($url, $data, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close ($ch);

        return $response;
    }

    public static function paymentSuccess($request_data,$transaction_id){
        $input = Transaction::where('transaction_id',$transaction_id)->first();
        // if only stripe response contains payment_intent
        if (isset($request_data['payment_intent']) && $request_data['payment_intent'] != null) {

            // here we will check status of the transaction with payment_intents from stripe server
            $get_url = self::BASE_URL.'/v1/payment_intents/'.$request_data['payment_intent'];

            $get_headers = [
                'Authorization: Bearer '.env('STRIPE_SECRET')
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $get_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $get_headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $get_response = curl_exec($ch);

            curl_close ($ch);

            $get_data = json_decode($get_response, 1);

            // succeeded means transaction success
            if (isset($get_data['status']) && $get_data['status'] == 'succeeded') {
                $input->status = 2;
                $input->update();
                return response()->json([
                    'status'  => true,
                    'message' => 'Payment Successfull',
                ],Response::HTTP_OK);

            } elseif (isset($get_data['error']['message']) && $get_data['error']['message'] != null) {
                $input->status = 3;
                $input->update();
                return response()->json([
                    'status'  => false,
                    'message' => $get_data['error']['message'],
                ],Response::HTTP_BAD_REQUEST);

            } else {
                $input->status = 3;
                $input->update();
                return response()->json([
                    'status'  => false,
                    'message' => 'Payment request failed',
                ],Response::HTTP_BAD_GATEWAY);
            }
        } else {
                $input->status = 4;
                $input->update();
                return response()->json([
                    'status'  => false,
                    'message' => 'Payment request failed',
                ],Response::HTTP_BAD_GATEWAY);
        }
    }
}
