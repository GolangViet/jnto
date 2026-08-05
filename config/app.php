<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Jnto'),
    'env' => env('APP_ENV', 'prod'),
    'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOL),
    'url' => env('APP_URL', 'URL: https://mng.sys.jnto.go.jp'),
    'key' => env('APP_KEY', 'base64:M0pudDBTZWN1cmVBcHBLZXlGb3JFcmNyeXB0aW9uMzI='),
    'cipher' => 'AES-256-CBC',
];
