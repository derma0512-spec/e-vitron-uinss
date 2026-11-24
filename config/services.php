<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'brevo' => [
        'api_key' => env('BREVO_API_KEY'),
        'endpoint' => env('BREVO_API_ENDPOINT', 'https://api.brevo.com/v3/smtp/email'),
        'timeout' => (int) env('MAIL_TIMEOUT', 8),
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

    'bridge_sso' => [
        'enabled' => env('BRIDGE_SSO_ENABLED', true),
        'base_url' => rtrim((string) env('BRIDGE_SSO_BASE_URL', 'https://bridge.uinssc.ac.id'), '/'),
        'timeout' => (int) env('BRIDGE_SSO_TIMEOUT', 8),
        'simulate' => (bool) env('BRIDGE_SSO_SIMULATE', false),
    ],

    'demo' => [
        'quick_login' => (bool) env('DEMO_QUICK_LOGIN', filled(env('DEMO_ADMIN_EMAIL')) && filled(env('DEMO_ADMIN_PASSWORD'))),
        'admin_email' => env('DEMO_ADMIN_EMAIL'),
        'admin_password' => env('DEMO_ADMIN_PASSWORD'),
        'applicant_identifier' => env('DEMO_APPLICANT_IDENTIFIER', '2208101001'),
        'applicant_email' => env('DEMO_APPLICANT_EMAIL', '2208101001@student.uin.ac.id'),
        'applicant_password' => env('DEMO_APPLICANT_PASSWORD', env('DEMO_ADMIN_PASSWORD')),
    ],

];
