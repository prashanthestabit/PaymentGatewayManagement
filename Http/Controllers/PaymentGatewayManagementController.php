<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Modules\PaymentGatewayManagement\Repositories\PaymentRepository;
use Nwidart\Modules\Facades\Module;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Exceptions\PaymentActionRequired;
use Stripe\StripeClient;

class PaymentGatewayManagementController extends Controller
{
    protected $payment;

    public function __construct(PaymentRepository $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        try{
            $responseData = [
                'status' => true,
                'data' => Config::get('paymentgatewaymanagement.paymentsPlatform')
            ];
            return $this->payment->responseMessage($responseData,Response::HTTP_OK);
        }catch(Exception $e)
        {
            $responseData = [
                'status' => false,
                'message' => __('authwithjwt::messages.try_again'),
            ];
            return $this->payment->responseMessage($responseData,Response::HTTP_BAD_REQUEST);
        }
    }



    public function store(Request $request)
    {
       try{
            $user = $request->user();
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            $stripe = new StripeClient(env('STRIPE_SECRET'));

            // Create a new test payment method
            $paymentMethod = $stripe->paymentMethods->create([
                'type' => 'card',
                'card' => [
                    'number' => '4242424242424242',
                    'exp_month' => 8,
                    'exp_year' => 2023,
                    'cvc' => '314',
                ],
            ]);

            //$paymentMethod = $user->addPaymentMethod($request->payment_method);

            $user->updateDefaultPaymentMethod($paymentMethod->id);

            $stripeCharge = $user->charge(
                $request->amount * 100, $paymentMethod->id
            );

            // Payment succeeded
            $responseData = [
                'status' => true,
                'message' => __('authwithjwt::messages.payment.succeeded'),
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
                'message' => __('authwithjwt::messages.try_again'),
            ];
            return $this->payment->responseMessage($responseData,Response::HTTP_BAD_REQUEST);
        }
    }

}
