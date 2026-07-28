<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Request;
use Core\Response;

final class AuthMiddleware
{
    /**
     * Handle authentication for the request.
     *
     * @param Request $request The incoming request instance.
     * @param Response $response The response instance used for redirects.
     * @return bool True to continue processing the request; false to stop.
     */
    public function handle(Request $request, Response $response): bool
    {
        if (!app()->session()->get('user')) {
            $response->redirect('/login');
        }

        return true;
    }
}
