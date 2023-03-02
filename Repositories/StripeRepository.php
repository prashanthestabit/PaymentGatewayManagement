<?php

namespace Modules\PaymentGatewayManagement\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Request;

/* Class StripeRepository.
 * This class is responsible for handling stripe operations related.
 */
class StripeRepository
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
