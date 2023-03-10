<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('user_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('transaction_id');
            $table->string('payment_id')->nullable();
            $table->integer('amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('status');
            $table->string('description')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('card_last_four')->nullable();
            $table->text('updated_fields')->nullable();
            $table->string('card_brand')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('failure_code')->nullable();
            $table->string('failure_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
