<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Merchant Details
    |--------------------------------------------------------------------------
    */

    'merchant_name' => env('MMQR_MERCHANT_NAME', ''),

    'merchant_city' => env('MMQR_MERCHANT_CITY', 'MY'),

    /*
    |--------------------------------------------------------------------------
    | Transaction Defaults
    |--------------------------------------------------------------------------
    |
    | currency: ISO 4217 numeric code (458 = MYR)
    | country_code: ISO 3166-1 alpha-2
    |
    */

    'currency' => env('MMQR_CURRENCY', '458'),

    'country_code' => env('MMQR_COUNTRY_CODE', 'MY'),

    /*
    |--------------------------------------------------------------------------
    | QR Mode
    |--------------------------------------------------------------------------
    |
    | static: amount entered by customer after scan
    | dynamic: amount embedded in QR payload
    |
    */

    'mode' => env('MMQR_MODE', 'static'),

    /*
    |--------------------------------------------------------------------------
    | PayNet / Acquirer (planned)
    |--------------------------------------------------------------------------
    |
    | 'acquirer_id' => env('MMQR_ACQUIRER_ID'),
    | 'qr_id' => env('MMQR_QR_ID'),
    | 'merchant_category_code' => env('MMQR_MCC'),
    |
    */

];
