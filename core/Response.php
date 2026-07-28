<?php

declare(strict_types=1);

namespace Core;

final class Response
{
    /**
     * Set the HTTP response status code.
     *
     * @param int $code HTTP status code to send.
     * @return self
     */
    public function status(int $code): self
    {
        http_response_code($code);
        return $this;
    }

    /**
     * Send a JSON response and terminate script execution.
     *
     * @param array $data The data to JSON-encode.
     * @param int $status HTTP status code (default 200).
     * @return never
     */
    public function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirect the client to a URL and terminate execution.
     *
     * @param string $url Destination URL.
     * @return never
     */
    public function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}
