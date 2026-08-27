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
}


