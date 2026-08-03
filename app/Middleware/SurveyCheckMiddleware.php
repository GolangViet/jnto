<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Request;
use Core\Response;
use Core\Database;

final class SurveyCheckMiddleware
{
    /**
     * Handle survey completion verification for the request.
     *
     * @param Request $request The incoming request instance.
     * @param Response $response The response instance used for redirects.
     * @return bool True to continue processing the request; false to stop.
     */
    public function handle(Request $request, Response $response): bool
    {
        $user = app()->session()->get('user');
        if (!$user) {
            return true;
        }

        if (($user['role'] ?? 'user') === 'admin') {
            return true;
        }

        $mainSurveyId = setting('main_survey_quiz_id');
        if (!$mainSurveyId) {
            return true;
        }

        $path = $request->path();

        // Check if accessing survey pages or logout to prevent loops
        if ($path === '/take-survey' || $path === '/take-questions' || $path === '/confirm-post' || $path === '/logout' || $path === '/thank-you') {
            return true;
        }

        // Check if accessing a survey quiz attempt
        if (preg_match('#^/quiz-attempts/(\d+)#', $path, $matches) || preg_match('#^/api/quiz-attempts/(\d+)#', $path, $matches)) {
            $attemptId = (int) $matches[1];
            try {
                $db = Database::connection();
                $stmt = $db->prepare("SELECT quiz_id FROM cms.quiz_attempts WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $attemptId]);
                $quizId = $stmt->fetchColumn();
                if ($quizId !== false && (int) $quizId === (int) $mainSurveyId) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Database fallback
            }
        }

        // Check if user has already completed the survey
        try {
            $db = Database::connection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM cms.quiz_attempts WHERE quiz_id = :quiz_id AND user_id = :user_id AND status = 'submitted'");
            $stmt->execute([
                'quiz_id' => (int) $mainSurveyId,
                'user_id' => (int) $user['id']
            ]);
            $hasCompleted = (int) $stmt->fetchColumn() > 0;

            if (!$hasCompleted) {
                $response->redirect('/take-survey');
                return false;
            }
        } catch (\Throwable $e) {
            // If DB error, let request proceed
        }

        return true;
    }
}
