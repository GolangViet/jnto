<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Csrf;
use Core\Validator;
use App\Services\UserService;

final class AuthController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Show the login form.
     *
     * @return string Rendered login page HTML.
     */
    public function showLogin(): string
    {
        return $this->view('auth/login');
    }

    /**
     * Handle a login request.
     *
     * Validates credentials and sets the authenticated user in session on success.
     *
     * @return never Redirects on success or failure.
     */
    public function login(): never
    {
        $request = app()->request();
        Csrf::verify($request);
        $data = $request->only(['username', 'password']);
        $validator = new Validator();
        if (!$validator->validate($data, ['username' => 'required|min:3', 'password' => 'required|min:6'])) {
            app()->session()->flash('errors', $validator->errors());
            app()->session()->flashOldInput($data);
            $this->redirect('/login');
        }

        $user = $this->userService->findByUsername($data['username']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            app()->session()->flash('error', 'Invalid username or password.');
            $this->redirect('/login');
        }

        if (($user['role'] ?? 'user') === 'admin') {
            app()->session()->flash('error', 'Admins must use the admin login portal.');
            $this->redirect('/login');
        }

        app()->session()->put('user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'] ?? 'user',
        ]);

        $this->redirect('/');
    }

    /**
     * Show the admin login form.
     *
     * @return string Rendered login page HTML.
     */
    public function showAdminLogin(): string
    {
        return $this->view('admin/auth/login');
    }

    /**
     * Handle an admin login request.
     *
     * @return never Redirects on success or failure.
     */
    public function adminLogin(): never
    {
        $request = app()->request();
        Csrf::verify($request);
        $data = $request->only(['username', 'password']);
        $validator = new Validator();
        if (!$validator->validate($data, ['username' => 'required|min:3', 'password' => 'required|min:6'])) {
            app()->session()->flash('errors', $validator->errors());
            app()->session()->flashOldInput($data);
            $this->redirect('/admin/login');
        }

        $user = $this->userService->findByUsername($data['username']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            app()->session()->flash('error', 'Invalid username or password.');
            $this->redirect('/admin/login');
        }

        if (($user['role'] ?? 'user') !== 'admin') {
            app()->session()->flash('error', 'Access denied. Administrators only.');
            $this->redirect('/admin/login');
        }

        app()->session()->put('user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => 'admin',
        ]);

        $this->redirect('/admin/posts');
    }

    /**
     * Log out the current user and redirect to home.
     *
     * @return never Redirects after logging out.
     */
    public function logout(): never
    {
        Csrf::verify(app()->request());
        app()->session()->forget('user');
        $this->redirect('/');
    }
}
