<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PaymentGatewayManagement\Entities\Transaction;

class PaymentHistoryController extends Controller
{
    public function getPaymentHistory(Request $request)
    {
        $page = $request->input('page');

        $transactions = Transaction::query()
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


        return response()->json(['data' => $transactions]);
    }

}
