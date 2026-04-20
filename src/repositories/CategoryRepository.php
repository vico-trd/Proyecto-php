<?php

namespace App\Repositories;

use PDO;
use App\Core\Database;
use App\Models\Category;

class CategoryRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Category(
                name: $data['name'],
                description: $data['description'],
                id: (int)$data['id'],
            );
        }

        return null;
    }

    public function findByName(string $name): ?Category
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE name = :name');
        $stmt->execute(['name' => $name]);
        $data = $stmt->fetch();

        if ($data) {
            return new Category(
                name: $data['name'],
                description: $data['description'],
                id: (int)$data['id'],
            );
        }

        return null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM categories');
        $categories = [];
        while ($data = $stmt->fetch()) {
            $categories[] = new Category(
                name: $data['name'],
                description: $data['description'],
                id: (int)$data['id'],
            );
        }

        return $categories;
    }

    public function save(object $category): bool
    {
        if (!$category instanceof Category) {
            return false;
        }

        if ($category->id) {
            $stmt = $this->db->prepare('UPDATE categories SET name = :name, description = :description WHERE id = :id');
            return $stmt->execute([
                'name' => $category->name,
                'description' => $category->description,
                'id' => $category->id,
            ]);
        } else {
            $stmt = $this->db->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');
            return $stmt->execute([
                'name' => $category->name,
                'description' => $category->description,
            ]);
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
