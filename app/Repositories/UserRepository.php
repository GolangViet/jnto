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
     * @param array $data Contains keys: name, email, password.
     * @return bool True on success, false on failure.
     */
    public function create(array $data): bool
    {
        /** @var User $userModel */
        $userModel = $this->model;
        return $userModel->create($data);
    }
}
