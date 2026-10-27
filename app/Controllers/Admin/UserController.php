<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\UserService;
use App\Repositories\QuizAttemptRepository;
use Core\Controller;
use Core\Csrf;
use Core\Validator;

final class UserController extends Controller
{
    private UserService $userService;
    private QuizAttemptRepository $attemptRepository;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->attemptRepository = new QuizAttemptRepository();
    }

    /**
     * Web Page: List all users.
     */
    public function index(): string
    {
        $request = app()->request();
        $search = trim((string)$request->input('search', ''));
        $role = trim((string)$request->input('role', ''));
        $facebookPost = trim((string)$request->input('facebook_post', ''));
        $page = $request->input('page') ? max(1, (int)$request->input('page')) : 1;
        $rawPerPage = (int)$request->input('per_page', 15);
        $allowedPerPage = [15, 25, 50, 100];
        $perPage = in_array($rawPerPage, $allowedPerPage, true) ? $rawPerPage : 15;

        $filters = [
            'search' => $search,
            'role' => $role,
            'facebook_post' => $facebookPost,
            'per_page' => $perPage,
        ];

        $result = $this->userService->getPaginatedUsers($filters, $page, $perPage);
        $totalPages = (int)ceil($result['total'] / $perPage);

        // If current page is beyond total pages after deletions, adjust to the last available page
        if ($page > 1 && empty($result['items']) && $result['total'] > 0) {
            $page = max(1, $totalPages);
            $result = $this->userService->getPaginatedUsers($filters, $page, $perPage);
        }

        return $this->view('admin/users/index', [
            'users' => $result['items'],
            'total' => $result['total'],
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'filters' => $filters,
        ]);
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
            $errors['username'][] = 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.';
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

        $attempts = $this->attemptRepository->findAttemptsByUserId((int) $id);

        return $this->view('admin/users/edit', [
            'user' => $user,
            'attempts' => $attempts,
        ]);
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
        $request = app()->request();
        Csrf::verify($request);

        $currentUser = app()->session()->get('user');
        if ($currentUser && (int) $currentUser['id'] === $userId) {
            app()->session()->flash('error', 'You cannot delete your own account.');
            $this->redirect($this->getRedirectUrlWithFilters($request));
        }

        $deleted = $this->userService->deleteUser($userId);

        if (!$deleted) {
            app()->session()->flash('error', 'Could not delete user. Please try again.');
        } else {
            app()->session()->flash('success', 'User and related data deleted successfully.');
        }

        $this->redirect($this->getRedirectUrlWithFilters($request));
    }

    /**
     * Web Page Action: Bulk delete users with related data.
     */
    public function bulkDestroy(): never
    {
        $request = app()->request();
        Csrf::verify($request);

        $rawIds = $request->input('user_ids');
        if (!is_array($rawIds)) {
            $rawIds = $rawIds !== null && $rawIds !== '' ? explode(',', (string) $rawIds) : [];
        }

        $userIds = array_values(array_unique(array_filter(array_map('intval', $rawIds), fn($id) => $id > 0)));

        if (empty($userIds)) {
            if ($request->expectsJson()) {
                app()->response()->json(['success' => false, 'message' => 'Please select at least one user to delete.'], 422);
            }
            app()->session()->flash('error', 'Please select at least one user to delete.');
            $this->redirect($this->getRedirectUrlWithFilters($request));
        }

        $currentUser = app()->session()->get('user');
        $currentUserId = $currentUser ? (int) $currentUser['id'] : 0;

        $hasSelf = in_array($currentUserId, $userIds, true);
        if ($hasSelf) {
            $userIds = array_values(array_diff($userIds, [$currentUserId]));
            if (empty($userIds)) {
                if ($request->expectsJson()) {
                    app()->response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
                }
                app()->session()->flash('error', 'You cannot delete your own account.');
                $this->redirect($this->getRedirectUrlWithFilters($request));
            }
        }

        try {
            $deletedCount = $this->userService->bulkDeleteUsers($userIds);

            $message = "{$deletedCount} user(s) and their related data deleted successfully.";
            if ($hasSelf) {
                $message .= ' (Your own account was skipped).';
            }

            if ($request->expectsJson()) {
                app()->response()->json([
                    'success' => true,
                    'message' => $message,
                    'deleted_count' => $deletedCount,
                ]);
            }

            app()->session()->flash('success', $message);
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                app()->response()->json(['success' => false, 'message' => 'Could not delete users: ' . $e->getMessage()], 500);
            }
            app()->session()->flash('error', 'Could not delete users. Please try again.');
        }

        $this->redirect($this->getRedirectUrlWithFilters($request));
    }

    /**
     * Build redirect URL for admin users preserving query parameters.
     */
    private function getRedirectUrlWithFilters(\Core\Request $request): string
    {
        $redirectParams = [];

        $search = trim((string)$request->input('search', ''));
        if ($search !== '') {
            $redirectParams['search'] = $search;
        }

        $role = trim((string)$request->input('role', ''));
        if ($role !== '') {
            $redirectParams['role'] = $role;
        }

        $facebookPost = trim((string)$request->input('facebook_post', ''));
        if ($facebookPost !== '') {
            $redirectParams['facebook_post'] = $facebookPost;
        }

        $perPage = (int)$request->input('per_page', 0);
        if (in_array($perPage, [15, 25, 50, 100], true)) {
            $redirectParams['per_page'] = $perPage;
        }

        $page = (int)$request->input('page', 1);
        if ($page > 1) {
            $redirectParams['page'] = $page;
        }

        return '/admin/users' . (!empty($redirectParams) ? '?' . http_build_query($redirectParams) : '');
    }
}
