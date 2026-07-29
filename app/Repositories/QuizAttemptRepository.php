<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\QuizAttempt;
use Core\Model;
use Core\Database;

final class QuizAttemptRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new QuizAttempt();
    }

    /**
     * Create a new attempt record.
     *
     * @param int $quizId
     * @param int $userId
     * @return int Created attempt ID
     */
    public function createAttempt(int $quizId, int $userId): int
    {
        $db = Database::connection();
        $sql = "INSERT INTO cms.quiz_attempts (
            quiz_id, user_id, status, started_at
        ) VALUES (
            :quiz_id, :user_id, 'in_progress', CURRENT_TIMESTAMP
        ) RETURNING id";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'quiz_id' => $quizId,
            'user_id' => $userId,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Find active in-progress attempt for a user on a specific quiz.
     *
     * @param int $quizId
     * @param int $userId
     * @return array|null
     */
    public function findActiveAttempt(int $quizId, int $userId): ?array
    {
        $db = Database::connection();
        $sql = "SELECT * FROM cms.quiz_attempts 
                WHERE quiz_id = :quiz_id 
                AND user_id = :user_id 
                AND status = 'in_progress' 
                ORDER BY id DESC LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'quiz_id' => $quizId,
            'user_id' => $userId,
        ]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Find attempt by ID, including quiz and user details.
     *
     * @param int $attemptId
     * @return array|null
     */
    public function findAttempt(int $attemptId): ?array
    {
        $db = Database::connection();
        $sql = "SELECT qa.*, 
                q.title as quiz_title, q.duration_minutes, q.pass_score, 
                q.show_result, q.show_correct_answer, q.allow_resume,
                u.name as user_name, u.email as user_email
                FROM cms.quiz_attempts qa
                JOIN cms.quizzes q ON qa.quiz_id = q.id
                LEFT JOIN cms.users u ON qa.user_id = u.id
                WHERE qa.id = :id LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $attemptId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Save an attempt's answer.
     *
     * @param int $attemptId
     * @param int $questionId
     * @param string|null $answerText
     * @param bool|null $isCorrect
     * @param float $awardedScore
     * @return int The ID of the saved answer row
     */
    public function saveAttemptAnswer(int $attemptId, int $questionId, ?string $answerText, ?bool $isCorrect, float $awardedScore): int
    {
        $db = Database::connection();
        
        $checkStmt = $db->prepare("SELECT id FROM cms.quiz_attempt_answers WHERE attempt_id = :attempt_id AND question_id = :question_id");
        $checkStmt->execute([
            'attempt_id' => $attemptId,
            'question_id' => $questionId,
        ]);
        
        $id = $checkStmt->fetchColumn();

        if ($id !== false) {
            $sql = "UPDATE cms.quiz_attempt_answers SET
                answer_text = :answer_text,
                is_correct = :is_correct,
                awarded_score = :awarded_score,
                answered_at = CURRENT_TIMESTAMP,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id' => (int) $id,
                'answer_text' => $answerText,
                'is_correct' => $isCorrect === null ? null : ($isCorrect ? 1 : 0),
                'awarded_score' => $awardedScore,
            ]);
            
            return (int) $id;
        } else {
            $sql = "INSERT INTO cms.quiz_attempt_answers (
                attempt_id, question_id, answer_text, is_correct, awarded_score, answered_at
            ) VALUES (
                :attempt_id, :question_id, :answer_text, :is_correct, :awarded_score, CURRENT_TIMESTAMP
            ) RETURNING id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'attempt_id' => $attemptId,
                'question_id' => $questionId,
                'answer_text' => $answerText,
                'is_correct' => $isCorrect === null ? null : ($isCorrect ? 1 : 0),
                'awarded_score' => $awardedScore,
            ]);
            
            return (int) $stmt->fetchColumn();
        }
    }

    /**
     * Save the selected options for a quiz attempt answer.
     *
     * @param int $attemptAnswerId
     * @param array $optionIds
     * @return void
     */
    public function saveAttemptAnswerOptions(int $attemptAnswerId, array $optionIds): void
    {
        $db = Database::connection();
        
        // Clear previous selection
        $deleteStmt = $db->prepare("DELETE FROM cms.quiz_attempt_answer_options WHERE attempt_answer_id = :attempt_answer_id");
        $deleteStmt->execute(['attempt_answer_id' => $attemptAnswerId]);

        if (empty($optionIds)) {
            return;
        }

        $insertStmt = $db->prepare("INSERT INTO cms.quiz_attempt_answer_options (attempt_answer_id, option_id) VALUES (:attempt_answer_id, :option_id)");
        foreach ($optionIds as $optionId) {
            $insertStmt->execute([
                'attempt_answer_id' => $attemptAnswerId,
                'option_id' => (int) $optionId,
            ]);
        }
    }

    /**
     * Get all saved answers for an attempt.
     *
     * @param int $attemptId
     * @return array
     */
    public function getAttemptAnswers(int $attemptId): array
    {
        $db = Database::connection();
        $sql = "SELECT qaa.*,
                COALESCE(
                    (SELECT json_agg(option_id) FROM cms.quiz_attempt_answer_options WHERE attempt_answer_id = qaa.id),
                    '[]'::json
                ) as selected_option_ids
                FROM cms.quiz_attempt_answers qaa
                WHERE qaa.attempt_id = :attempt_id";

        $stmt = $db->prepare($sql);
        $stmt->execute(['attempt_id' => $attemptId]);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $row['selected_option_ids'] = json_decode((string) $row['selected_option_ids'], true) ?: [];
        }

        return $rows;
    }

    /**
     * Update attempt with submission scores and marks.
     *
     * @param int $attemptId
     * @param array $data
     * @return bool
     */
    public function submitAttempt(int $attemptId, array $data): bool
    {
        $db = Database::connection();
        $sql = "UPDATE cms.quiz_attempts SET
            status = :status,
            score = :score,
            total_score = :total_score,
            percentage = :percentage,
            passed = :passed,
            submitted_at = CURRENT_TIMESTAMP,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id' => $attemptId,
            'status' => $data['status'] ?? 'submitted',
            'score' => (float) $data['score'],
            'total_score' => (float) $data['total_score'],
            'percentage' => (float) $data['percentage'],
            'passed' => $data['passed'] ? 1 : 0,
        ]);
    }
}
