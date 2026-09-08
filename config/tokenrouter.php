<?php

return [

    /*
    |--------------------------------------------------------------------------
    | TokenRouter Configuration
    |--------------------------------------------------------------------------
    |
    | TokenRouter is a unified multi-model API gateway (OpenAI-compatible):
    | un solo API key y un formato de request consistente para acceder a
    | múltiples modelos. Ver:
    |   https://www.tokenrouter.com/docs/tokenrouter-feature-guide/
    |
    | NOTA: confirma el base_url real en la consola/API de TokenRouter antes de
    | usar en producción (el valor por defecto sigue el patrón /v1 de los
    | gateways OpenAI-compatibles).
    |
    */

    'api_key' => env('TOKENROUTER_API_KEY'),

    'base_url' => env('TOKENROUTER_BASE_URL', 'https://api.tokenrouter.com/v1'),

    'model' => env('TOKENROUTER_MODEL', 'gpt-4o-mini'),

    /*
    |--------------------------------------------------------------------------
    | Model fallback chain (dynamic global routing / high availability)
    |--------------------------------------------------------------------------
    |
    | Cadena de modelos a probar en orden. Si el primario falla (error 5xx,
    | sin contenido, etc.) se intenta fallback1, luego fallback2, etc.
    | "Multi-Channel Failover" del gateway: el proveedor reenruta a un canal
    | disponible si la ruta se degrada.
    |
    */
    'model_primary' => env('TOKENROUTER_MODEL_PRIMARY', 'gpt-4o-mini'),
    'model_fallback1' => env('TOKENROUTER_MODEL_FALLBACK1', 'claude-3-5-haiku'),
    'model_fallback2' => env('TOKENROUTER_MODEL_FALLBACK2', 'deepseek-chat'),

    /*
    |--------------------------------------------------------------------------
    | Default generation parameters
    |--------------------------------------------------------------------------
    */
    'max_tokens' => env('TOKENROUTER_MAX_TOKENS', 4096),

    'temperature' => env('TOKENROUTER_TEMPERATURE', 0.7),

    'timeout' => env('TOKENROUTER_TIMEOUT', 60),
];
