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


// --- QUIZ & QUESTION FEATURE ROUTES ---

use App\Controllers\Admin\QuizController as AdminQuizController;
use App\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Controllers\QuizAttemptController;

// Admin Quiz HTML Routes
$router->get('/admin/quizzes', [AdminQuizController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/quizzes/create', [AdminQuizController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/quizzes', [AdminQuizController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/quizzes/{id}/edit', [AdminQuizController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->put('/admin/quizzes/{id}', [AdminQuizController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->delete('/admin/quizzes/{id}', [AdminQuizController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/quizzes/{quizId}/questions', [AdminQuestionController::class, 'builder'], [AuthMiddleware::class, AdminMiddleware::class]);

// Admin Quiz API Routes
$router->get('/api/admin/quizzes', [AdminQuizController::class, 'apiIndex'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/api/admin/quizzes', [AdminQuizController::class, 'apiStore'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/api/admin/quizzes/{id}', [AdminQuizController::class, 'apiShow'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->put('/api/admin/quizzes/{id}', [AdminQuizController::class, 'apiUpdate'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->delete('/api/admin/quizzes/{id}', [AdminQuizController::class, 'apiDestroy'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/api/admin/quizzes/{id}/publish', [AdminQuizController::class, 'apiPublish'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/api/admin/quizzes/{id}/duplicate', [AdminQuizController::class, 'apiDuplicate'], [AuthMiddleware::class, AdminMiddleware::class]);

// Admin Question API Routes
$router->get('/api/admin/quizzes/{quizId}/questions', [AdminQuestionController::class, 'apiIndex'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/api/admin/quizzes/{quizId}/questions', [AdminQuestionController::class, 'apiStore'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/api/admin/questions/{id}', [AdminQuestionController::class, 'apiShow'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->put('/api/admin/questions/{id}', [AdminQuestionController::class, 'apiUpdate'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->delete('/api/admin/questions/{id}', [AdminQuestionController::class, 'apiDestroy'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/api/admin/quizzes/{quizId}/questions/reorder', [AdminQuestionController::class, 'apiReorder'], [AuthMiddleware::class, AdminMiddleware::class]);

// End-User Quiz HTML Routes
$router->get('/quizzes', [QuizAttemptController::class, 'index'], [AuthMiddleware::class]);
$router->get('/quizzes/{id}', [QuizAttemptController::class, 'show'], [AuthMiddleware::class]);
$router->post('/quizzes/{id}/start', [QuizAttemptController::class, 'start'], [AuthMiddleware::class]);
$router->get('/quiz-attempts/{attemptId}', [QuizAttemptController::class, 'attempt'], [AuthMiddleware::class]);
$router->get('/quiz-attempts/{attemptId}/result', [QuizAttemptController::class, 'result'], [AuthMiddleware::class]);

// End-User Quiz API Routes
$router->get('/api/quizzes', [QuizAttemptController::class, 'apiIndex'], [AuthMiddleware::class]);
$router->get('/api/quizzes/{id}', [QuizAttemptController::class, 'apiShow'], [AuthMiddleware::class]);
$router->post('/api/quizzes/{id}/start', [QuizAttemptController::class, 'apiStart'], [AuthMiddleware::class]);
$router->get('/api/quiz-attempts/{attemptId}', [QuizAttemptController::class, 'apiGetAttempt'], [AuthMiddleware::class]);
$router->post('/api/quiz-attempts/{attemptId}/answers', [QuizAttemptController::class, 'apiSaveAnswer'], [AuthMiddleware::class]);
$router->post('/api/quiz-attempts/{attemptId}/submit', [QuizAttemptController::class, 'apiSubmit'], [AuthMiddleware::class]);
$router->get('/api/quiz-attempts/{attemptId}/result', [QuizAttemptController::class, 'apiResult'], [AuthMiddleware::class]);


