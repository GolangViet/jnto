<?php

declare(strict_types=1);

namespace Core;

use RuntimeException;

final class Encrypter
{
    private string $key;
    private string $cipher;

    /**
     * Initialize the Encrypter with a key and cipher.
     *
     * @param string $key
     * @param string $cipher
     * @throws RuntimeException
     */
    public function __construct(string $key, string $cipher = 'AES-256-CBC')
    {
        $this->cipher = $cipher;

        // If the key starts with base64:, decode it
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        if (!$this->supported($key, $cipher)) {
            throw new RuntimeException("The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.");
        }

        $this->key = $key;
    }

    /**
     * Determine if the given key and cipher combination is supported.
     *
     * @param string $key
     * @param string $cipher
     * @return bool
     */
    protected function supported(string $key, string $cipher): bool
    {
        $length = mb_strlen($key, '8bit');

        return ($cipher === 'AES-128-CBC' && $length === 16) ||
               ($cipher === 'AES-256-CBC' && $length === 32);
    }

    /**
     * Encrypt the given value.
     *
     * @param string $value
     * @return string
     * @throws RuntimeException
     */
    public function encrypt(string $value): string
    {
        $ivLength = openssl_cipher_iv_length($this->cipher);
        if ($ivLength === false) {
            throw new RuntimeException('Could not get cipher IV length.');
        }

        $iv = openssl_random_pseudo_bytes($ivLength);

        $encrypted = openssl_encrypt($value, $this->cipher, $this->key, 0, $iv);

        if ($encrypted === false) {
            throw new RuntimeException('Could not encrypt the data.');
        }

        $ivEncoded = base64_encode($iv);
        $mac = $this->hash($ivEncoded, $encrypted);
        $json = json_encode(['iv' => $ivEncoded, 'value' => $encrypted, 'mac' => $mac], JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Could not JSON encode encrypted payload.');
        }

        return base64_encode($json);
    }

    /**
     * Decrypt the given value.
     *
     * @param string $payload
     * @return string
     * @throws RuntimeException
     */
    public function decrypt(string $payload): string
    {
        $payloadData = $this->getJsonPayload($payload);
        $iv = base64_decode($payloadData['iv']);

        $decrypted = openssl_decrypt($payloadData['value'], $this->cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            throw new RuntimeException('Could not decrypt the data.');
        }

        return $decrypted;
    }

    /**
     * Get the JSON payload and validate it.
     *
     * @param string $payload
     * @return array
     * @throws RuntimeException
     */
    protected function getJsonPayload(string $payload): array
    {
        $decoded = base64_decode($payload, true);
        if ($decoded === false) {
            throw new RuntimeException('The payload is not valid base64.');
        }

        $data = json_decode($decoded, true);

        if (!is_array($data) || !isset($data['iv'], $data['value'], $data['mac'])) {
            throw new RuntimeException('The payload structure is invalid.');
        }

        $decodedIv = base64_decode($data['iv'], true);
        $ivLength = openssl_cipher_iv_length($this->cipher);
        if ($decodedIv === false || mb_strlen($decodedIv, '8bit') !== $ivLength) {
            throw new RuntimeException('The IV is invalid.');
        }

        if (!hash_equals($data['mac'], $this->hash($data['iv'], $data['value']))) {
            throw new RuntimeException('The MAC is invalid.');
        }

        return $data;
    }

    /**
     * Create a MAC for the given value.
     *
     * @param string $iv
     * @param string $value
     * @return string
     */
    protected function hash(string $iv, string $value): string
    {
        return hash_hmac('sha256', $iv . $value, $this->key);
    }
}
