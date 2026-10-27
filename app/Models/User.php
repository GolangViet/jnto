<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

final class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected string $table = 'cms.users';

    /**
     * Find a user by their username.
     *
     * @param string $username
     * @return array|null The user as an associative array, or null if not found.
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return array|null The user as an associative array, or null if not found.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Create a new user record.
     *
     * @param array $data Contains keys: username, name, email, password.
     * @return bool True on success, false on failure.
     */
    public function create(array $data): bool
    {
        $data['role'] = $data['role'] ?? 'user';
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (username, name, email, password, role) VALUES (:username, :name, :email, :password, :role)"
        );

        return $stmt->execute($data);
    }

    /**
     * Update an existing user record.
     *
     * @param int $id User identifier.
     * @param array $data Contains keys: username, name, email, role, and optionally password.
     * @return bool True on success, false on failure.
     */
    public function update(int $id, array $data): bool
    {
        if (isset($data['password']) && $data['password'] !== '') {
            $stmt = $this->db->prepare(
                "UPDATE {$this->table} SET username = :username, name = :name, email = :email, role = :role, password = :password, updated_at = CURRENT_TIMESTAMP WHERE id = :id"
            );
        } else {
            unset($data['password']);
            $stmt = $this->db->prepare(
                "UPDATE {$this->table} SET username = :username, name = :name, email = :email, role = :role, updated_at = CURRENT_TIMESTAMP WHERE id = :id"
            );
        }

        return $stmt->execute($data + ['id' => $id]);
    }

    /**
     * Delete multiple users by their primary keys.
     *
     * @param array<int> $ids
     * @return int Number of deleted rows.
     */
    public function deleteByIds(array $ids): int
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn($id) => $id > 0)));
        if (empty($ids)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id IN ($placeholders)");
        $stmt->execute($ids);

        return $stmt->rowCount();
    }
}

