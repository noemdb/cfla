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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sendpulse' => [
        'client_id'              => env('SENDPULSE_CLIENT_ID'),
        'client_secret'          => env('SENDPULSE_API_KEY'),
        'from_email'             => env('SENDPULSE_FROM'),
        'from_name'              => env('SENDPULSE_FROM_NAME'),
        'oauth_url'              => env('SENDPULSE_OAUTH_URL', 'https://api.sendpulse.com/oauth/access_token'),
    ],

    'gemini' => [
        'api_key'         => env('GEMINI_API_KEY'),
        'api_url'         => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta'),
    ],

];
