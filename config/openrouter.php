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
    'max_tokens' => env('OPENROUTER_MAX_TOKENS', 8192),

    'temperature' => env('OPENROUTER_TEMPERATURE', 0.7),

    'timeout' => env('OPENROUTER_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Model fallback chain (used by lesson wizard AI generation)
    |--------------------------------------------------------------------------
    |
    | Three models tried in sequence. If the primary fails, fallback1 is used,
    | then fallback2. If all three fail, the user is asked to retry manually.
    |
    */
    'model_primary'   => env('OPENROUTER_MODEL_PRIMARY',   'qwen/qwen3-32b'),
    'model_fallback1' => env('OPENROUTER_MODEL_FALLBACK1', 'mistralai/mistral-large'),
    'model_fallback2' => env('OPENROUTER_MODEL_FALLBACK2', 'inclusionai/ling-2.6-flash'),
    'model_fallback3' => env('OPENROUTER_MODEL_FALLBACK3', 'nvidia/nemotron-3-nano-30b-a3b'),
    'model_fallback4' => env('OPENROUTER_MODEL_FALLBACK4', 'anthropic/claude-sonnet-4'),

    /*
    |--------------------------------------------------------------------------
    | Diagram generation models (used by generateSlideDiagram)
    |--------------------------------------------------------------------------
    |
    | Separate chain for diagram generation (Mermaid.js HTML). Primary model
    | with two fallback levels, all configurable via .env.
    |
    */
    'model_diagram_primary'   => env('OPENROUTER_MODEL_DIAGRAM_PRIMARY',   'qwen/qwen3-32b'),
    'model_diagram_fallback1' => env('OPENROUTER_MODEL_DIAGRAM_FALLBACK1', 'mistralai/mistral-large'),
    'model_diagram_fallback2' => env('OPENROUTER_MODEL_DIAGRAM_FALLBACK2', 'anthropic/claude-sonnet-4'),

];
