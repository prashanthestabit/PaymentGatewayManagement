<?php

namespace Modules\PaymentGatewayManagement\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Request;
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
}
