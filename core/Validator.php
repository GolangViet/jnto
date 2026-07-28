<?php

declare(strict_types=1);

namespace Core;

final class Validator
{
    /**
     * @var array
     */
    private array $errors = [];

    /**
     * Validate the given data against the provided rules.
     *
     * @param array $data
     * @param array $rules
     * @return bool
     */
    public function validate(array $data, array $rules): bool
    {
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach (explode('|', $fieldRules) as $rule) {
                [$name, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

                if ($name === 'required' && ($value === null || trim((string) $value) === '')) {
                    $this->errors[$field][] = ucfirst($field) . ' is required.';
                }

                if ($name === 'min' && mb_strlen((string) $value) < (int) $parameter) {
                    $this->errors[$field][] = ucfirst($field) . " must be at least {$parameter} characters.";
                }

                if ($name === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a valid email.';
                }
            }
        }

        return $this->errors === [];
    }

    /**
     * Get the validation errors.
     *
     * @return array
     */
    public function errors(): array { return $this->errors; }

    /**
     * Get the first error message for a specific field.
     *
     * @param string $field
     * @return string|null
     */
    public function first(string $field): ?string { return $this->errors[$field][0] ?? null; }
}
