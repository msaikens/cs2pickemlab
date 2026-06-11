<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paid Sales Mode
    |--------------------------------------------------------------------------
    |
    | Keep this false until licensing/payment/legal flow is ready.
    |
    */

    'paid_sales_enabled' => env('MARKETPLACE_PAID_SALES_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Seller Fee
    |--------------------------------------------------------------------------
    |
    | Basis points avoid float math.
    | 700 = 7.00%
    |
    */

    'seller_fee_bps' => (int) env('MARKETPLACE_SELLER_FEE_BPS', 500),

    /*
    |--------------------------------------------------------------------------
    | Minimum Fee
    |--------------------------------------------------------------------------
    |
    | Stored in cents.
    |
    */

    'minimum_fee_cents' => (int) env('MARKETPLACE_MIN_FEE_CENTS', 50),
];