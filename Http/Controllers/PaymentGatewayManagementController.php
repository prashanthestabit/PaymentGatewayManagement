<?php

namespace Modules\PaymentGatewayManagement\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Modules\PaymentGatewayManagement\Repositories\PaymentRepository;
use Nwidart\Modules\Facades\Module;

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

}
