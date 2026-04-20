<?php

namespace App\Repositories;

use PDO;
use App\Core\Database;
use App\Models\Order;


class OrderRepository implements RepositoryInterface{
private PDO $db;


public function __construct(){
    $this->db = Database::getInstance()->getConnection();

}


public function findById(int $id): ?Order{
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Order(
                id: $data['id'],
                user_id: $data['user_id'],
                total: $data['total'],
                status: $data['status']
                
            );
        }

        return null;
    }



    public function findAll(): array{
        $stmt= $this->db->query('SELECT * FROM orders');
        $orders=[];
        while ($data = $stmt->fetch()) {
            $orders[] = new Order(
                id: $data['id'],
                user_id: $data['user_id'],
                total: $data['total'],
                status: $data['status']
            );
        }

        return $orders;
    }

    public function save(Order $order): bool
    {
        if(!$order instanceof Order){
            return false;
        }

        if($order->id){
            $stmt = $this->db->prepare('UPDATE orders SET user_id = :user_id, total = :total, status = :status WHERE id = :id');
            return $stmt->execute([
                'id' => $order->id,
                'user_id' => $order->user_id,
                'total' => $order->total,
                'status' => $order->status
            ]);
        }else{
            $stmt = $this->db->prepare('INSERT INTO orders (user_id, total, status) VALUES (:user_id, :total, :status)');
            return $stmt->execute([
                'user_id' => $order->user_id,
                'total' => $order->total,
                'status' => $order->status
            ]);
        }
       
    }

    public function delete(int $id):bool{
        $stmt = $this->db->prepare('DELETE FROM orders WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }







}
