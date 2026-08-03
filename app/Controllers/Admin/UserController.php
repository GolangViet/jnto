<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\UserService;
use Core\Controller;
use Core\Csrf;
use Core\Validator;

final class UserController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Web Page: List all users.
     */
    public function index(): string
    {
        $users = $this->userService->getAllUsers();
        return $this->view('admin/users/index', ['users' => $users]);
    }

    /**
     * Web Page: Create user form.
     */
    public function create(): string
    {
        return $this->view('admin/users/create');
    }

    /**
     * Web Page Action: Store new user.
     */
    public function store(): never
    {
        $request = app()->request();
        Csrf::verify($request);

        $data = $request->only(['username', 'name', 'email', 'role', 'password', 'password_confirmation']);

        $validator = new Validator();
        $rules = [
            'username' => 'required|min:3',
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
            'password' => 'required|min:6'
        ];

        if (!$validator->validate($data, $rules)) {
            app()->session()->flash('errors', $validator->errors());
            app()->session()->flashOldInput($data);
            $this->redirect('/admin/users/create');
        }

        $errors = [];
        if ($data['password'] !== $data['password_confirmation']) {
            $errors['password_confirmation'][] = 'Password confirmation does not match.';
        }

        if ($this->userService->findByUsername($data['username'])) {
            $errors['username'][] = 'Username is already taken.';
        }

        if ($this->userService->findByEmail($data['email'])) {
            $errors['email'][] = 'Email is already taken.';
        }

        if (!empty($errors)) {
            app()->session()->flash('errors', $errors);
            app()->session()->flashOldInput($data);
            $this->redirect('/admin/users/create');
        }

        $created = $this->userService->createUser([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => $data['password'],
        ]);

        if (!$created) {
            app()->session()->flash('error', 'Could not create user. Please try again.');
            app()->session()->flashOldInput($data);
            $this->redirect('/admin/users/create');
        }

        app()->session()->flash('success', 'User created successfully.');
        $this->redirect('/admin/users');
    }

    /**
     * Web Page: Edit user form.
     */
    public function edit(string $id): string
    {
        $user = $this->userService->getUserById((int) $id);
        if (!$user) {
            http_response_code(404);
            return 'User not found.';
        }

        return $this->view('admin/users/edit', ['user' => $user]);
    }

    /**
     * Web Page Action: Update user.
     */
    public function update(string $id): never
    {
        $userId = (int) $id;
        $request = app()->request();
        Csrf::verify($request);

        $user = $this->userService->getUserById($userId);
        if (!$user) {
            app()->session()->flash('error', 'User not found.');
            $this->redirect('/admin/users');
        }

        $data = $request->only(['username', 'name', 'email', 'role', 'password', 'password_confirmation']);

        $validator = new Validator();
        $rules = [
            'username' => 'required|min:3',
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
        ];

        // Only validate password length if a password was actually typed
        if (!empty($data['password'])) {
            $rules['password'] = 'min:6';
        }

        if (!$validator->validate($data, $rules)) {
            app()->session()->flash('errors', $validator->errors());
            app()->session()->flashOldInput($data);
            $this->redirect("/admin/users/{$userId}/edit");
        }

        $errors = [];
        if (!empty($data['password']) && $data['password'] !== $data['password_confirmation']) {
            $errors['password_confirmation'][] = 'Password confirmation does not match.';
        }

        $existingUserByUsername = $this->userService->findByUsername($data['username']);
        if ($existingUserByUsername && (int) $existingUserByUsername['id'] !== $userId) {
            $errors['username'][] = 'Username is already taken by another user.';
        }

        $existingUserByEmail = $this->userService->findByEmail($data['email']);
        if ($existingUserByEmail && (int) $existingUserByEmail['id'] !== $userId) {
            $errors['email'][] = 'Email is already taken by another user.';
        }

        if (!empty($errors)) {
            app()->session()->flash('errors', $errors);
            app()->session()->flashOldInput($data);
            $this->redirect("/admin/users/{$userId}/edit");
        }

        $updateData = [
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $updated = $this->userService->updateUser($userId, $updateData);

        if (!$updated) {
            app()->session()->flash('error', 'Could not update user. Please try again.');
            app()->session()->flashOldInput($data);
            $this->redirect("/admin/users/{$userId}/edit");
        }

        // If currently logged-in user updates their own profile, update session info
        $currentUser = app()->session()->get('user');
        if ($currentUser && (int) $currentUser['id'] === $userId) {
            app()->session()->put('user', [
                'id' => $userId,
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
            ]);
        }

        app()->session()->flash('success', 'User updated successfully.');
        $this->redirect('/admin/users');
    }

    /**
     * Web Page Action: Delete user.
     */
    public function destroy(string $id): never
    {
        $userId = (int) $id;
        Csrf::verify(app()->request());

        $currentUser = app()->session()->get('user');
        if ($currentUser && (int) $currentUser['id'] === $userId) {
            app()->session()->flash('error', 'You cannot delete your own account.');
            $this->redirect('/admin/users');
        }

        $deleted = $this->userService->deleteUser($userId);

        if (!$deleted) {
            app()->session()->flash('error', 'Could not delete user. Please try again.');
        } else {
            app()->session()->flash('success', 'User deleted successfully.');
        }

        $this->redirect('/admin/users');
    }
}
