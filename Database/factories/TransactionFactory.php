<?php

namespace Modules\PaymentGatewayManagement\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\PaymentGatewayManagement\Entities\Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['paypal', 'stripe']),
            'user_id' => $this->faker->numberBetween(1, 10),
            'order_id' => $this->faker->numberBetween(1, 100),
            'transaction_id' => $this->faker->uuid(),
            'payment_id' => $this->faker->uuid(),
            'amount' => $this->faker->randomFloat(2, 10, 100),
            'currency' => $this->faker->currencyCode(),
            'status' => $this->faker->randomElement(['success', 'failure', 'pending']),
            'description' => $this->faker->sentence(),
            'customer_id' => $this->faker->uuid(),
            'card_last_four' => $this->faker->creditCardNumber(),
            'card_brand' => $this->faker->creditCardType(),
            'refunded_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'failure_code' => $this->faker->randomNumber(),
            'failure_message' => $this->faker->sentence(),
            'metadata' => json_encode(Config::get('paymentgatewaymanagement.paypalContent')),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}

