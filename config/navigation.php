<?php

return [
    'public' => [
        [
            'label' => 'Home',
            'route' => 'home',
        ],
        [
            'label' => 'Marketplace',
            'route' => 'marketplace.index',
        ],
        [
            'label' => 'Matches',
            'route' => 'matches.index',
        ],
        [
            'label' => 'Pick’em',
            'route' => 'pickem.index',
        ],
        [
            'label' => 'Teams',
            'route' => 'teams.index',
        ],
        [
            'label' => 'Shop',
            'route' => 'shop.index',
        ],
    ],

    'footer_explore' => [
        [
            'label' => 'Home',
            'route' => 'home',
        ],
        [
            'label' => 'Marketplace',
            'route' => 'marketplace.index',
        ],
        [
            'label' => 'Matches',
            'route' => 'matches.index',
        ],
        [
            'label' => 'Pick’em',
            'route' => 'pickem.index',
        ],
        [
            'label' => 'Teams',
            'route' => 'teams.index',
        ],
        [
            'label' => 'Shop',
            'route' => 'shop.index',
        ],
        [
            'label' => 'Contact',
            'route' => 'contact.create',
        ],
        [
            'label' => 'Sitemap',
            'route' => 'sitemap',
        ],
    ],

    'footer_legal' => [
        [
            'label' => 'Privacy Policy',
            'route' => 'legal.privacy',
        ],
        [
            'label' => 'Terms of Service',
            'route' => 'legal.terms',
        ],
        [
            'label' => 'Data Usage',
            'route' => 'legal.data',
        ],
        [
            'label' => 'Affiliate Disclosures',
            'route' => 'legal.affiliate',
        ],
        [
            'label' => 'Disclaimer',
            'route' => 'legal.disclaimer',
        ],
        [
            'label' => 'Wallet Terms',
            'route' => 'wallet.terms',
        ],
    ],

    'footer_law_enforcement' => [
        [
            'label' => 'Law Enforcement Requests',
            'route' => 'legal.law-enforcement',
        ],
        [
            'label' => 'Report Payment Fraud',
            'route' => 'legal.law-enforcement',
            'fragment' => 'payment-fraud',
        ],
        [
            'label' => 'Report Marketplace Fraud',
            'route' => 'legal.law-enforcement',
            'fragment' => 'marketplace-fraud',
        ],
        [
            'label' => 'Emergency Requests',
            'route' => 'legal.law-enforcement',
            'fragment' => 'emergency-requests',
        ],
    ],

    'admin' => [
        [
            'label' => 'Front End',
            'route' => 'home',
        ],
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
        ],

        [
            'heading' => 'CS2 Content',
        ],
        [
            'label' => 'Teams',
            'route' => 'admin.teams.index',
        ],
        [
            'label' => 'Players',
            'route' => 'admin.players.index',
        ],
        [
            'label' => 'Events',
            'route' => 'admin.events.index',
        ],
        [
            'label' => 'Matches',
            'route' => 'admin.matches.index',
        ],
        [
            'label' => 'Predictions',
            'route' => 'admin.predictions.index',
        ],
        [
            'label' => 'Pick’em',
            'route' => 'admin.pickem.index',
        ],
        [
            'label' => 'GRID Imports',
            'route' => 'admin.grid.index',
        ],

        [
            'heading' => 'Commerce',
        ],
        [
            'label' => 'Products',
            'route' => 'admin.products.index',
        ],
        [
            'label' => 'Shop Orders',
            'route' => 'admin.orders.index',
        ],
        [
            'label' => 'Marketplace Listings',
            'route' => 'admin.marketplace.listings',
        ],
        [
            'label' => 'Marketplace Trades',
            'route' => 'admin.marketplace.trade-requests',
        ],
        [
            'label' => 'Wallet Terms',
            'route' => 'admin.wallet-terms.acceptances',
        ],

        [
            'heading' => 'Moderation',
        ],
        [
            'label' => 'Crackdown',
            'route' => 'admin.crackdown.index',
        ],

        [
            'heading' => 'System',
        ],
        [
            'label' => 'Content Gates',
            'route' => 'admin.content-gates.index',
        ],
    ],
];