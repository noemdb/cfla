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
    | Step2 generation models (used by generateStep2Sections)
    |--------------------------------------------------------------------------
    |
    | Specialized chain for generating full lesson structure (INICIO, DESARROLLO,
    | CIERRE). Primary model prioritized for quality, with 3 fallback levels.
    |
    */
    'model_step2_primary'   => env('OPENROUTER_MODEL_STEP2_PRIMARY',   'anthropic/claude-sonnet-4'),
    'model_step2_fallback1' => env('OPENROUTER_MODEL_STEP2_FALLBACK1', 'qwen/qwen3-32b'),
    'model_step2_fallback2' => env('OPENROUTER_MODEL_STEP2_FALLBACK2', 'mistralai/mistral-large'),
    'model_step2_fallback3' => env('OPENROUTER_MODEL_STEP2_FALLBACK3', 'inclusionai/ling-2.6-flash'),

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

    /*
    |--------------------------------------------------------------------------
    | Text generation models (used by generateSlideText)
    |--------------------------------------------------------------------------
    |
    | Chain for slide text content generation. Primary model with two fallback
    | levels, all configurable via .env. The user prefers:
    | primary → inclusionai/ling-2.6-flash
    | fallback1 → nvidia/nemotron-3-nano-30b-a3b
    | fallback2 → mistralai/mistral-large
    |
    */
    'model_text_primary'   => env('OPENROUTER_MODEL_TEXT_PRIMARY',   'inclusionai/ling-2.6-flash'),
    'model_text_fallback1' => env('OPENROUTER_MODEL_TEXT_FALLBACK1', 'nvidia/nemotron-3-nano-30b-a3b'),
    'model_text_fallback2' => env('OPENROUTER_MODEL_TEXT_FALLBACK2', 'mistralai/mistral-large'),

    /*
    |--------------------------------------------------------------------------
    | Image/SVG generation models (used by generateSlideImage)
    |--------------------------------------------------------------------------
    |
    | Chain for educational SVG diagram generation. Primary model with two
    | fallback levels, all configurable via .env.
    |
    */
    'model_image_primary'   => env('OPENROUTER_MODEL_IMAGE_PRIMARY',   'anthropic/claude-sonnet-4'),
    'model_image_fallback1' => env('OPENROUTER_MODEL_IMAGE_FALLBACK1', 'nvidia/nemotron-3-nano-30b-a3b'),
    'model_image_fallback2' => env('OPENROUTER_MODEL_IMAGE_FALLBACK2', 'mistralai/mistral-large'),

    /*
    |--------------------------------------------------------------------------
    | Illustration/SVG generation models (used by generateSectionIllustration)
    |--------------------------------------------------------------------------
    |
    | Chain for educational SVG illustration generation (prompt SVG-educativo-v3).
    | Primary model with two fallback levels, all configurable via .env.
    |
    */
    'model_illustration_primary'   => env('OPENROUTER_MODEL_ILLUSTRATION_PRIMARY',   'anthropic/claude-sonnet-4'),
    'model_illustration_fallback1' => env('OPENROUTER_MODEL_ILLUSTRATION_FALLBACK1', 'nvidia/nemotron-3-nano-30b-a3b'),
    'model_illustration_fallback2' => env('OPENROUTER_MODEL_ILLUSTRATION_FALLBACK2', 'mistralai/mistral-large'),

    /*
    |--------------------------------------------------------------------------
    | Math/LaTeX generation models (used by generateSlideMath)
    |--------------------------------------------------------------------------
    |
    | Chain for detecting math formulas and converting to LaTeX. Primary model
    | with one fallback, both configurable via .env.
    |
    */
    'model_math_primary'   => env('OPENROUTER_MODEL_MATH_PRIMARY',   'qwen/qwen3-coder-flash'),
    'model_math_fallback1' => env('OPENROUTER_MODEL_MATH_FALLBACK1', 'deepseek/deepseek-v4-flash'),

];
