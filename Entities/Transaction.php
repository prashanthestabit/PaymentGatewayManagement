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
        'status',
        'description',
        'customer_id',
        'card_last_four',
        'card_brand',
        'refunded_at',
        'failure_code',
        'failure_message',
        'metadata',
        'created_at'
    ];


    protected static function newFactory()
    {
        return \Modules\PaymentGatewayManagement\Database\factories\TransactionFactory::new();
    }
}
