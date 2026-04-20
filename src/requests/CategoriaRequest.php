<?php

namespace App\Requests;

class CategoriaRequest
{
    private array $errors = [];

    public function validate(array $data): bool
    {
        $this->errors = [];

        $name = trim($data['name'] ?? '');

        if ($name === '') {
            $this->errors['name'] = 'El nombre de la categoría es obligatorio.';
        } elseif (mb_strlen($name) < 3) {
            $this->errors['name'] = 'El nombre debe tener al menos 3 caracteres.';
        } elseif (mb_strlen($name) > 100) {
            $this->errors['name'] = 'El nombre no puede superar los 100 caracteres.';
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function sanitize(array $data): array
    {
        return [
            'name'        => htmlspecialchars(trim($data['name'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars(trim($data['description'] ?? ''), ENT_QUOTES, 'UTF-8'),
        ];
    }
}
