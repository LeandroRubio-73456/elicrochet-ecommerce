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
    'payphone' => [
        'token' => env('PAYPHONE_TOKEN'),
        'client_id' => env('PAYPHONE_CLIENT_ID'),
        'client_secret' => env('PAYPHONE_CLIENT_SECRET'),
        'mode' => env('PAYPHONE_MODE', 'sandbox'),
        // null = automático (simula solo si no hay token real configurado).
        // true/false fuerza el modo sin importar si hay token.
        'simulate' => env('PAYPHONE_SIMULATE'),
        'base_url' => env('PAYPHONE_MODE') == 'live'
           ? 'https://api.payphone.app/prod' // URL de producción
           : 'https://api.payphone.app/dev', // URL de pruebas
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],
];
