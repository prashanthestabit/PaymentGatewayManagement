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
            $table->integer('type')->nullable()->comment('1 => Stripe, 2 => Paypal');
            $table->unsignedBigInteger('user_id');
            $table->string('order_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_id');
            $table->double('amount',8,2);
            $table->string('currency');
            $table->string('status');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
