<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\PaymentGatewayManagement\Services\PaypalPaymentService;

class PaypalWebhookController extends Controller
{

    protected $paymentService;

    /**
     * Service Provider Registered in PaymentGatewayManagementServiceProvider
     * Hendle Paypal event and store in database Transaction table
     */
    public function __construct(PaypalPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
    *
    * Handle incoming PayPal webhook requests
    *
    * @param Request $request The incoming request object
    *
    * @return JsonResponse The JSON response indicating success or failure of the request
    *
    * @throws \Exception If an error occurs while processing the webhook
    */
    public function handleWebhook(Request $request)
    {
        try{
            $payload = $request->getContent();

            Log:info($payload);

            $event = json_decode($payload, true);

            $this->paymentService->handleEvent($event);

            return response()->json(['success' => true]);

        }catch(Exception $e)
        {
            Log::error($e->getMessage());
            return response()->json(['success' => false]);
        }
    }
}
