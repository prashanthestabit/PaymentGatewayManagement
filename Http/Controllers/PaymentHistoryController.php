<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\PaymentGatewayManagement\Repositories\PaymentRepository;

class PaymentHistoryController extends Controller
{
    protected $payment;

    public function __construct(PaymentRepository $payment)
    {
        $this->payment = $payment;
    }

    public function getPaymentHistory(Request $request)
    {
        try {
            $transactions = $this->payment->getTransaction($request);

            return response()->json([
                'status' => true,
                'data' => $transactions,
            ]);
        } catch (Exception $e) {
            Log::error("message : " . $e->getMessage());
            $responseData = [
                'status' => false,
                'message' => __('paymentgatewaymanagement::messages.try_again'),
            ];
            return $this->payment->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
        }

    }
}
