<?php

namespace Modules\PaymentGatewayManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', //Payment Gateway Type
        'user_id',
        'order_id',
        'transaction_id',
        'payment_id',
        'amount',
        'currency',
        'status'
    ];

    protected static function newFactory()
    {
        return \Modules\PaymentGatewayManagement\Database\factories\TransactionFactory::new();
    }
}
