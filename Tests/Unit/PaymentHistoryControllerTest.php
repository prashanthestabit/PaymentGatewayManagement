<?php

namespace Modules\PaymentGatewayManagement\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Modules\PaymentGatewayManagement\Entities\Transaction;

class PaymentHistoryControllerTest extends TestCase
{
    use WithFaker,RefreshDatabase;

    protected $listDataStructure = [
        'data',
        'current_page',
        'first_page_url',
        'from',
        'last_page',
        'last_page_url',
        'next_page_url',
        'path',
        'per_page',
        'prev_page_url',
        'to',
        'total',
    ];

    /**
     * Test the getPaymentHistory method.
     *
     * @return void
     */
    public function testGetPaymentHistoryWithValidToken()
    {
        $token = $this->getToken();

        Transaction::factory()->count(500)->create();

        $response = $this->postJson(route('payments.history'), [
            'token' => $token,
            'page' => 1,
            'payment_type' => 'paypal',
            'per_page' => 10,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'status',
            'data' => $this->listDataStructure,
        ]);
    }

        /**
     * Test the getPaymentHistory method.
     *
     * @return void
     */
    public function testGetPaymentHistoryWithInvalidToken()
    {
        Transaction::factory()->count(500)->create();

        $response = $this->postJson(route('payments.history'), [
            'token' => 'invalid-token',
            'page' => 1,
            'payment_type' => 'paypal',
            'per_page' => 10,
        ]);

        // Assert that the response has a HTTP status code of 500 (Internal Server Error)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
        ]);
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
