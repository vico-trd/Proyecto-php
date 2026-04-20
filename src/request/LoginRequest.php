<?php

namespace App\Request;

class LoginRequest
{
    private array $errors = [];
    private array $data = [];

    public function __construct(array $input)
    {
        $this->data = $this->sanitize($input);
    }

    private function sanitize(array $input): array
    {
        return [
            'email'    => isset($input['email']) ? filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL) : '',
            'password' => isset($input['password']) ? trim($input['password']) : '',
        ];
    }

    public function validate(): bool
    {
        $this->errors = [];

        if (empty($this->data['email'])) {
            $this->errors['email'] = 'El email es obligatorio.';
        } elseif (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'El formato del email no es válido.';
        }

        if (empty($this->data['password'])) {
            $this->errors['password'] = 'La contraseña es obligatoria.';
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function get(string $field): string
    {
        return $this->data[$field] ?? '';
    }

    public function all(): array
    {
        return $this->data;
    }
}
