<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Request;
use Core\Response;

final class AdminMiddleware
{
    /**
     * Handle admin authorization for the request.
     *
     * @param Request $request The incoming request instance.
     * @param Response $response The response instance used for redirects.
     * @return bool True to continue processing the request; false to stop.
     */
    public function handle(Request $request, Response $response): bool
    {
        $user = app()->session()->get('user');

        if (!$user) {
            $response->redirect('/login');
            return false;
        }

        if (($user['role'] ?? 'user') !== 'admin') {
            app()->session()->flash('error', 'Access denied. Admins only.');
            $response->redirect('/');
            return false;
        }

        return true;
    }
}
