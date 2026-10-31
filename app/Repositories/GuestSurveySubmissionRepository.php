<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GuestSurveySubmission;
use Core\Database;
use Core\Model;

final class GuestSurveySubmissionRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new GuestSurveySubmission();
    }

    /**
     * Create a guest survey submission record.
     *
     * @param string $token
     * @param int $quizId
     * @param string|null $travelStatus
     * @param array $answersPayload
     * @param int $expiresInHours
     * @return int
     */
    public function createSubmission(
        string $token,
        int $quizId,
        ?string $travelStatus,
        array $answersPayload,
        int $expiresInHours = 48
    ): int {
        $db = Database::connection();
        $sql = "INSERT INTO cms.guest_survey_submissions (
            guest_token,
            quiz_id,
            travel_status,
            answers_payload,
            created_at,
            expires_at
        ) VALUES (
            :guest_token,
            :quiz_id,
            :travel_status,
            :answers_payload,
            CURRENT_TIMESTAMP,
            CURRENT_TIMESTAMP + (:expires_hours || ' hours')::INTERVAL
        ) RETURNING id";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'guest_token'     => $token,
            'quiz_id'         => $quizId,
            'travel_status'   => $travelStatus,
            'answers_payload' => json_encode($answersPayload, JSON_UNESCAPED_UNICODE),
            'expires_hours'   => $expiresInHours,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Find a guest submission by token.
     *
     * @param string $token
     * @return array|null
     */
    public function findByToken(string $token): ?array
    {
        $db = Database::connection();
        $sql = "SELECT * FROM cms.guest_survey_submissions 
                WHERE guest_token = :guest_token 
                AND (expires_at IS NULL OR expires_at > CURRENT_TIMESTAMP)
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute(['guest_token' => $token]);
        $row = $stmt->fetch();

        if ($row && isset($row['answers_payload']) && is_string($row['answers_payload'])) {
            $row['answers_payload'] = json_decode($row['answers_payload'], true) ?: [];
        }

        return $row ?: null;
    }

    /**
     * Delete a guest submission by token.
     *
     * @param string $token
     * @return bool
     */
    public function deleteByToken(string $token): bool
    {
        $db = Database::connection();
        $sql = "DELETE FROM cms.guest_survey_submissions WHERE guest_token = :guest_token";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['guest_token' => $token]);
    }

    /**
     * Find a guest submission by its primary key ID.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $db = Database::connection();
        $sql = "SELECT gss.*, q.title as quiz_title
                FROM cms.guest_survey_submissions gss
                LEFT JOIN cms.quizzes q ON gss.quiz_id = q.id
                WHERE gss.id = :id
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row && isset($row['answers_payload']) && is_string($row['answers_payload'])) {
            $row['answers_payload'] = json_decode($row['answers_payload'], true) ?: [];
        }

        return $row ?: null;
    }

    /**
     * Delete a guest submission by its primary key ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteById(int $id): bool
    {
        $db = Database::connection();
        $sql = "DELETE FROM cms.guest_survey_submissions WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Bulk delete guest submissions by an array of IDs.
     *
     * @param array $ids
     * @return int Number of deleted rows
     */
    public function bulkDeleteByIds(array $ids): int
    {
        $cleanIds = array_values(array_unique(array_filter(array_map('intval', $ids), fn($id) => $id > 0)));
        if (empty($cleanIds)) {
            return 0;
        }

        $db = Database::connection();
        $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
        $stmt = $db->prepare("DELETE FROM cms.guest_survey_submissions WHERE id IN ($placeholders)");
        $stmt->execute($cleanIds);
        return $stmt->rowCount();
    }

    /**
     * Get paginated guest submissions with optional search and date filters.
     *
     * @param int $quizId
     * @param string|null $search
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getSubmissions(
        int $quizId,
        ?string $search = null,
        ?string $startDate = null,
        ?string $endDate = null,
        int $limit = 20,
        int $offset = 0
    ): array {
        $db = Database::connection();
        $conditions = ["gss.quiz_id = :quiz_id"];
        $params = ['quiz_id' => $quizId];

        if ($search !== null && $search !== '') {
            $conditions[] = "(gss.guest_token ILIKE :search OR gss.travel_status ILIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($startDate !== null && $startDate !== '') {
            $conditions[] = "gss.created_at >= :start_date";
            $params['start_date'] = $startDate . ' 00:00:00';
        }

        if ($endDate !== null && $endDate !== '') {
            $conditions[] = "gss.created_at <= :end_date";
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT gss.*, q.title as quiz_title
                FROM cms.guest_survey_submissions gss
                LEFT JOIN cms.quizzes q ON gss.quiz_id = q.id
                WHERE {$whereClause}
                ORDER BY gss.id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll() ?: [];
        foreach ($rows as &$row) {
            if (isset($row['answers_payload']) && is_string($row['answers_payload'])) {
                $row['answers_payload'] = json_decode($row['answers_payload'], true) ?: [];
            }
        }

        return $rows;
    }

    /**
     * Count guest submissions matching filters.
     *
     * @param int $quizId
     * @param string|null $search
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function countSubmissions(
        int $quizId,
        ?string $search = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): int {
        $db = Database::connection();
        $conditions = ["quiz_id = :quiz_id"];
        $params = ['quiz_id' => $quizId];

        if ($search !== null && $search !== '') {
            $conditions[] = "(guest_token ILIKE :search OR travel_status ILIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($startDate !== null && $startDate !== '') {
            $conditions[] = "created_at >= :start_date";
            $params['start_date'] = $startDate . ' 00:00:00';
        }

        if ($endDate !== null && $endDate !== '') {
            $conditions[] = "created_at <= :end_date";
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT COUNT(*) FROM cms.guest_survey_submissions WHERE {$whereClause}";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Delete expired guest submissions.
     *
     * @return int Number of deleted rows
     */
    public function deleteExpired(): int
    {
        $db = Database::connection();
        $sql = "DELETE FROM cms.guest_survey_submissions WHERE expires_at IS NOT NULL AND expires_at <= CURRENT_TIMESTAMP";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }
}

