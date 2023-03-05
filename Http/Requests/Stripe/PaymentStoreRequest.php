<?php

namespace Modules\PaymentGatewayManagement\Http\Requests\Stripe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;

class PaymentStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required|string',
            'payment_method' => 'required|array',
            'payment_method.type' => 'required|in:card',
            'payment_method.card' => 'required|array',
            'payment_method.card.number' => 'required|string',
            'payment_method.card.exp_month' => 'required|numeric',
            'payment_method.card.exp_year' => 'required|numeric',
            'payment_method.card.cvc' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors()
        ]),Response::HTTP_OK);
    }
}
