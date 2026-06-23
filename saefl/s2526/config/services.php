<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URL'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'email_provider' => env('EMAIL_PROVIDER', 'resend'),

    'resend' => [
        'url' => env('RESEND_URL'),
        'api_key' => env('RESEND_API_KEY'),
        'from' => env('RESEND_FROM'),
        'name' => env('RESEND_FROM_NAME'),
        'webhook_secret' => env('RESEND_WEBHOOK_SECRET'),
        'daily_limit' => env('RESEND_DAILY_LIMIT', 50),
        'delay_seconds' => env('RESEND_DELAY_SECONDS', 80),
        'enabled' => env('RESEND_ENABLED', true),
    ],

    'sendpulse' => [
        'client_id' => env('SENDPULSE_CLIENT_ID'),
        'client_secret' => env('SENDPULSE_CLIENT_SECRET'),
        'token_storage' => env('SENDPULSE_TOKEN_STORAGE', 'file'),
        'verified_sender' => env('SENDPULSE_VERIFIED_SENDER'),
        'from' => env('SENDPULSE_FROM'),
        'daily_limit' => env('SENDPULSE_DAILY_LIMIT', 250),
        'delay_seconds' => env('SENDPULSE_DELAY_SECONDS', 100),
        'enabled' => env('SENDPULSE_ENABLED', true),
    ],

    'mailjet' => [
        'public_key' => env('MJ_APIKEY_PUBLIC'),
        'private_key' => env('MJ_APIKEY_PRIVATE'),
        'default_from_email' => env('MJ_DEFAULT_FROM_EMAIL'),
        'default_from_name' => env('MJ_DEFAULT_FROM_NAME'),
        'daily_limit' => env('MJ_DAILY_LIMIT', 180),
        'delay_seconds' => env('MJ_DELAY_SECONDS', 90),
        'enabled' => env('MJ_ENABLED', true),
        'from' => env('SENDPULSE_FROM'),
    ],

    'brevo' => [
        'api_key' => env('BREVO_API_KEY'),
        'default_sender' => [
            'name' => env('BREVO_SENDER_NAME'),
            'email' => env('BREVO_SENDER_EMAIL')
        ],
        'daily_limit' => env('BREVO_DAILY_LIMIT', 250),
        'delay_seconds' => env('BREVO_DELAY_SECONDS', 120),
        'enabled' => env('BREVO_ENABLED', true),
        'from' => env('SENDPULSE_FROM'),

    ],

    'gemini' => [
        'api_key'         => env('GEMINI_API_KEY'),
        'api_url'         => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta'),

        'timeout'         => env('GEMINI_TIMEOUT', 60),
        'connect_timeout' => env('GEMINI_CONNECT_TIMEOUT', 10),

        // Retry
        'retry_max'        => env('GEMINI_RETRY_MAX', 3),
        'retry_base_delay' => env('GEMINI_RETRY_BASE_DELAY', 200),
    ],

    'openrouter' => [
        'api_key'         => env('OPENROUTER_API_KEY'),
        'base_url'        => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
        'model'           => env('OPENROUTER_MODEL', 'qwen/qwen-2.5-vl-7b-instruct:free'),
        // meta-llama/llama-3.3-70b-instruct:free|| tngtech/deepseek-r1t2-chimera:free || qwen/qwen-2.5-vl-7b-instruct:free

        'timeout'         => env('OPENROUTER_TIMEOUT', 120),
        'connect_timeout' => env('OPENROUTER_CONNECT_TIMEOUT', 10),
    ],

];
