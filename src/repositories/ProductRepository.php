<?php

namespace App\Repositories;

use PDO;
use App\Core\Database;
use App\Models\Product;

class ProductRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById(int $id): ?Product
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Product(
                id: (int)$data['id'],
                name: $data['name'],
                category_id: (int)$data['category_id'],
                description: $data['description'] ?? '',
                price: (float)$data['price'],
                stock: (int)$data['stock'],
                image: $data['image'] ?? ''
            );
        }

        return null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM products');
        $products = [];
        while ($data = $stmt->fetch()) {
            $products[] = new Product(
                id: (int)$data['id'],
                name: $data['name'],
                category_id: (int)$data['category_id'],
                description: $data['description'] ?? '',
                price: (float)$data['price'],
                stock: (int)$data['stock'],
                image: $data['image'] ?? ''
            );
        }

        return $products;
    }

    public function countByCategory(int $categoryId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total FROM products WHERE category_id = :category_id');
        $stmt->execute(['category_id' => $categoryId]);
        $data = $stmt->fetch();

        return (int)($data['total'] ?? 0);
    }

    public function findByCategoryPaginated(int $categoryId, int $limit, int $offset): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM products WHERE category_id = :category_id ORDER BY id DESC LIMIT :limit OFFSET :offset'
        );

        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $products = [];
        while ($data = $stmt->fetch()) {
            $products[] = new Product(
                id: (int)$data['id'],
                name: $data['name'],
                category_id: (int)$data['category_id'],
                description: $data['description'] ?? '',
                price: (float)$data['price'],
                stock: (int)$data['stock'],
                image: $data['image'] ?? ''
            );
        }

        return $products;
    }

    public function save(object $product): bool
    {
        if (!$product instanceof Product) {
            return false;
        }

        if ($product->id) {
            $stmt = $this->db->prepare('UPDATE products SET name = :name, category_id = :category_id, description = :description, price = :price, stock = :stock, image = :image WHERE id = :id');
            return $stmt->execute([
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'image' => $product->image
            ]);
        } else {
            $stmt = $this->db->prepare('INSERT INTO products (name, category_id, description, price, stock, image) VALUES (:name, :category_id, :description, :price, :stock, :image)');
            return $stmt->execute([
                'name' => $product->name,
                'category_id' => $product->category_id,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'image' => $product->image
            ]);
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}