<?php
namespace App\Repositories;

use PDO;
use App\Core\Database;
use App\Models\OrderItem;

class OrderItemRepository implements RepositoryInterface
{
    private PDO $pd;


    public function __construct(){
        $this->db=Database::getInstance()->getConnection();

    }


    
    public function findById(int $id): ?OrderItem{
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new User(
                id: $data['id'],
                order_id: $data['order_id'],
                product_id: $data['product_id'],
                quantity: $data['quantity'],
                price: $data['price']
            );
        }

        return null;
    }


    public function findAll(): array{
        $stmt= $this->db->query('SELECT * FROM order_items');
        $items=[];
        while ($data = $stmt->fetch()) {
            $items[] = new OrderItem(
                id: $data['id'],
                order_id: $data['order_id'],
                product_id: $data['product_id'],
                quantity: $data['quantity'],
                price: $data['price']
            );
        }

        return $items;
    }


    public function save(OrderItem $item): bool
    {
        if(!$item instanceof OrderItem){
            return false;
        }

        if($item->id){
            $stmt = $this->db->prepare('UPDATE order_items SET name = :name, email = :email, password = :password, role = :role WHERE id = :id');
            return $stmt->execute([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'role' => $user->role,
                'id' => $user->id
            ]);
        }else{
            $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
            return $stmt->execute([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'role' => $user->role
            ]);
        }
       
    }











}



