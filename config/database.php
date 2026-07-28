<?php

declare(strict_types=1);

return [
    'driver' => env('DB_CONNECTION', 'pgsql'),
    'host' => env('DB_HOST', 'db'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'jnto'),
    'username' => env('DB_USERNAME', 'postgres'),
    'password' => env('DB_PASSWORD', '123456xyz'),
    'charset' => 'utf8mb4',
];
