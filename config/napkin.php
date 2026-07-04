<?php

return [
    'api_url' => env('NAPKIN_API_URL', 'https://api.napkin.ai'),
    'api_token' => env('NAPKIN_API_TOKEN'),
    'timeout' => env('NAPKIN_API_TIMEOUT', 30),
    'storage_disk' => env('NAPKIN_STORAGE_DISK', 'public'),
    'formats' => ['svg', 'png', 'ppt'],
    'default_language' => 'es-ES',
];
