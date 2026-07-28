<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$router = app()->router();

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);

$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);

$router->get('/admin/login', [AuthController::class, 'showAdminLogin'], [GuestMiddleware::class]);

$router->post('/admin/login', [AuthController::class, 'adminLogin'], [GuestMiddleware::class]);

$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->get('/admin/posts', [PostController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);

$router->get('/admin/posts/create', [PostController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);

$router->post('/admin/posts', [PostController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);

$router->get('/admin/posts/{id}/edit', [PostController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);

$router->put('/admin/posts/{id}', [PostController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class]);

$router->delete('/admin/posts/{id}', [PostController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class]);


