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
 * Get a system setting value from the settings table.
 *
 * @param string $key
 * @param mixed|null $default
 * @return mixed
 */
function setting(string $key, mixed $default = null): mixed
{
    static $settings = null;
    if ($settings === null) {
        $settings = [];
        try {
            $db = \Core\Database::connection();
            $stmt = $db->query("SELECT key, value FROM cms.settings");
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $settings[$row['key']] = $row['value'];
            }
        } catch (\Throwable $e) {
            // Table might not exist yet during migrations
        }
    }
    return $settings[$key] ?? $default;
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
    static $base = null;
    if ($base === null) {
        if (php_sapi_name() === 'cli') {
            $base = rtrim((string) config('app.url'), '/');
        } else {
            $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on');
            $scheme = $isHttps ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $base = $scheme . '://' . $host;
        }
    }

    return $base . '/' . ltrim($path, '/');
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

/**
 * Render a view and return its HTML as a string.
 *
 * @param string $view
 * @param array $data
 * @param string|bool $layout
 * @return string
 */
function view(string $view, array $data = [], string|bool $layout = false): string
{
    return \Core\View::render($view, $data, $layout);
}

/**
 * Push a style file to the view.
 *
 * @param string $url
 * @return void
 */
function push_style(string $url): void
{
    \Core\View::pushStyle($url);
}

/**
 * Push a script file to the view.
 *
 * @param string $url
 * @return void
 */
function push_script(string $url): void
{
    \Core\View::pushScript($url);
}

/**
 * Push a modal template to the view.
 *
 * @param string $modal
 * @return void
 */
function push_modal(string $modal): void
{
    \Core\View::pushModal($modal);
}

/**
 * Render all pushed style files.
 *
 * @return string
 */
function render_styles(): string
{
    return \Core\View::renderStyles();
}

/**
 * Render all pushed script files.
 *
 * @return string
 */
function render_scripts(): string
{
    return \Core\View::renderScripts();
}

/**
 * Render all pushed modal templates.
 *
 * @return string
 */
function render_modals(): string
{
    return \Core\View::renderModals();
}

/**
 * Summary of assets
 *
 * @param string $path
 * @return string
 */
function assets(string $path = ''): string
{
    return url("/assets/{$path}");
}

/**
 * Get the path to an asset with its version hash.
 *
 * @param string $path
 * @return string
 */
function asset_with_version(string $path = ''): string
{
    $url = assets($path);
    $version = \Core\View::getVersion();
    $separator = str_contains($url, '?') ? '&' : '?';

    return $url . $separator . 'v=' . $version;
}

/**
 * Get the canonical url
 *
 * @return string
 */
function canonical_url(): string
{
    $canonicalBase = url();
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path = '/' . trim($path, '/');
    if ($path === '/') {
        $canonical = $canonicalBase . '/';
    } else {
        $canonical = $canonicalBase . $path;
    }

    return $canonical;
}
