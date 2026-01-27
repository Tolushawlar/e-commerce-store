<?php

namespace App\Helpers;

/**
 * Validation Helper
 */
class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Validate data
     */
    public function validate(): bool
    {
        foreach ($this->rules as $field => $rules) {
            $ruleArray = explode('|', $rules);

            foreach ($ruleArray as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Apply validation rule
     */
    private function applyRule(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? null;

        // Required
        if ($rule === 'required' && empty($value)) {
            $this->addError($field, ucfirst($field) . ' is required');
            return;
        }

        // Email
        if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, ucfirst($field) . ' must be a valid email');
        }

        // Numeric
        if ($rule === 'numeric' && $value && !is_numeric($value)) {
            $this->addError($field, ucfirst($field) . ' must be a number');
        }

        // Min length
        if (strpos($rule, 'min:') === 0 && $value) {
            $min = (int)substr($rule, 4);
            if (strlen($value) < $min) {
                $this->addError($field, ucfirst($field) . " must be at least $min characters");
            }
        }

        // Max length
        if (strpos($rule, 'max:') === 0 && $value) {
            $max = (int)substr($rule, 4);
            if (strlen($value) > $max) {
                $this->addError($field, ucfirst($field) . " must not exceed $max characters");
            }
        }

        // URL
        if ($rule === 'url' && $value && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, ucfirst($field) . ' must be a valid URL');
        }

        // Alpha
        if ($rule === 'alpha' && $value && !ctype_alpha($value)) {
            $this->addError($field, ucfirst($field) . ' must contain only letters');
        }

        // Alpha numeric
        if ($rule === 'alphanumeric' && $value && !ctype_alnum($value)) {
            $this->addError($field, ucfirst($field) . ' must contain only letters and numbers');
        }
    }

    /**
     * Add validation error
     */
    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $message;
    }
}
