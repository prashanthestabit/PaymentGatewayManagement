# Payment Gateway Management Module

This module provides one-time payment functionality with Stripe and PayPal in Laravel applications. It is pre-built and ready to use, and can be easily modified to suit your specific requirements.

## Features

1. One-time payment functionality with Stripe and PayPal
2. Stripe payments using Laravel Cashier
3. Handling of webhooks for Stripe and PayPal payments
4. Creation of filter API for both payment gateways

## Installation

This module has been built using the nwidart/laravel-modules package, so make sure you have this package installed in your project. For installation, please refer to the nwidart/laravel-modules [documentation](https://nwidart.com/laravel-modules/v6/installation-and-setup).

Once you have installed the nwidart/laravel-modules package, you can simply clone this repository and copy the PaymentGateway module to the <b>Modules</b> directory of your Laravel application.

### Setting up Stripe with Laravel Cashier[documentation](https://laravel.com/docs/10.x/billing)

Laravel Cashier provides a simple way to integrate Stripe into your Laravel application. In this guide, we'll walk you through the steps to set up Stripe with Laravel Cashier.

### Prerequisites

Before we get started, you'll need to make sure that you have the following prerequisites in place:

1. A Stripe account (if you don't have one, you can sign up for free at stripe.com)

### Step 1: Install Laravel Cashier

 ``` bash
  composer require laravel/cashier
 ```

 ``` bash
   php artisan migrate
 ```

  ``` bash
  php artisan vendor:publish --tag="cashier-config"
  ```

 This will install Laravel Cashier and all of its dependencies.

 ### Step 2: Configure Your Stripe API Keys

 Make sure to replace STRIPE_KEY, STRIPE_SECRET, and STRIPE_WEBHOOK_SECRET with your actual Stripe API keys. 
 in .env file. You can find your API keys in your Stripe dashboard.

 ### step 3: The User model setup

 First, let's set up our first billable model, User with Cashier. Add Billable trait to our first billable model which at App\Models\User.

  ``` bash
    use Laravel\Cashier\Billable;

    class User extends Authenticatable
    {
        use Billable;
    }
  ```

  ### Setting up Paypal with Laravel Omnipay/Omnipay [documentation](https://omnipay.thephpleague.com/)

  Laravel Omnipay is a payment processing library that provides a unified API for various payment gateways, including PayPal. In this guide, we'll walk you through the steps to set up PayPal with Laravel Omnipay.

  ### Prerequisites

  Before we get started, you'll need to make sure that you have the following prerequisites in place:

  1. A PayPal account (if you don't have one, you can [sign up](https://developer.paypal.com/home) for free at paypal.com)
  2. The Laravel Omnipay package installed in your Laravel application (if you don't have it installed, you can install it using Composer: composer require omnipay/paypal)

  ### Step 1: Configure Your PayPal API Credentials

  To configure your PayPal API credentials, you'll need to add the following lines to your .env file:

``` bash
        PAYPAL_USERNAME = your_paypal_api_username
        PAYPAL_PASSWORD = your_paypal_api_password
        PAYPAL_SIGNATURE = your_paypal_api_signature
        PAYPAL_SANDBOX = Set true or false
 ```

 Make sure to replace your_paypal_api_username, your_paypal_api_password, and your_paypal_api_signature with your actual PayPal API credentials. You can find your API credentials in your PayPal dashboard. Set PAYPAL_SANDBOX to true to enable testing mode.


### EndPoints

#### 1. Get List of payments gateways

```bash
URL:- /api/payment-gateways

Method:- GET
```
Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |

#### 2. Store Stripe payments

```bash
URL:- /api/stripe/payment

Method:- Post
```
Request Body:- 

| Field Name                    | Data Type | Required | Description                                                                                                        |
|-------------------------------|-----------|----------|--------------------------------------------------------------------------------------------------------------------|
| token                         | string    | Yes      | A unique identifier representing the payment authorization for the user's account                                  |
| payment_method                | array     | Yes      | An array of payment methods that the user can select to complete the payment transaction                           |
| payment_method.type           | string    | Yes      | The type of payment method that the user has selected, e.g., credit card, debit card, PayPal, etc.                 |
| payment_method.card           | array     | Yes      | An array containing the user's credit card information                                                             |
| payment_method.card.number    | string    | Yes      | The 16-digit credit card number                                                                                    |
| payment_method.card.exp_month | number    | Yes      | The expiration month of the credit card, represented as a two-digit number (e.g., 01 for January, 12 for December) |
| payment_method.card.exp_year  | number    | Yes      | The expiration year of the credit card, represented as a four-digit number                                         |
| payment_method.card.cvc       | string    | Yes      | The three-digit Card Verification Code (CVC) printed on the back of the credit card                                |
| amount                        | number    | Yes      | The total amount of the payment transaction, in the currency specified in the user's account settings              |



#### 3. Create payment Paypal 

```bash
URL:- /api/paypal/create-payment

Method:- Post
```
Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |
|     amount          |     number         |       Yes           |The total amount of the payment |

#### 4. get payment reports

```bash
URL:- /api/payments/history

Method:- Post
```
Request Body:-

| Field Name     | Data Type | Required | Description                                                                                                   |
|----------------|-----------|----------|---------------------------------------------------------------------------------------------------------------|
| token          | string    | Yes      | A unique identifier representing the payment authorization for the user's account                             |
| per_page       | number    | No       | The number of payment transactions to include in a single page of results, default is 25                      |
| page           | number    | No       | The page number of payment transactions to retrieve, default is 1                                             |
| status         | string    | No       | The status of the payment transaction, e.g., "approved", "pending", "failed", etc.                            |
| amount         | number    | No       | The total amount of the payment transaction, in the currency specified in the user's account settings         |
| payment_id     | string    | No       | The unique identifier for the payment transaction, assigned by the payment gateway                            |
| transaction_id | string    | No       | The unique identifier for the payment transaction, assigned by the user's bank or financial institution       |
| from_date      | date      | No       | The earliest date to include payment transactions from, in ISO 8601 format (e.g., "2023-01-01T00:00:00Z")     |
| to_date        | date      | No       | The latest date to include payment transactions from, in ISO 8601 format (e.g., "2023-03-16T23:59:59Z")       |
| created_at     | date      | No       | The date and time when the payment transaction was created, in ISO 8601 format (e.g., "2023-03-16T12:34:56Z") |


### Modification

This module is pre-built and ready to use, but you can modify it to suit your specific requirements. The module has been built using the nwidart/laravel-modules package, so you can easily modify the code by following the Laravel module development guidelines.

### Support
If you encounter any issues with the Payment Gateway Management Module, please open an issue on the GitHub repository. We will do our best to provide you with timely support and resolve any issues that you encounter.


### For testing the api you can run the following command


```bash
php artisan test Modules/PaymentGatewayManagement/Tests/Unit
```




