<?php

namespace Modules\PaymentGatewayManagement\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaypalWebhookControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test the handleWebhook method of the PaypalWebhookController.
     *
     * @return void
     */
    public function testHandlePaypalWebhookWithValidData()
    {
        $event = Config::get('paymentgatewaymanagement.paypalContent');

        // Create a new request with the Paypal event as content
        $request = Request::create('/', 'POST', [], [], [], [], json_encode($event));

        $response = $this->post(route('paypal.webhook'), [
            'request' => $request
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }


    /**
     * Test the handleWebhook method with In valid data.
     *
     * @return void
     */
    public function testHandlePaypalWebhookWithInvalidData()
    {
        $response = $this->post(route('paypal.webhook'), $event=array())
                         ->assertStatus(Response::HTTP_OK);
    }

}
