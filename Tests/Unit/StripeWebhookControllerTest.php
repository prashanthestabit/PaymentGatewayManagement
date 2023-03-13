<?php

namespace Modules\PaymentGatewayManagement\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Tests\TestCase;

/**
 * For Testing php artisan test Modules/PaymentGatewayManagement/Tests/Unit/StripeWebhookControllerTest.php
 */
class StripeWebhookControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test the handleWebhook method of the StripeWebhookController.
     *
     * @return void
     */
    public function testHandleStripeWebhookWithValidDataAndStripeWebhookController()
    {
        // Mock the Stripe event
        $event = Config::get('paymentgatewaymanagement.stripeContent');
        // Create a new request with the Stripe event as content
        $request = Request::create('/', 'POST', [], [], [], [], json_encode($event));

        // Use the WebhookController directly
        $controller = new WebhookController;

        $response = $controller->handleWebhook($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Test the handleWebhook method with valid data and not use the StripeWebhookController.
     *
     * @return void
     */
    public function testHandleStripeWebhookWithInvalidData()
    {
        $response = $this->post(route('stripe.webhook'), $event=array())
                         ->assertStatus(Response::HTTP_OK);
    }


}
