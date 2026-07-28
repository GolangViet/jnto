<?php

declare(strict_types=1);

namespace Core;

final class Request
{
    /**
     * Get the HTTP method for the current request.
     *
     * @return string Uppercased HTTP method (GET, POST, etc.).
     */
    public function method(): string
    {
        return strtoupper($_POST['_method'] ?? $_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get the request path (URI) normalized to a leading slash.
     *
     * @return string Normalized path.
     */
    public function path(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }

    /**
     * Retrieve an input value from POST or GET data.
     *
     * @param string $key Input key to retrieve.
     * @param mixed|null $default Default value if key is missing.
     * @return mixed The input value or default.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Get all input data from GET and POST.
     *
     * @return array Merged GET and POST arrays.
     */
    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Return only the specified keys from the input data.
     *
     * @param array $keys Keys to retain.
     * @return array Filtered input array.
     */
    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    /**
     * Determine if the current request is a POST request.
     *
     * @return bool True if POST, false otherwise.
     */
    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }
}
