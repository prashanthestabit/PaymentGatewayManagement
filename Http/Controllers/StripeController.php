<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\PaymentGatewayManagement\Repositories\PaymentRepository;
use Laravel\Cashier\Exceptions\PaymentActionRequired;
use Modules\PaymentGatewayManagement\Http\Requests\Stripe\PaymentStoreRequest;
use Stripe\StripeClient;

class StripeController extends Controller
{
    protected $payment;

    public function __construct(PaymentRepository $payment)
    {
        $this->payment = $payment;
    }


    public function store(PaymentStoreRequest $request)
    {
       try{
            $user = $request->user();
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            $stripe = new StripeClient(config('cashier.secret'));

            $paymentMethod = $stripe->paymentMethods->create($request->payment_method);

            $user->updateDefaultPaymentMethod($paymentMethod->id);

            $user->charge(
                $request->amount * 100, $paymentMethod->id
            );

            // Payment succeeded
            $responseData = [
                'status' => true,
                'message' => __('paymentgatewaymanagement::messages.payment.succeeded'),
            ];
            return $this->payment->responseMessage($responseData,Response::HTTP_OK);

        } catch (PaymentActionRequired $e) {
           // Return the checkout URL as a JSON response
            return response()->json(['checkout_url' => $e->payment->getCheckoutUrl()], 400);
        } catch (Exception $e) {
            // Payment failed
            Log::error("message: ". $e->getMessage());
            $responseData = [
                'status' => false,
                'message' => __('paymentgatewaymanagement::messages.try_again'),
            ];
            return $this->payment->responseMessage($responseData,Response::HTTP_BAD_REQUEST);
        }
    }
}
