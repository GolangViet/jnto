<?php

declare(strict_types=1);

use Core\Application;

/**
 * Get an environment variable or return a default value.
 *
 * @param string $key
 * @param mixed|null $default
 * @return mixed
 */
function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

/**
 * Get a configuration value from the application config.
 *
 * @param string $key
 * @param mixed|null $default
 * @return mixed
 */
function config(string $key, mixed $default = null): mixed
{
    return Application::getInstance()->config($key, $default);
}

/**
 * Get the application instance.
 *
 * @return Application
 */
function app(): Application
{
    return Application::getInstance();
}

/**
 * Escape a string for safe output in HTML.
 *
 * @param string|null $value
 * @return string
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate a full URL for a given path based on the application URL configuration.
 *
 * @param string $path
 * @return string
 */
function url(string $path = ''): string
{
    return rtrim((string) config('app.url'), '/') . '/' . ltrim($path, '/');
}

/**
 * Redirect to a given path and terminate the script.
 *
 * @param string $path
 * @return never
 */
function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

/**
 * Get the old input value for a given key from the session, or return a default value.
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

/**
 * Generate a CSRF token input field for forms.
 *
 * @return string
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(app()->session()->token()) . '">';
}

/**
 * Encrypt the given value.
 *
 * @param string $value
 * @return string
 */
function encrypt(string $value): string
{
    static $encrypter = null;
    if ($encrypter === null) {
        $encrypter = new \Core\Encrypter(
            (string) config('app.key'),
            (string) config('app.cipher', 'AES-256-CBC')
        );
    }
    return $encrypter->encrypt($value);
}

/**
 * Decrypt the given value.
 *
 * @param string $payload
 * @return string
 */
function decrypt(string $payload): string
{
    static $encrypter = null;
    if ($encrypter === null) {
        $encrypter = new \Core\Encrypter(
            (string) config('app.key'),
            (string) config('app.cipher', 'AES-256-CBC')
        );
    }
    return $encrypter->decrypt($payload);
}

/**
 * Render a component view with isolated scope.
 *
 * @param string $name
 * @param array $data
 * @return void
 */
function component(string $name, array $data = []): void
{
    extract($data);
    require Application::getInstance()->basePath('app/Views/components/' . $name . '.php');
}

