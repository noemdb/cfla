<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Kimi (Moonshot) Configuration
    |--------------------------------------------------------------------------
    |
    | API integration with Kimi AI via Moonshot platform (platform.kimi.ai).
    | Uses OpenAI-compatible API format.
    | See https://platform.kimi.ai/docs/overview for API documentation.
    |
    */

    'api_key' => env('KIMI_API_KEY'),

    'base_url' => env('KIMI_BASE_URL', 'https://api.moonshot.cn/v1'),

    'model' => env('KIMI_MODEL', 'moonshot-v1-8k'),

    /*
    |--------------------------------------------------------------------------
    | Default generation parameters
    |--------------------------------------------------------------------------
    */
    'max_tokens' => env('KIMI_MAX_TOKENS', 8192),

    'temperature' => env('KIMI_TEMPERATURE', 0.7),

    'timeout' => env('KIMI_TIMEOUT', 60),

];
