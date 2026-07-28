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
     * @param array $data Contains keys: name, email, password.
     * @return bool True on success, false on failure.
     */
    public function create(array $data): bool
    {
        $data['role'] = $data['role'] ?? 'user';
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (name, email, password, role) VALUES (:name, :email, :password, :role)"
        );

        return $stmt->execute($data);
    }
}
