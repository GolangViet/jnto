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
            $userId = (int) $user['id'];
            $surveyDone = $this->isSurveyCompleted($userId);

            if (!$surveyDone) {
                $response->redirect('/take-survey');
                return false;
            }
        } catch (\Throwable $e) {
            // If DB error, let request proceed
        }

        return true;
    }

    private function isSurveyCompleted(int $userId): bool
    {
        $mainSurveyId = setting('main_survey_quiz_id');
        if (!$mainSurveyId) {
            return true;
        }

        $db = Database::connection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM cms.quiz_attempts WHERE quiz_id = :quiz_id AND user_id = :user_id AND status = 'submitted'");
        $stmt->execute([
            'quiz_id' => (int) $mainSurveyId,
            'user_id' => $userId
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function isQuestionsCompleted(int $userId): bool
    {
        $mainQuizId = setting('main_quiz_quiz_id');
        $mainOpenId = setting('main_open_quiz_id');

        $quizFinished = true;
        if ($mainQuizId) {
            $db = Database::connection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM cms.quiz_attempts WHERE quiz_id = :quiz_id AND user_id = :user_id AND status != 'in_progress'");
            $stmt->execute([
                'quiz_id' => (int) $mainQuizId,
                'user_id' => $userId
            ]);
            $quizFinished = (int) $stmt->fetchColumn() > 0;
        }

        $openFinished = true;
        if ($mainOpenId) {
            $db = Database::connection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM cms.quiz_attempts WHERE quiz_id = :quiz_id AND user_id = :user_id AND status != 'in_progress'");
            $stmt->execute([
                'quiz_id' => (int) $mainOpenId,
                'user_id' => $userId
            ]);
            $openFinished = (int) $stmt->fetchColumn() > 0;
        }

        return $quizFinished && $openFinished;
    }

    private function isPostSubmitted(int $userId): bool
    {
        $db = Database::connection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM cms.user_facebook_posts WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
