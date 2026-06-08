<?php

return [
    'adsense' => [
        'enabled' => env('ADSENSE_ENABLED', false),
        'client' => env('ADSENSE_CLIENT'),
        'slots' => [
            'top_banner' => env('ADSENSE_SLOT_TOP_BANNER'),
            'sidebar' => env('ADSENSE_SLOT_SIDEBAR'),
            'in_content' => env('ADSENSE_SLOT_IN_CONTENT'),
            'footer_banner' => env('ADSENSE_SLOT_FOOTER_BANNER'),
        ],
    ],

    'affiliate' => [
        'enabled' => env('AFFILIATE_ENABLED', true),
        'disclosure' => env(
            'AFFILIATE_DISCLOSURE',
            'This page may contain affiliate links. If you buy through these links, CS2 PickLab may earn a commission at no extra cost to you.'
        ),
    ],
];