<?php
namespace App\Repositories;

use PDO;
use App\Core\Database;
use App\Models\OrderItem;

class OrderItemRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById(int $id): ?OrderItem
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new OrderItem(
                id: (int)$data['id'],
                order_id: (int)$data['order_id'],
                product_id: (int)$data['product_id'],
                quantity: (int)$data['quantity'],
                price: (float)$data['price']
            );
        }

        return null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM order_items');
        $items = [];
        while ($data = $stmt->fetch()) {
            $items[] = new OrderItem(
                id: (int)$data['id'],
                order_id: (int)$data['order_id'],
                product_id: (int)$data['product_id'],
                quantity: (int)$data['quantity'],
                price: (float)$data['price']
            );
        }

        return $items;
    }

    public function save(object $item): bool
    {
        if (!$item instanceof OrderItem) {
            return false;
        }

        if ($item->id) {
            $stmt = $this->db->prepare('UPDATE order_items SET order_id = :order_id, product_id = :product_id, quantity = :quantity, price = :price WHERE id = :id');
            return $stmt->execute([
                'order_id' => $item->order_id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'id' => $item->id
            ]);
        } else {
            $stmt = $this->db->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)');
            return $stmt->execute([
                'order_id' => $item->order_id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price
            ]);
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM order_items WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}



