<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;

class CategoriaService
{
    private CategoryRepository $repository;

    public function __construct()
    {
        $this->repository = new CategoryRepository();
    }

    public function listar(): array
    {
        return $this->repository->findAll();
    }

    public function obtenerPorId(int $id): ?Category
    {
        return $this->repository->findById($id);
    }

    /**
     * Crea una nueva categoría. Devuelve true si se creó correctamente,
     * o un string con el mensaje de error si falló.
     */
    public function crear(array $data): bool|string
    {
        $existing = $this->repository->findByName($data['name']);
        if ($existing) {
            return 'Ya existe una categoría con ese nombre.';
        }

        $category = new Category(
            name: $data['name'],
            description: $data['description'],
        );

        return $this->repository->save($category);
    }

    /**
     * Edita una categoría existente. Devuelve true si se editó correctamente,
     * o un string con el mensaje de error si falló.
     */
    public function editar(int $id, array $data): bool|string
    {
        $category = $this->repository->findById($id);
        if (!$category) {
            return 'La categoría no existe.';
        }

        // Verificar nombre duplicado excluyendo la categoría actual
        $existing = $this->repository->findByName($data['name']);
        if ($existing && $existing->id !== $id) {
            return 'Ya existe otra categoría con ese nombre.';
        }

        $category->name = $data['name'];
        $category->description = $data['description'];

        return $this->repository->save($category);
    }

    public function eliminar(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
