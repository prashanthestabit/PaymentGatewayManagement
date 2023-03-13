<?php

namespace Modules\PaymentGatewayManagement\Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Response;

class PaypalControllerTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    public function testCreatePaymentWithValidRequest()
    {
        $token = $this->getToken();

        $response = $this->post(route('paypal.create-payment'), [
            'token' => $token,
            'amount' => 10
        ]);

        $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure(['success', 'redirect_url'])
        ->assertJson([
            'success' => true,

        ]);
    }

    public function testCreatePaymentWithInvalidRequest()
    {
        $token = $this->getToken();

        $response = $this->post(route('paypal.create-payment'), [
            'token' => $token,
            'amount' => 'invalid-amount'
        ]);

        $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure(['message', 'status'])
        ->assertJson([
            'status' => false,

        ]);
    }

    public function testCreatePaymentWithvalidRequestAndInvalidToken()
    {

        $response = $this->post(route('paypal.create-payment'), [
            'token' => 'invalid_token',
            'amount' => 10
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
