<?php

namespace App\Repositories;

use PDO;
use App\Core\Database;
use App\Models\User;

class CategoryRepository implements RepositoryInterface
{
private PDO $db;

public function __construct()
{
    $this->db=Database::getInstance()->getConnection();
}

  public function findById(int $id): ?Category{
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Category(
                id: $data['id'],
                name: $data['name'],
                description: $data['description'],
            );
        }

        return null;
    }


    public function findAll(): array{
        $stmt= $this->db->query('SELECT * FROM categories');
        $categories=[];
        while ($data = $stmt->fetch()) {
            $categories[] = new Category(
                id: $data['id'],
                name: $data['name'],
                description: $data['description'],
                
            );
        }

        return $categories;
    }


     public function save( $category): bool
    {
        if(!$category instanceof Category){
            return false;
        }

        if($category->id){
            $stmt = $this->db->prepare('UPDATE categories SET name = :name, description= :description WHERE id = :id');
            return $stmt->execute([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'role' => $user->role,
                'id' => $user->id
            ]);
        }else{
            $stmt = $this->db->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');
            return $stmt->execute([
                'id'=>$category->id,
                'name' => $category->name,
                'description'=>$category->description,
                
            ]);
        }

       
    }
     public function delete(int $id):bool{
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute(['id' => $id]);
       
    }





}