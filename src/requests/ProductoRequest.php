<?php

namespace App\Requests;

class ProductoRequest
{
    private array $errors = [];

    public function validate(array $data, array $files = []): bool
    {
        $this->errors = [];

        $name = trim($data['name'] ?? '');
        $categoryId = $data['category_id'] ?? null;
        $price = $data['price'] ?? null;
        $stock = $data['stock'] ?? null;

        if ($name === '') {
            $this->errors['name'] = 'El nombre del producto es obligatorio.';
        } elseif (mb_strlen($name) < 3) {
            $this->errors['name'] = 'El nombre debe tener al menos 3 caracteres.';
        } elseif (mb_strlen($name) > 100) {
            $this->errors['name'] = 'El nombre no puede superar los 100 caracteres.';
        }

        if (!is_numeric($categoryId) || (int)$categoryId <= 0) {
            $this->errors['category_id'] = 'Debes seleccionar una categoria valida.';
        }

        if (!is_numeric($price) || (float)$price < 0) {
            $this->errors['price'] = 'El precio debe ser un numero mayor o igual a 0.';
        }

        if (filter_var($stock, FILTER_VALIDATE_INT) === false || (int)$stock < 0) {
            $this->errors['stock'] = 'El stock debe ser un entero mayor o igual a 0.';
        }

        $image = $files['image'] ?? null;
        if (is_array($image) && ($image['name'] ?? '') !== '') {
            if (($image['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $this->errors['image'] = 'Error al subir la imagen.';
            } else {
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $extension = strtolower(pathinfo((string)$image['name'], PATHINFO_EXTENSION));
                if (!in_array($extension, $allowed, true)) {
                    $this->errors['image'] = 'Formato de imagen no permitido.';
                }
            }
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
            'name' => htmlspecialchars(trim($data['name'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'category_id' => (int)($data['category_id'] ?? 0),
            'description' => htmlspecialchars(trim($data['description'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'price' => (float)($data['price'] ?? 0),
            'stock' => (int)($data['stock'] ?? 0),
        ];
    }
}
