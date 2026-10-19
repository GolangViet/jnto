<?php

declare(strict_types=1);

namespace Core;

final class Csrf
{
    public static function verify(Request $request): void
    {
        $token = $request->input('_token')
            ?? $request->header('X-CSRF-TOKEN')
            ?? $request->header('X-XSRF-TOKEN');

        if (!app()->session()->validateToken(is_string($token) ? $token : null)) {
            http_response_code(419);
            if ($request->expectsJson()) {
                app()->response()->json(['message' => 'Invalid CSRF token.'], 419);
                exit;
            }
            exit('Invalid CSRF token.');
        }
    }
}
