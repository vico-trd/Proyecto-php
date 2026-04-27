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
                status: $data['status'],
                created_at: $data['created_at'] ?? null
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
                status: $data['status'],
                created_at: $data['created_at'] ?? null
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
  // 1. BUSCAR POR USUARIO (Este es el que te daba el error Fatal)
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
            user_id: $data['user_id'] ? (int)$data['user_id'] : null,
            total: (float)$data['total'],
            status: $data['status'],
            created_at: $data['created_at'] ?? null
        );
    }
    return null;
}

// 2. BUSCAR POR SESIÓN (Para invitados)
public function findPendingBySessionId(string $sessionId): ?Order
{
    $stmt = $this->db->prepare(
        "SELECT * FROM orders WHERE session_id = :session_id AND status = 'pending' LIMIT 1"
    );
    $stmt->execute(['session_id' => $sessionId]);
    $data = $stmt->fetch();

    if ($data) {
        return new Order(
            id: (int)$data['id'],
            user_id: $data['user_id'] ? (int)$data['user_id'] : null,
            total: (float)$data['total'],
            status: $data['status'],
            created_at: $data['created_at'] ?? null
        );
    }
    return null;
}

// 3. CREAR PEDIDO (Acepta ambos casos)
public function createPendingOrder(?int $userId, ?string $sessionId = null): int
{
    $sql = "INSERT INTO orders (user_id, session_id, total, status) VALUES (:user_id, :session_id, 0, 'pending')";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        'user_id' => $userId,
        'session_id' => $sessionId
    ]);
    return (int)$this->db->lastInsertId();
}


    public function updateTotal(int $orderId, float $total): bool
    {
        $stmt = $this->db->prepare('UPDATE orders SET total = :total WHERE id = :id');
        return $stmt->execute(['total' => $total, 'id' => $orderId]);
    }



    /**
     * Devuelve todos los pedidos confirmados de un usuario, del más reciente al más antiguo.
     */
    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE user_id = :user_id AND status != 'pending' ORDER BY created_at DESC"
        );
        $stmt->execute(['user_id' => $userId]);
        $orders = [];
        while ($data = $stmt->fetch()) {
            $orders[] = new Order(
                id: (int)$data['id'],
                user_id: (int)$data['user_id'],
                total: (float)$data['total'],
                status: $data['status'],
                created_at: $data['created_at'] ?? null
            );
        }
        return $orders;
    }

    /**
     * Devuelve un pedido por ID solo si pertenece al usuario indicado.
     */
    public function findByIdAndUserId(int $orderId, int $userId): ?Order
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE id = :id AND user_id = :user_id AND status != 'pending'"
        );
        $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
        $data = $stmt->fetch();
        if ($data) {
            return new Order(
                id: (int)$data['id'],
                user_id: (int)$data['user_id'],
                total: (float)$data['total'],
                status: $data['status'],
                created_at: $data['created_at'] ?? null
            );
        }
        return null;
    }

    //FUNCION PARA LOS PEDIDOS, PUNTO 15
    public function finalizarPedido(int $orderId, array $items): bool
{
    try {
        $this->db->beginTransaction();

        // 1. Cambiar estado del pedido
        $stmt = $this->db->prepare("UPDATE orders SET status = 'confirmado' WHERE id = :id");
        $stmt->execute(['id' => $orderId]);

        // 2. Decrementar stock de cada producto
        $stmtStock = $this->db->prepare("UPDATE products SET stock = stock - :cantidad WHERE id = :product_id");
        
        foreach ($items as $productId => $cantidad) {
            $stmtStock->execute([
                'cantidad' => $cantidad,
                'product_id' => $productId
            ]);
        }

        $this->db->commit();
        return true;
    } catch (\Exception $e) {
        $this->db->rollBack();
        // Opcional: loguear el error $e->getMessage();
        return false;
    }
}
    
}
