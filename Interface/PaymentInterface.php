<?php

namespace Modules\PaymentGatewayManagement\Interface;

interface PaymentInterface
{
    public function responseMessage($responseData, $statusCode);
}
