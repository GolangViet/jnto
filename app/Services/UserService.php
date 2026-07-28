<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

final class UserService
{
    private UserRepository $userRepository;

    /**
     * Initialize the UserService and inject UserRepository.
     */
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return array|null The user record or null if not found.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Create a new user, hashing their password.
     *
     * @param array $data Contains name, email, password.
     * @return bool True on success, false on failure.
     */
    public function createUser(array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->userRepository->create($data);
    }
}
