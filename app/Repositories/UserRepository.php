<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Core\Model;

final class UserRepository extends BaseRepository
{
    /**
     * Get the model instance associated with the repository.
     *
     * @return Model
     */
    protected function getModel(): Model
    {
        return new User();
    }

    /**
     * Find a user by their username.
     *
     * @param string $username
     * @return array|null The user as an associative array, or null if not found.
     */
    public function findByUsername(string $username): ?array
    {
        /** @var User $userModel */
        $userModel = $this->model;
        return $userModel->findByUsername($username);
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return array|null The user as an associative array, or null if not found.
     */
    public function findByEmail(string $email): ?array
    {
        /** @var User $userModel */
        $userModel = $this->model;
        return $userModel->findByEmail($email);
    }

    /**
     * Create a new user record.
     *
     * @param array $data Contains keys: username, name, email, password.
     * @return bool True on success, false on failure.
     */
    public function create(array $data): bool
    {
        /** @var User $userModel */
        $userModel = $this->model;
        return $userModel->create($data);
    }

    /**
     * Update an existing user record.
     *
     * @param int $id User identifier.
     * @param array $data Updated user data.
     * @return bool True on success, false on failure.
     */
    public function update(int $id, array $data): bool
    {
        /** @var User $userModel */
        $userModel = $this->model;
        return $userModel->update($id, $data);
    }

    /**
     * Get all users with their submitted Facebook posts.
     *
     * @return array
     */
    public function getAllWithFacebookPost(): array
    {
        $db = \Core\Database::connection();
        $sql = "SELECT u.*, fp.facebook_url, fp.score AS facebook_score 
                FROM cms.users u
                LEFT JOIN cms.user_facebook_posts fp ON u.id = fp.user_id
                ORDER BY u.id ASC";
        return $db->query($sql)->fetchAll() ?: [];
    }

    /**
     * Get paginated users with facebook post details and optional filters.
     *
     * @param array $filters Supported keys: 'search', 'role', 'facebook_post'
     * @param int $page Current page number
     * @param int $perPage Records per page
     * @return array Contains 'items' (array) and 'total' (int)
     */
    public function getPaginatedWithFacebookPost(array $filters, int $page = 1, int $perPage = 15): array
    {
        $db = \Core\Database::connection();
        $whereClauses = [];
        $params = [];

        if (!empty($filters['search'])) {
            $whereClauses[] = "(u.username ILIKE :search OR u.name ILIKE :search OR u.email ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['role'])) {
            $whereClauses[] = "u.role = :role";
            $params['role'] = $filters['role'];
        }

        if (isset($filters['facebook_post']) && $filters['facebook_post'] !== '') {
            if ($filters['facebook_post'] === 'yes') {
                $whereClauses[] = "fp.facebook_url IS NOT NULL AND fp.facebook_url != ''";
            } elseif ($filters['facebook_post'] === 'no') {
                $whereClauses[] = "(fp.facebook_url IS NULL OR fp.facebook_url = '')";
            }
        }

        $whereSql = '';
        if (count($whereClauses) > 0) {
            $whereSql = "WHERE " . implode(" AND ", $whereClauses);
        }

        // Count Query
        $countSql = "SELECT COUNT(u.id) 
                     FROM cms.users u
                     LEFT JOIN cms.user_facebook_posts fp ON u.id = fp.user_id
                     $whereSql";
        
        $stmtCount = $db->prepare($countSql);
        $stmtCount->execute($params);
        $totalCount = (int)$stmtCount->fetchColumn();

        // Data Query
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT u.*, fp.facebook_url, fp.score AS facebook_score 
                FROM cms.users u
                LEFT JOIN cms.user_facebook_posts fp ON u.id = fp.user_id
                $whereSql
                ORDER BY u.id ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll() ?: [];

        return [
            'items' => $items,
            'total' => $totalCount,
        ];
    }

    /**
     * Delete multiple users and all their related data within a database transaction.
     * Related data includes:
     * - cms.user_facebook_posts (user_id)
     * - cms.quiz_attempts (user_id) -> cascades to answers & answer options
     * - cms.quizzes (created_by disassociated to NULL)
     *
     * @param array<int> $userIds
     * @return int Number of users deleted
     * @throws \Throwable
     */
    public function bulkDeleteWithRelatedData(array $userIds): int
    {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds), fn($id) => $id > 0)));
        if (empty($userIds)) {
            return 0;
        }

        $db = \Core\Database::connection();
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));

        $db->beginTransaction();
        try {
            // 1. Delete user facebook posts
            $stmtFp = $db->prepare("DELETE FROM cms.user_facebook_posts WHERE user_id IN ($placeholders)");
            $stmtFp->execute($userIds);

            // 2. Delete quiz attempts (cascades to answers and options)
            $stmtAttempts = $db->prepare("DELETE FROM cms.quiz_attempts WHERE user_id IN ($placeholders)");
            $stmtAttempts->execute($userIds);

            // 3. Clear quiz creator references so quizzes are not orphaned or deleted
            $stmtQuizzes = $db->prepare("UPDATE cms.quizzes SET created_by = NULL WHERE created_by IN ($placeholders)");
            $stmtQuizzes->execute($userIds);

            // 4. Delete the users
            $stmtUsers = $db->prepare("DELETE FROM cms.users WHERE id IN ($placeholders)");
            $stmtUsers->execute($userIds);
            $deletedCount = $stmtUsers->rowCount();

            $db->commit();
            return $deletedCount;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Delete a single user and their related data.
     *
     * @param int $id
     * @return bool
     */
    public function deleteWithRelatedData(int $id): bool
    {
        return $this->bulkDeleteWithRelatedData([$id]) > 0;
    }
}


