<?php

declare(strict_types=1);

namespace Core;

final class Csrf
{
    public static function verify(Request $request): void
    {
        if (!app()->session()->validateToken((string) $request->input('_token'))) {
            http_response_code(419);
            exit('Invalid CSRF token.');
        }
    }
}
