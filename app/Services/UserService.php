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
     * Find a user by their username.
     *
     * @param string $username
     * @return array|null The user record or null if not found.
     */
    public function findByUsername(string $username): ?array
    {
        return $this->userRepository->findByUsername($username);
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
     * @param array $data Contains username, name, email, password.
     * @return bool True on success, false on failure.
     */
    public function createUser(array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->userRepository->create($data);
    }

    /**
     * Get all users.
     *
     * @return array List of all users.
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->getAllWithFacebookPost();
    }

    /**
     * Get a user by ID.
     *
     * @param int $id User ID.
     * @return array|null The user record or null if not found.
     */
    public function getUserById(int $id): ?array
    {
        return $this->userRepository->find($id);
    }

    /**
     * Update an existing user.
     *
     * @param int $id User ID.
     * @param array $data Contains keys: username, name, email, role, and optionally password.
     * @return bool True on success, false on failure.
     */
    public function updateUser(int $id, array $data): bool
    {
        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        return $this->userRepository->update($id, $data);
    }

    /**
     * Delete a user.
     *
     * @param int $id User ID.
     * @return bool True on success, false on failure.
     */
    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }
}

