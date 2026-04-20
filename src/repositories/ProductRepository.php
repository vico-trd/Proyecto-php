<?php

namespace App\Repositories;

use PDO;
use App\Core\DataBase;
use App\Models\Product;



class ProductRepository implements RepositoryInterface{

private PDO $db;

public function __construct(){
    $this->db=Database::getInstance()->getConnection();


}

    public function findById(int $id): ?Product
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();



if($data){
    return new Product(
        id: $data['id'],
        name:  $data['name'],
        category_id: $data['category_id'],
        description: $data['description'],
        price: $data['price'],
        stock: $data['stock'],
        image: $data['image']
    );
    }
}

public function findAll(): array{
        $stmt= $this->db->query('SELECT * FROM products');
        $products=[];
        while ($data = $stmt->fetch()) {
            $products[] = new Product(
                id: $data['id'],
                name: $data['name'],
                category_id: $data['category_id'],
                description: $data['description'],
                price: $data['price'],
                stock: $data['stock'],
                image: $data['image']
            );
        }

        return $products;
    }

    public function save(object $product): bool
    {
        if(!$product instanceof Product){
            return false;
        }

        if($product->category_id){
            $stmt = $this->db->prepare('UPDATE products SET name = :name, description = :description, price = :price, stock = :stock WHERE category_id = :category_id');
            return $stmt->execute([
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock
            ]);
        }else{
            $stmt = $this->db->prepare('INSERT INTO products (name, category_id, description, price,stock,image) VALUES (:name, :category_id, :description, :price,:stock,:image)');
            return $stmt->execute([
                'name' => $product->name,
                'category_id' => $product->category_id,
                'description' => $product->description,
                'price' => $product->price,
                'stock'=>$product->stock
            ]);
        }
    }

    public function delete(int $category_id):bool{
        $stmt = $this->db->prepare('DELETE FROM products WHERE category_id = :category_id');
        return $stmt->execute(['category_id' => $category_id]);
    }
}
?>