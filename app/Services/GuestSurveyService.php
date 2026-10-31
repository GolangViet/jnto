<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\GuestSurveySubmissionRepository;
use Core\Database;

final class GuestSurveyService
{
    private GuestSurveySubmissionRepository $repository;
    private QuizAttemptService $attemptService;

    public function __construct()
    {
        $this->repository = new GuestSurveySubmissionRepository();
        $this->attemptService = new QuizAttemptService();
    }

    /**
     * Save a guest survey submission to the temporary staging table.
     *
     * @param int $quizId
     * @param string|null $travelStatus
     * @param array $answers
     * @return string Generated guest token
     */
    public function saveGuestSubmission(int $quizId, ?string $travelStatus, array $answers): string
    {
        $token = bin2hex(random_bytes(32));

        $this->repository->createSubmission(
            $token,
            $quizId,
            $travelStatus,
            $answers,
            48 // expires in 48 hours
        );

        app()->session()->put('guest_survey_token', $token);

        return $token;
    }

    /**
     * Check if a guest survey token exists in session or request.
     *
     * @param string|null $token
     * @return bool
     */
    public function hasGuestSubmission(?string $token = null): bool
    {
        $effectiveToken = $token ?: app()->session()->get('guest_survey_token');
        if (!$effectiveToken || !is_string($effectiveToken)) {
            return false;
        }

        return $this->repository->findByToken($effectiveToken) !== null;
    }

    /**
     * Claim and convert a guest submission into official quiz attempt records for a user.
     *
     * @param int $userId
     * @param string|null $token
     * @return bool True if claimed or already completed, false if no submission found
     */
    public function claimGuestSubmissionForUser(int $userId, ?string $token = null): bool
    {
        $effectiveToken = $token ?: app()->session()->get('guest_survey_token');
        if (!$effectiveToken || !is_string($effectiveToken)) {
            return false;
        }

        $submission = $this->repository->findByToken($effectiveToken);
        if (!$submission) {
            app()->session()->forget('guest_survey_token');
            return false;
        }

        $quizId = (int) $submission['quiz_id'];

        // If user already completed this survey, clean up staging record and return
        if ($this->isSurveyCompleted($userId, $quizId)) {
            $this->repository->deleteByToken($effectiveToken);
            app()->session()->forget('guest_survey_token');
            return true;
        }

        $answers = $submission['answers_payload'] ?? [];

        try {
            $attemptId = $this->attemptService->startAttempt($quizId, $userId);

            if (is_array($answers)) {
                foreach ($answers as $ans) {
                    if (is_array($ans) && !empty($ans['question_id'])) {
                        try {
                            $this->attemptService->saveAnswer($attemptId, $userId, $ans);
                        } catch (\Throwable $e) {
                            error_log("Failed to save answer for guest claim: " . $e->getMessage());
                        }
                    }
                }
            }

            $this->attemptService->submitAttempt($attemptId, $userId);

            // Successfully claimed, clean up staging record
            $this->repository->deleteByToken($effectiveToken);
            app()->session()->forget('guest_survey_token');

            return true;
        } catch (\Throwable $e) {
            error_log("Failed to claim guest survey for user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a user already submitted an attempt for a quiz.
     */
    private function isSurveyCompleted(int $userId, int $quizId): bool
    {
        $db = Database::connection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM cms.quiz_attempts WHERE quiz_id = :quiz_id AND user_id = :user_id AND status = 'submitted'");
        $stmt->execute([
            'quiz_id' => $quizId,
            'user_id' => $userId,
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }
}

