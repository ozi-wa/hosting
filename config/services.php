<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'whmcs' => [
        'enabled' => env('WHMCS_ENABLED', false),
        'api_url' => env('WHMCS_API_URL'),
        'identifier' => env('WHMCS_API_IDENTIFIER'),
        'secret' => env('WHMCS_API_SECRET'),
        'access_key' => env('WHMCS_ACCESS_KEY'),
        'default_payment_method' => env('WHMCS_DEFAULT_PAYMENT_METHOD', 'banktransfer'),
        'default_billing_cycle' => env('WHMCS_DEFAULT_BILLING_CYCLE', 'monthly'),
        'sync_currency' => env('WHMCS_SYNC_CURRENCY', 'TRY'),
    ],

];
