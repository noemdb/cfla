<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenRouter Configuration
    |--------------------------------------------------------------------------
    |
    | OpenRouter provides a unified API for multiple LLM providers.
    | See https://openrouter.ai/docs for API documentation.
    |
    */

    'api_key' => env('OPENROUTER_API_KEY'),

    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),

    'model' => env('OPENROUTER_MODEL', 'google/gemma-4-31b-it:free'),

    /*
    |--------------------------------------------------------------------------
    | Default generation parameters
    |--------------------------------------------------------------------------
    */
    'max_tokens' => env('OPENROUTER_MAX_TOKENS', 1024),

    'temperature' => env('OPENROUTER_TEMPERATURE', 0.7),

    'timeout' => env('OPENROUTER_TIMEOUT', 60),

];
