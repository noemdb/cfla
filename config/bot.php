<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bot / Autoresponder SAEFL
    |--------------------------------------------------------------------------
    |
    | URL base de la API de saefl (Sistema de Administración Educativa
    | Fray Luis) usada por el chatbot del sitio. Se define desde
    | APP_URL_SAEFL en el .env, con fallback a localhost.
    |
    | IMPORTANTE: usar siempre config() en lugar de env() dentro de clases y
    | vistas, para que la URL se resuelva correctamente al hacer
    | php artisan config:cache en producción.
    |
    */

    'saefl' => [
        'base_url' => rtrim(env('APP_URL_SAEFL', 'http://localhost:2526'), '/'),
        'autoresponder_path' => '/api/bot/autoresponder',
    ],

];
