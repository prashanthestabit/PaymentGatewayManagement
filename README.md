# Payment Gateway Management Module

This module provides one-time payment functionality with Stripe and PayPal in Laravel applications. It is pre-built and ready to use, and can be easily modified to suit your specific requirements.

## Features

1. One-time payment functionality with Stripe and PayPal
2. Stripe payments using Laravel Cashier
3. Handling of webhooks for Stripe and PayPal payments
4. Creation of filter API for both payment gateways

## Installation

This module has been built using the nwidart/laravel-modules package, so make sure you have this package installed in your project. For installation, please refer to the nwidart/laravel-modules documentation.

Once you have installed the nwidart/laravel-modules package, you can simply clone this repository and copy the PaymentGateway module to the Modules directory of your Laravel application.

### Setting up Stripe with Laravel Cashier

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

  ### Setting up Paypal with Laravel Omnipay/Omnipay

  Laravel Omnipay is a payment processing library that provides a unified API for various payment gateways, including PayPal. In this guide, we'll walk you through the steps to set up PayPal with Laravel Omnipay.

  ### Prerequisites

  Before we get started, you'll need to make sure that you have the following prerequisites in place:

  1. A PayPal account (if you don't have one, you can sign up for free at paypal.com)
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

### Modification

This module is pre-built and ready to use, but you can modify it to suit your specific requirements. The module has been built using the nwidart/laravel-modules package, so you can easily modify the code by following the Laravel module development guidelines.

### Support
If you encounter any issues with the Payment Gateway Management Module, please open an issue on the GitHub repository. We will do our best to provide you with timely support and resolve any issues that you encounter.


### For testing the api you can run the following command


```bash
php artisan test Modules/PaymentGatewayManagement/Tests/Unit
```




