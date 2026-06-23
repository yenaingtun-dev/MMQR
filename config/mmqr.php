<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Supported: "uat", "production"
    |
    */

    'environment' => env('MMQR_ENV', 'uat'),

    'currency' => env('MMQR_CURRENCY', 'MMK'),

    'uat' => [
        'access_token_url' => env('MMQR_UAT_ACCESS_TOKEN_URL', 'https://opensandbox.ayainnovation.com/token'),
        'user_token_url' => env('MMQR_UAT_USER_TOKEN_URL', 'https://opensandbox.ayainnovation.com/om/1.0.0/thirdparty/merchant/login'),
        'qr_url' => env('MMQR_UAT_QR_URL', 'https://opensandbox.ayainnovation.com/om/1.0.0/thirdparty/merchant/v2/requestQRPayment'),
        'phone' => env('MMQR_UAT_PHONE'),
        'pin' => env('MMQR_UAT_PIN'),
        'service_code_qr' => env('MMQR_UAT_SERVICE_CODE_QR'),
        'consumer_key' => env('MMQR_UAT_CONSUMER_KEY'),
        'consumer_secret' => env('MMQR_UAT_CONSUMER_SECRET'),
        'decryption_key' => env('MMQR_UAT_DECRYPTION_KEY'),
    ],

    'production' => [
        'access_token_url' => env('MMQR_PROD_ACCESS_TOKEN_URL', 'https://api.ayapay.com/token'),
        'user_token_url' => env('MMQR_PROD_USER_TOKEN_URL', 'https://api.ayapay.com/merchant/1.0.0/thirdparty/merchant/login'),
        'qr_url' => env('MMQR_PROD_QR_URL', 'https://api.ayapay.com/merchant/1.0.0/thirdparty/merchant/v2/requestQRPayment'),
        'phone' => env('MMQR_PROD_PHONE'),
        'pin' => env('MMQR_PROD_PIN'),
        'service_code_qr' => env('MMQR_PROD_SERVICE_CODE_QR'),
        'consumer_key' => env('MMQR_PROD_CONSUMER_KEY'),
        'consumer_secret' => env('MMQR_PROD_CONSUMER_SECRET'),
        'decryption_key' => env('MMQR_PROD_DECRYPTION_KEY'),
    ],

];
