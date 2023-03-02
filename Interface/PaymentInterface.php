<?php

namespace Modules\AuthWithJWT\Interface;

interface PaymentInterface
{
    public function responseMessage($responseData, $statusCode);
}
