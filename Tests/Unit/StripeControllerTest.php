<?php

namespace Modules\PaymentGatewayManagement\Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * This Class use for StripeController Testing
 * For Testing
 * php artisan test Modules/PaymentGatewayManagement/Tests/Unit/StripeControllerTest.php
 */
class StripeControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * A test Payment Store With Valid Token.
     *
     * @return void
     */
    public function testStorePaymentWithValidToken()
    {
        $token = $this->getToken();

        $response = $this->post(route('stripe.payment'), [
            'token' => $token,
            'amount' => 10,
            'payment_method' => [
                'type' => 'card',
                'card' => [
                    'number' => '4242424242424242',
                    'exp_month' => 8,
                    'exp_year' => 2023,
                    'cvc' => '314',
                ],
            ],
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'message'])
            ->assertJson([
                'status' => true,
                'message' => __('paymentgatewaymanagement::messages.payment.succeeded'),
            ]);

    }

    /**
     * A test Payment Store With Valid Token and Invalid Data.
     *
     * @return void
     */
    public function testStorePaymentWithInValidDataAndValidToken()
    {
        $token = $this->getToken();
        $response = $this->post(route('stripe.payment'), [
            'token' => $token,
            'amount' => 10,
            'payment_method' => [
                'type' => '123',
                'card' => [
                    'number' => '4242424242424242',
                    'exp_month' => 8,
                    'exp_year' => 2023,
                    'cvc' => '314',
                ],
            ],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'status' => false,
        ]);

    }

    /**
     * A test Payment Store With In Valid Token.
     *
     * @return void
     */
    public function testStorePaymentWithInValidToken()
    {

        $response = $this->post(route('stripe.payment'), [
            'token' => 'invalid_token',
            'amount' => 10,
            'payment_method' => [
                'type' => 'card',
                'card' => [
                    'number' => '4242424242424242',
                    'exp_month' => 8,
                    'exp_year' => 2023,
                    'cvc' => '314',
                ],
            ],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'status' => __('paymentgatewaymanagement::messages.invalid_token'),
        ]);

    }

    private function getToken()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        //Get JwtAuth Token
        return JWTAuth::fromUser($user);
    }
}
