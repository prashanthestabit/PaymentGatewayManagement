<?php

return [
    'name' => 'PaymentGatewayManagement',
    'paymentsPlatform' => [
        1 => 'stripe',
        2 => 'paypal'
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
                    ]
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
                                "request_three_d_secure" => "automatic"
                             ]
                          ],
                 "payment_method_types" => [
                                   "card"
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
                 "transfer_group" => null
              ]
           ],
        "livemode" => false,
        "pending_webhooks" => 2,
        "request" => [
                                      "id" => "req_XLbIvNsd3zqmCd",
                                      "idempotency_key" => "25d06d33-81f0-4e6f-94c7-a74044fa1a22"
                                   ],
        "type" => "payment_intent.succeeded"
     ]
];
