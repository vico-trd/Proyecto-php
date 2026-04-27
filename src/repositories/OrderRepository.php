<?php

namespace App\Repositories;

use PDO;
use App\Core\Database;
use App\Models\Order;

class OrderRepository implements RepositoryInterface
{
    private PDO $db;


    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }


    public function findById(int $id): ?Order
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Order(
                id: (int)$data['id'],
                user_id: (int)$data['user_id'],
                total: (float)$data['total'],
                status: $data['status']
            );
        }

        return null;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM orders');
        $orders = [];
        while ($data = $stmt->fetch()) {
            $orders[] = new Order(
                id: (int)$data['id'],
                user_id: (int)$data['user_id'],
                total: (float)$data['total'],
                status: $data['status']
            );
        }

        return $orders;
    }

    public function save(object $order): bool
    {
        if (!$order instanceof Order) {
            return false;
        }

        if ($order->id) {
            $stmt = $this->db->prepare('UPDATE orders SET user_id = :user_id, total = :total, status = :status WHERE id = :id');
            return $stmt->execute([
                'user_id' => $order->user_id,
                'total' => $order->total,
                'status' => $order->status,
            ]);
        } else {
            $stmt = $this->db->prepare('INSERT INTO orders (user_id, total, status) VALUES (:user_id, :total, :status)');
            return $stmt->execute([
                'user_id' => $order->user_id,
                'total' => $order->total,
                'status' => $order->status
            ]);
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM orders WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Busca el pedido 'pending' más reciente de un usuario.
     */
    public function findPendingByUserId(int $userId): ?Order
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE user_id = :user_id AND status = 'pending' ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute(['user_id' => $userId]);
        $data = $stmt->fetch();

        if ($data) {
            return new Order(
                id: (int)$data['id'],
                user_id: (int)$data['user_id'],
                total: (float)$data['total'],
                status: $data['status']
            );
        }

        return null;
    }

    /**
     * Crea un pedido pendiente para el usuario y devuelve su ID.
     */
    public function createPendingOrder(int $userId): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO orders (user_id, total, status) VALUES (:user_id, 0, 'pending')"
        );
        $stmt->execute(['user_id' => $userId]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualiza el importe total de un pedido.
     */
    public function updateTotal(int $orderId, float $total): bool
    {
        $stmt = $this->db->prepare('UPDATE orders SET total = :total WHERE id = :id');
        return $stmt->execute(['total' => $total, 'id' => $orderId]);
    }

    /**
     * Confirma un pedido cambiando su estado de 'pending' a 'confirmed'.
     */
    public function confirmarPedido(int $orderId): bool
    {
        $stmt = $this->db->prepare("UPDATE orders SET status = 'confirmed' WHERE id = :id AND status = 'pending'");
        return $stmt->execute(['id' => $orderId]);
    }

    /**
     * Obtiene los items de un pedido con información del producto.
     */
    public function findItemsWithProductByOrderId(int $orderId): array
    {
        $stmt = $this->db->prepare(
            "SELECT oi.*, p.name AS product_name
             FROM order_items oi
             INNER JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = :order_id"
        );
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }
}
