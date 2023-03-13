<?php

namespace Modules\PaymentGatewayManagement\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Request;
use Modules\PaymentGatewayManagement\Entities\Transaction;
use Modules\PaymentGatewayManagement\Entities\Payment;
use Modules\PaymentGatewayManagement\Interface\PaymentInterface;

/* Class StripeRepository.
 * This class is responsible for handling stripe operations related.
 */
class PaymentRepository implements PaymentInterface
{
    /**
     * Generate a response with the given status, message, data and status code.
     *
     * @param array $responseData
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseMessage($responseData, $statusCode)
    {
        return response()->json($responseData, $statusCode);
    }


    public function store($data)
    {
        return Transaction::create($data);
    }

    public function savePayment($data)
    {
        return Payment::create($data);
    }

    public function getPayment($condition)
    {
        return Payment::where($condition)->first();
    }

    public function getTransaction($request)
    {
        $transaction = Transaction::query()
                ->when($request->input('payment_type'), function ($query, $paymentType) {
                    return $query->where('type', $paymentType);
                })
                ->when($request->input('status'), function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($request->input('amount'), function ($query, $amount) {
                    return $query->where('amount', $amount);
                })
                ->when($request->input('payment_id'), function ($query, $paymentId) {
                    return $query->where('payment_id', $paymentId);
                })
                ->when($request->input('transaction_id'), function ($query, $transactionId) {
                    return $query->where('transaction_id', $transactionId);
                })
                ->when($request->input('from_date') && $request->input('to_date'), function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')]);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($request->input('per_page'));

            return $transaction;
    }
}
