<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Request;
use Core\Response;
use Core\View;

final class MaintenanceMiddleware
{
    /**
     * Handle incoming requests and enforce maintenance mode if active.
     *
     * @param Request $request The incoming request.
     * @param Response $response The outgoing response helper.
     * @return bool True if the request should proceed; false if blocked.
     */
    public function handle(Request $request, Response $response): bool
    {
        $mode = (string) setting('maintenance_mode', '0');
        if ($mode !== '1') {
            return true;
        }

        $path = '/' . trim($request->path(), '/');

        // Always allow admin routes and admin auth routes
        if ($path === '/admin' || str_starts_with($path, '/admin/') || str_starts_with($path, '/api/admin/')) {
            return true;
        }

        // Allow logging out
        if ($path === '/logout') {
            return true;
        }

        // Allow authenticated administrators to access any page
        $user = app()->session()->get('user');
        if (is_array($user) && ($user['role'] ?? '') === 'admin') {
            return true;
        }

        // Set HTTP 503 Service Unavailable and retry after 1 hour header
        $response->status(503);
        if (!headers_sent()) {
            header('Retry-After: 3600');
        }

        $rawMessage = (string) setting('maintenance_message', '');
        $hasCustomContent = trim($rawMessage) !== '';
        $apiMessage = $hasCustomContent ? strip_tags($rawMessage) : 'Website đang trong quá trình bảo trì và nâng cấp hệ thống. Xin vui lòng quay lại sau!';

        if ($request->expectsJson() || str_starts_with($path, '/api/')) {
            $response->json([
                'success' => false,
                'message' => $apiMessage,
                'maintenance' => true,
            ], 503);
        }

        try {
            echo View::render('pages/index-2', [
                'message' => $hasCustomContent ? $rawMessage : null,
                'title' => 'HỆ THỐNG ĐANG BẢO TRÌ',
            ], true);
        } catch (\Throwable $e) {
            try {
                echo View::render('errors/503', [
                    'message' => $apiMessage,
                ], false);
            } catch (\Throwable $fallbackEx) {
                echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Bảo trì hệ thống</title></head><body style="font-family:sans-serif;text-align:center;padding:50px;"><h1>Hệ thống đang bảo trì</h1><p>' . e($apiMessage) . '</p></body></html>';
            }
        }

        return false;
    }
}
