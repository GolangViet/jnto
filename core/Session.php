<?php

declare(strict_types=1);

namespace Core;

final class Session
{
    /**
     * Start the session if it is not already active.
     */
    public function __construct()
    {
        if (php_sapi_name() !== 'cli' && session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
    }

    /**
     * Retrieve a value from the session.
     *
     * @param string $key Session key.
     * @param mixed|null $default Default value if key missing.
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Put a value into the session.
     *
     * @param string $key Session key.
     * @param mixed $value Value to store.
     * @return void
     */
    public function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Remove a key from the session.
     *
     * @param string $key Session key to remove.
     * @return void
     */
    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Flash a value to the session for the next request.
     *
     * @param string $key Flash key.
     * @param mixed $value Value to store for one request.
     * @return void
     */
    public function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Retrieve and remove a flashed value from the session.
     *
     * @param string $key Flash key to pull.
     * @param mixed|null $default Default value if not present.
     * @return mixed
     */
    public function pullFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * Store old input data in the session for the next request.
     *
     * @param array $input Input data to persist.
     * @return void
     */
    public function flashOldInput(array $input): void
    {
        $_SESSION['_old'] = $input;
    }

    /**
     * Get or generate a CSRF token stored in session.
     *
     * @return string CSRF token value.
     */
    public function token(): string
    {
        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_token'];
    }

    /**
     * Validate a provided CSRF token against the session token.
     *
     * @param string|null $token Token to validate.
     * @return bool True if token is valid, false otherwise.
     */
    public function validateToken(?string $token): bool
    {
        return is_string($token) && hash_equals($this->token(), $token);
    }
}
