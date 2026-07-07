<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Napkin.ai Configuration
    |--------------------------------------------------------------------------
    |
    | napkin.ai provides AI-powered diagram/image generation from text.
    | Generates SVG diagrams that can be embedded directly as HTML.
    | See https://app.napkin.ai for API key management (Profile → API Keys).
    |
    */

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    */
    'api_key' => env('NAPKIN_API_KEY', env('NAPKIN_API_TOKEN')),

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | base_url:      NLP California API — image/diagram generation
    | vega_url:      Data graphics generator (Vega-based visualizations)
    | tool_url:      Tool API for captures and source links
    |
    */
    'base_url' => env('NAPKIN_API_URL', 'https://nlp-california-api.napkin.ai/api/v1'),

    'vega_url' => env('NAPKIN_VEGA_URL', 'https://vega.nlp.api.napkin.ai/api/v1'),

    'tool_url' => env('NAPKIN_TOOL_URL', 'https://api.tool.napkin.ai/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | Image/Diagram Generation Parameters
    |--------------------------------------------------------------------------
    */
    'resolution' => env('NAPKIN_RESOLUTION', '1024x1024'),

    'timeout' => env('NAPKIN_API_TIMEOUT', 120),

];

