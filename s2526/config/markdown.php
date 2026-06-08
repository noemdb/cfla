<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bootstrap Version
    |--------------------------------------------------------------------------
    |
    | Versión de Bootstrap a utilizar (4.3, 5, etc.)
    |
    */
    'bootstrap_version' => env('BOOTSTRAP_VERSION', '4.3'),
    
    /*
    |--------------------------------------------------------------------------
    | Default Options
    |--------------------------------------------------------------------------
    |
    | Opciones por defecto para aplicar al HTML
    |
    */
    'default_options' => [
        'p' => 'text-justify',
        'a' => 'text-primary',
        'h1' => 'display-4',
        'h2' => 'display-5',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Use External Parser
    |--------------------------------------------------------------------------
    |
    | Usar una librería externa para parsing de markdown
    |
    */
    'use_external_parser' => env('MARKDOWN_USE_EXTERNAL', false),
    
    /*
    |--------------------------------------------------------------------------
    | External Parser Class
    |--------------------------------------------------------------------------
    |
    | Clase del parser externo a utilizar
    |
    */
    'external_parser' => \League\CommonMark\CommonMarkConverter::class,
];