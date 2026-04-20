<?php

namespace App\Request;

class UserRequest
{
    private array $errors = [];
    private array $data = [];

    public function __construct(array $input)
    {
        $this->data = $this->sanitize($input);
    }

    /**
     * Sanitiza los campos de entrada
     */
    private function sanitize(array $input): array
    {
        return [
            'name'     => isset($input['name']) ? htmlspecialchars(trim($input['name']), ENT_QUOTES, 'UTF-8') : '',
            'email'    => isset($input['email']) ? filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL) : '',
            'password' => isset($input['password']) ? trim($input['password']) : '',
        ];
    }

    /**
     * Valida los datos para el registro
     */
    public function validateRegister(): bool
    {
        $this->errors = [];

        // Nombre
        if (empty($this->data['name'])) {
            $this->errors['name'] = 'El nombre es obligatorio.';
        } elseif (strlen($this->data['name']) < 3) {
            $this->errors['name'] = 'El nombre debe tener al menos 3 caracteres.';
        } elseif (strlen($this->data['name']) > 100) {
            $this->errors['name'] = 'El nombre no puede superar los 100 caracteres.';
        }

        // Email
        if (empty($this->data['email'])) {
            $this->errors['email'] = 'El email es obligatorio.';
        } elseif (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'El formato del email no es válido.';
        }

        // Password
        if (empty($this->data['password'])) {
            $this->errors['password'] = 'La contraseña es obligatoria.';
        } elseif (strlen($this->data['password']) < 6) {
            $this->errors['password'] = 'La contraseña debe tener al menos 6 caracteres.';
        }

        return empty($this->errors);
    }

    /**
     * Valida los datos para el login
     */
    public function validateLogin(): bool
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

    /**
     * Devuelve los errores de validación
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Devuelve un campo sanitizado
     */
    public function get(string $field): string
    {
        return $this->data[$field] ?? '';
    }

    /**
     * Devuelve todos los datos sanitizados
     */
    public function all(): array
    {
        return $this->data;
    }
}
