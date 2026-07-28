<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Jnto'),
    'env' => env('APP_ENV', 'local'),
    'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOL),
    'url' => env('APP_URL', 'http://localhost:8086'),
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
];
