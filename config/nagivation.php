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
            'class' => 'featured',
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
            'class' => 'featured',
        ],
    ],

    'footer_legal' => [
        [
            'label' => 'Privacy Policy',
            'route' => 'legal.privacy',
        ],
        [
            'label' => 'Data Usage & Collection',
            'route' => 'legal.data',
        ],
        [
            'label' => 'Terms of Service',
            'route' => 'legal.terms',
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
            'label' => 'Contact Us',
            'route' => 'contact.create',
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
            'heading' => 'Moderation',
        ],

        [
            'label' => 'Crackdown',
            'route' => 'admin.crackdown.index',
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
            'label' => 'Orders',
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
            'label' => 'Shop Orders',
            'route' => 'admin.orders.index',
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