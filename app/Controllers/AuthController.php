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
     * Show the registration form.
     *
     * @return string Rendered registration page HTML.
     */
    public function showRegister(): string
    {
        return $this->view('auth/register');
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
        $isJson = $request->expectsJson();
        if (!$validator->validate($data, ['username' => 'required|min:3', 'password' => 'required|min:6'])) {
            if ($isJson) {
                app()->response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Thông tin đăng nhập không hợp lệ.',
                ], 422);
            }

            app()->session()->flash('errors', $validator->errors());
            app()->session()->flashOldInput($data);
            $this->redirect('/login');
        }

        $user = $this->userService->findByUsername($data['username']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $errorMsg = "Tên đăng nhập / Mật khẩu không đúng \nVui lòng thử lại!";
            if ($isJson) {
                app()->response()->json(['message' => $errorMsg], 401);
            }

            app()->session()->flash('error', $errorMsg);
            $this->redirect('/login');
        }

        if (($user['role'] ?? 'user') === 'admin') {
            $errorMsg = 'Admins must use the admin login portal.';
            if ($isJson) {
                app()->response()->json(['message' => $errorMsg], 403);
            }

            app()->session()->flash('error', $errorMsg);
            $this->redirect('/login');
        }

        app()->session()->put('user', [
            'id'    => $user['id'] ?? '',
            'name'  => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'role'  => $user['role'] ?? 'user',
        ]);

        if ($isJson) {
            app()->response()->json(['success' => true, 'redirect' => '/']);
        }

        $this->redirect('/');
    }

    /**
     * Handle a registration request.
     *
     * @return never Redirects on success or failure.
     */
    public function register(): never
    {
        $request = app()->request();
        Csrf::verify($request);
        $validator = new Validator();
        $data = $request->only(['username', 'password']);
        $isJson = $request->expectsJson();

        if (!$validator->validate($data, ['username' => 'required|min:3', 'password' => 'required|min:6'])) {
            if ($isJson) {
                app()->response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Thông tin đăng ký không hợp lệ.',
                ], 422);
            }

            app()->session()->flash('errors', $validator->errors());
            app()->session()->flashOldInput($data);
            $this->redirect('/register');
        }

        $errors = [];

        if ($this->userService->findByUsername($data['username'])) {
            $errors['username'][] = 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.';
        }

        if (!empty($errors)) {
            if ($isJson) {
                app()->response()->json([
                    'errors' => $errors,
                    'message' => 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.',
                ], 422);
            }

            app()->session()->flash('errors', $errors);
            app()->session()->flashOldInput($data);
            $this->redirect('/register');
        }

        $username = $data['username'] ?? '';
        $created = $this->userService->createUser([
            'name'     => $username,
            'email'    => $username,
            'username' => $username,
            'password' => $data['password'],
        ]);
        if (!$created) {
            $errorMsg = 'Could not create account. Please try again.';
            if ($isJson) {
                app()->response()->json([
                    'message' => $errorMsg,
                ], 500);
            }

            app()->session()->flash('error', $errorMsg);
            app()->session()->flashOldInput($data);
            $this->redirect('/register');
        }

        app()->session()->flash('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.');

        if ($isJson) {
            app()->response()->json([
                'success' => true,
                'redirect' => '/login',
            ]);
        }

        $this->redirect('/login');
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
            app()->session()->flash('error', 'Tên đăng nhập / Mật khẩu không đúng. Vui lòng thử lại!');
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
