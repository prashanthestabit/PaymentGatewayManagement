<?php

namespace Modules\PaymentGatewayManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'token',
        'amount'
    ];

    protected static function newFactory()
    {
        return \Modules\PaymentGatewayManagement\Database\factories\PaymentFactory::new();
    }
}
