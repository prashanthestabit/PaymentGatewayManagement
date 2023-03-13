<?php

namespace Modules\PaymentGatewayManagement\Interface;

interface PaymentInterface
{
    public function responseMessage($responseData, $statusCode);

    public function store($data);

    public function savePayment($data);

    public function getPayment($condition);
}
