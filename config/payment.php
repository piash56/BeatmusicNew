<?php

return [
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'sandbox_api_url' => 'https://api-m.sandbox.paypal.com',
        'live_api_url' => 'https://api-m.paypal.com',
    ],

    'renewal_reminder_days' => 3,
    'subscription_grace_period_days' => 3,

    'plans' => [
        'Free' => [
            'tracks_per_month' => 2,
            'can_submit_playlists' => false,
            'can_request_radio' => false,
        ],
        'Premium' => [
            'tracks_per_month' => 10,
            'can_submit_playlists' => true,
            'can_request_radio' => true,
        ],
        'Pro' => [
            'tracks_per_month' => -1, // unlimited
            'can_submit_playlists' => true,
            'can_request_radio' => true,
        ],
    ],
];
