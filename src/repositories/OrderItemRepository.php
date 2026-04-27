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

    /**
     * Elimina todos los items de un pedido (para reconstrucción).
     */
    public function deleteByOrderId(int $orderId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM order_items WHERE order_id = :order_id');
        return $stmt->execute(['order_id' => $orderId]);
    }

    /**
     * Inserta un item en un pedido.
     */
    public function insertItem(int $orderId, int $productId, int $quantity, float $price): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)'
        );
        return $stmt->execute([
            'order_id'   => $orderId,
            'product_id' => $productId,
            'quantity'   => $quantity,
            'price'      => $price,
        ]);
    }

    /**
     * Busca todos los items de un pedido específico.
     */
    public function findByOrderId(int $orderId): array
    {
        $sql = "SELECT product_id, quantity FROM order_items WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
        
        // Retorna un array de objetos estándar con product_id y quantity
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // En OrderItemRepository.php
public function migrarCarrito(int $userId, string $sessionId): void
{
    // 1. Buscamos si el invitado tenía una orden pendiente asociada a su sesión
    // Nota: Esto depende de que en tu tabla 'orders' guardes el session_id
    $sql = "UPDATE orders SET user_id = :user_id, session_id = NULL 
            WHERE session_id = :session_id AND status = 'pending'";
    
    $stmt = $this->db->prepare($sql); // Asumo que usas $this->db para la conexión
    $stmt->execute([
        'user_id' => $userId,
        'session_id' => $sessionId
    ]);
}
}


