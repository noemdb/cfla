<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Datalab (preprocesamiento de PDFs)
    |--------------------------------------------------------------------------
    |
    | Datalab (https://www.datalab.to) convierte PDFs a markdown estructurado
    | antes de enviar el texto al LLM. Se consume como API REST (submit + poll)
    | con la clave `DATALAB_API_KEY` en el header `X-API-Key`.
    |
    */

    'datalab' => [
        'api_key' => env('DATALAB_API_KEY') ?: null,
        'base_url' => rtrim(env('DATALAB_API_BASE_URL', 'https://www.datalab.to'), '/'),
        // 'fast' (bajo costo), 'balanced' (recomendado) o 'accurate' (máxima precisión).
        'mode' => env('DATALAB_MODE', 'balanced'),
        // Tiempo máximo total (segundos) entre submit + polling.
        'timeout' => (int) env('DATALAB_TIMEOUT', 300),
        // Intervalo entre polls (segundos).
        'poll_interval' => (int) env('DATALAB_POLL_INTERVAL', 2),
        // Reintentos ante 429/5xx en el submit.
        'max_attempts' => (int) env('DATALAB_MAX_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Generación de lecciones (paso 2)
    |--------------------------------------------------------------------------
    */

    // Palabras mínimas por bloque de //DESARROLLO para aceptar la generación.
    'section_block_min_words' => (int) env('LMS_SECTION_BLOCK_MIN_WORDS', 60),

    // Piso duro (palabras): un bloque por debajo de esto se considera placeholder
    // y rechaza la generación. Entre hard_floor y min_words el bloque se acepta.
    'section_block_hard_floor' => (int) env('LMS_SECTION_BLOCK_HARD_FLOOR', 30),

    // Reintentos del MISMO modelo con feedback del validador antes de pasar
    // al siguiente modelo de la cadena (0 = desactivado).
    'repair_attempts' => (int) env('LMS_REPAIR_ATTEMPTS', 1),

];
