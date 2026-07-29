<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Quiz;
use Core\Model;
use Core\Database;

final class QuizRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new Quiz();
    }

    /**
     * Create a new quiz.
     *
     * @param array $data
     * @return int Inserted quiz ID
     */
    public function create(array $data): int
    {
        $db = Database::connection();
        $sql = "INSERT INTO cms.quizzes (
            title, description, status, duration_minutes, pass_score,
            show_result, show_correct_answer, allow_resume, start_at, end_at, created_by
        ) VALUES (
            :title, :description, :status, :duration_minutes, :pass_score,
            :show_result, :show_correct_answer, :allow_resume, :start_at, :end_at, :created_by
        ) RETURNING id";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'duration_minutes' => isset($data['duration_minutes']) && $data['duration_minutes'] !== '' ? (int) $data['duration_minutes'] : null,
            'pass_score' => isset($data['pass_score']) && $data['pass_score'] !== '' ? (float) $data['pass_score'] : null,
            'show_result' => isset($data['show_result']) ? (bool) $data['show_result'] : true,
            'show_correct_answer' => isset($data['show_correct_answer']) ? (bool) $data['show_correct_answer'] : true,
            'allow_resume' => isset($data['allow_resume']) ? (bool) $data['allow_resume'] : true,
            'start_at' => !empty($data['start_at']) ? $data['start_at'] : null,
            'end_at' => !empty($data['end_at']) ? $data['end_at'] : null,
            'created_by' => isset($data['created_by']) ? (int) $data['created_by'] : null,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Update an existing quiz.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $db = Database::connection();
        $sql = "UPDATE cms.quizzes SET
            title = :title,
            description = :description,
            status = :status,
            duration_minutes = :duration_minutes,
            pass_score = :pass_score,
            show_result = :show_result,
            show_correct_answer = :show_correct_answer,
            allow_resume = :allow_resume,
            start_at = :start_at,
            end_at = :end_at,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'duration_minutes' => isset($data['duration_minutes']) && $data['duration_minutes'] !== '' ? (int) $data['duration_minutes'] : null,
            'pass_score' => isset($data['pass_score']) && $data['pass_score'] !== '' ? (float) $data['pass_score'] : null,
            'show_result' => isset($data['show_result']) ? (bool) $data['show_result'] : true,
            'show_correct_answer' => isset($data['show_correct_answer']) ? (bool) $data['show_correct_answer'] : true,
            'allow_resume' => isset($data['allow_resume']) ? (bool) $data['allow_resume'] : true,
            'start_at' => !empty($data['start_at']) ? $data['start_at'] : null,
            'end_at' => !empty($data['end_at']) ? $data['end_at'] : null,
        ]);
    }

    /**
     * Get all published quizzes.
     *
     * @return array
     */
    public function getPublished(): array
    {
        $db = Database::connection();
        // A quiz is active/published if status = 'published' AND (start_at IS NULL OR start_at <= NOW()) AND (end_at IS NULL OR end_at >= NOW())
        $sql = "SELECT q.*, 
                (SELECT COUNT(*) FROM cms.questions WHERE quiz_id = q.id) as questions_count
                FROM cms.quizzes q
                WHERE q.status = 'published'
                AND (q.start_at IS NULL OR q.start_at <= CURRENT_TIMESTAMP)
                AND (q.end_at IS NULL OR q.end_at >= CURRENT_TIMESTAMP)
                ORDER BY q.id DESC";
        return $db->query($sql)->fetchAll();
    }

    /**
     * Get list of all quizzes with questions and attempts statistics for Admin dashboard.
     *
     * @return array
     */
    public function getQuizzesWithStats(): array
    {
        $db = Database::connection();
        $sql = "SELECT q.*,
                (SELECT COUNT(*) FROM cms.questions WHERE quiz_id = q.id) as questions_count,
                (SELECT COUNT(*) FROM cms.quiz_attempts WHERE quiz_id = q.id) as attempts_count
                FROM cms.quizzes q
                ORDER BY q.id DESC";
        return $db->query($sql)->fetchAll();
    }
}
