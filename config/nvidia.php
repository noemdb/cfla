<?php

return [

    /*
    |--------------------------------------------------------------------------
    | NVIDIA build.nvidia.com Configuration
    |--------------------------------------------------------------------------
    |
    | API integration with NVIDIA's build.nvidia.com platform for LLM inference.
    | Uses OpenAI-compatible API format.
    | See https://build.nvidia.com/docs for API documentation.
    |
    */

    'api_key' => env('NVIDIA_API_KEY'),

    'base_url' => env('NVIDIA_BASE_URL', 'https://integrate.api.nvidia.com/v1'),

    'model' => env('NVIDIA_MODEL', 'qwen/qwen3.5-122b-a10b'),

    /*
    |--------------------------------------------------------------------------
    | Default generation parameters
    |--------------------------------------------------------------------------
    */
    'max_tokens' => env('NVIDIA_MAX_TOKENS', 8192),

    'temperature' => env('NVIDIA_TEMPERATURE', 0.7),

    'timeout' => env('NVIDIA_TIMEOUT', 60),

];
