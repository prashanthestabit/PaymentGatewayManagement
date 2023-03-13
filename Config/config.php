<?php

return [
    'name' => 'PaymentGatewayManagement',
    'paymentsPlatform' => [
        1 => 'stripe',
        2 => 'paypal',
    ],
    'stripeContent' => [
        "id" => "evt_3Mhw3WGS2JV1gkf60ZDWn9iM",
        "object" => "event",
        "api_version" => "2022-11-15",
        "created" => 1677940047,
        "data" => [
            "object" => [
                "id" => "pi_3Mhw3WGS2JV1gkf60TbQ8rMl",
                "object" => "payment_intent",
                "amount" => 12300,
                "amount_capturable" => 0,
                "amount_details" => [
                    "tip" => [
                    ],
                ],
                "amount_received" => 12300,
                "application" => null,
                "application_fee_amount" => null,
                "automatic_payment_methods" => null,
                "canceled_at" => null,
                "cancellation_reason" => null,
                "capture_method" => "automatic",
                "client_secret" => "pi_3Mhw3WGS2JV1gkf60TbQ8rMl_secret_hbwTf4vEtfnltuJFVgDM87G41",
                "confirmation_method" => "automatic",
                "created" => 1677940046,
                "currency" => "usd",
                "customer" => "cus_NSqFNll4wkfZft",
                "description" => null,
                "invoice" => null,
                "last_payment_error" => null,
                "latest_charge" => "ch_3Mhw3WGS2JV1gkf60J5dO5a1",
                "livemode" => false,
                "metadata" => [
                ],
                "next_action" => null,
                "on_behalf_of" => null,
                "payment_method" => "pm_1Mhw3TGS2JV1gkf6efgfFOvU",
                "payment_method_options" => [
                    "card" => [
                        "installments" => null,
                        "mandate_options" => null,
                        "network" => null,
                        "request_three_d_secure" => "automatic",
                    ],
                ],
                "payment_method_types" => [
                    "card",
                ],
                "processing" => null,
                "receipt_email" => null,
                "review" => null,
                "setup_future_usage" => null,
                "shipping" => null,
                "source" => null,
                "statement_descriptor" => null,
                "statement_descriptor_suffix" => null,
                "status" => "succeeded",
                "transfer_data" => null,
                "transfer_group" => null,
            ],
        ],
        "livemode" => false,
        "pending_webhooks" => 2,
        "request" => [
            "id" => "req_XLbIvNsd3zqmCd",
            "idempotency_key" => "25d06d33-81f0-4e6f-94c7-a74044fa1a22",
        ],
        "type" => "payment_intent.succeeded",
    ],
    'paypalContent' => [
        "id" => "WH-7Y7254563A4550640-11V2185806837105M",
        "event_version" => "1.0",
        "create_time" => "2015-02-17T18:51:33Z",
        "resource_type" => "capture",
        "resource_version" => "2.0",
        "event_type" => "PAYMENT.CAPTURE.COMPLETED",
        "summary" => "Payment completed for $ 57.0 USD",
        "resource" => [
            "id" => "42311647XV020574X",
            "amount" => [
                "currency_code" => "USD",
                "value" => "57.00",
            ],
            "final_capture" => true,
            "seller_protection" => [
                "status" => "ELIGIBLE",
                "dispute_categories" => [
                    "ITEM_NOT_RECEIVED",
                    "UNAUTHORIZED_TRANSACTION",
                ],
            ],
            "disbursement_mode" => "DELAYED",
            "seller_receivable_breakdown" => [
                "gross_amount" => [
                    "currency_code" => "USD",
                    "value" => "57.00",
                ],
                "paypal_fee" => [
                    "currency_code" => "USD",
                    "value" => "2.48",
                ],
                "platform_fees" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => "5.13",
                        ],
                        "payee" => [
                            "merchant_id" => "CDD7K6247RPCC",
                        ],
                    ],
                ],
                "net_amount" => [
                    "currency_code" => "USD",
                    "value" => "49.39",
                ],
            ],
            "invoice_id" => "3942619:fdv09c49-a3g6-4cbf-1358-f6d241dacea2",
            "custom_id" => "d93e4fcb-d3af-137c-82fe-1a8101f1ad11",
            "status" => "COMPLETED",
            "supplementary_data" => [
                "related_ids" => [
                    "order_id" => "8U481631H66031715",
                ],
            ],
            "create_time" => "2022-08-26T18:29:50Z",
            "update_time" => "2022-08-26T18:29:50Z",
            "links" => [
                [
                    "href" => "https://api.paypal.com/v2/payments/captures/0KG12345VG343800K",
                    "rel" => "self",
                    "method" => "GET",
                ],
                [
                    "href" => "https://api.paypal.com/v2/payments/captures/0KG12345VG343880K/refund",
                    "rel" => "refund",
                    "method" => "POST",
                ],
                [
                    "href" => "https://api.paypal.com/v2/checkout/orders/8U481631H66031715",
                    "rel" => "up",
                    "method" => "GET",
                ],
            ],
        ],
        "links" => [
            [
                "href" => "https://api.paypal.com/v1/notifications/webhooks-events/WH-7Y7254563A4550640-11V2185806837105M",
                "rel" => "self",
                "method" => "GET",
            ],
            [
                "href" => "https://api.paypal.com/v1/notifications/webhooks-events/WH-7Y7254563A4550640-11V2185806837105M/resend",
                "rel" => "resend",
                "method" => "POST",
            ],
        ],
    ],
];
