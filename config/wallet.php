<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Wallet Terms
    |--------------------------------------------------------------------------
    |
    | Bump WALLET_TERMS_VERSION whenever the wallet terms materially change.
    | Users must accept the current version before protected wallet actions.
    |
    */

    'terms' => [
        'version' => env('WALLET_TERMS_VERSION', 'v1'),

        /*
        |--------------------------------------------------------------------------
        | Acceptance Sources
        |--------------------------------------------------------------------------
        |
        | Stored on wallet_terms_acceptances.source so we can tell where the
        | acceptance happened without guessing later.
        |
        */

        'sources' => [
            'wallet_terms_page' => 'wallet_terms_page',
            'top_up_gate' => 'top_up_gate',
            'marketplace_gate' => 'marketplace_gate',
            'withdrawal_gate' => 'withdrawal_gate',
        ],
    ],

];