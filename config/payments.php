<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Which gateway is used when a payer doesn't explicitly pick one at
    | checkout. New gateways can be added by implementing the
    | App\Services\Payments\PaymentGateway contract and registering them
    | in App\Services\Payments\PaymentManager.
    |
    */

    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'stripe'),

    'currency' => env('PAYMENT_CURRENCY', 'INR'),

    'platform_fee_percent' => (float) env('PLATFORM_FEE_PERCENT', 15),

];
