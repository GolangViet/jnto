<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\SurveyCheckMiddleware;

$router = app()->router();

$router->get('/', [HomeController::class, 'index'], [SurveyCheckMiddleware::class]);

$router->get('/thank-you', [HomeController::class, 'detailThankYou']);

$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);

$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);

$router->get('/register', [AuthController::class, 'showRegister'], [GuestMiddleware::class]);

$router->post('/register', [AuthController::class, 'register'], [GuestMiddleware::class]);

$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->get('/admin/login', [AuthController::class, 'showAdminLogin'], [GuestMiddleware::class]);

$router->post('/admin/login', [AuthController::class, 'adminLogin'], [GuestMiddleware::class]);

$router->get('/admin/posts', [PostController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);

$router->get('/admin/posts/create', [PostController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);

$router->post('/admin/posts', [PostController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);

$router->get('/admin/posts/{id}/edit', [PostController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);

$router->put('/admin/posts/{id}', [PostController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class]);

$router->delete('/admin/posts/{id}', [PostController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class]);

// --- QUIZ & QUESTION FEATURE ROUTES ---

use App\Controllers\Admin\QuizController as AdminQuizController;
use App\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Controllers\Admin\SettingController as AdminSettingController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\QuizAttemptController;
use App\Controllers\TakeSurveyController;

// Admin Dashboard Route
$router->get('/admin/dashboard', [AdminDashboardController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);

// Admin Settings Routes
$router->get('/admin/settings', [AdminSettingController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/settings', [AdminSettingController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class]);

// Admin Users Routes
$router->get('/admin/users', [AdminUserController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/users/create', [AdminUserController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/users', [AdminUserController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/users/{id}/edit', [AdminUserController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->put('/admin/users/{id}', [AdminUserController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->delete('/admin/users/{id}', [AdminUserController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class]);


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

// End-User take survey
$router->get('/take-survey', [TakeSurveyController::class, 'detailSurvey'], [AuthMiddleware::class]);
$router->get('/take-questions', [TakeSurveyController::class, 'detailQuestions'], [AuthMiddleware::class]);
$router->get('/confirm-post', [TakeSurveyController::class, 'confirmPost'], [AuthMiddleware::class]);
$router->post('/submit-post', [TakeSurveyController::class, 'submitPost'], [AuthMiddleware::class]);

// End-User Quiz HTML Routes
$router->get('/quizzes', [QuizAttemptController::class, 'index'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);
$router->get('/quizzes/{id}', [QuizAttemptController::class, 'show'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);
$router->post('/quizzes/{id}/start', [QuizAttemptController::class, 'start'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);
$router->get('/quiz-attempts/{attemptId}', [QuizAttemptController::class, 'attempt'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);
$router->get('/quiz-attempts/{attemptId}/result', [QuizAttemptController::class, 'result'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);

// End-User Quiz API Routes
$router->get('/api/quizzes', [QuizAttemptController::class, 'apiIndex'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);
$router->get('/api/quizzes/{id}', [QuizAttemptController::class, 'apiShow'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);
$router->post('/api/quizzes/{id}/start', [QuizAttemptController::class, 'apiStart'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);
$router->get('/api/quiz-attempts/{attemptId}', [QuizAttemptController::class, 'apiGetAttempt'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);
$router->post('/api/quiz-attempts/{attemptId}/answers', [QuizAttemptController::class, 'apiSaveAnswer'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);
$router->post('/api/quiz-attempts/{attemptId}/submit', [QuizAttemptController::class, 'apiSubmit'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);
$router->get('/api/quiz-attempts/{attemptId}/result', [QuizAttemptController::class, 'apiResult'], [AuthMiddleware::class, SurveyCheckMiddleware::class]);


