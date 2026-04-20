<?php
namespace App\Repositories;

use PDO;
use App\Core\Database;
use App\Models\User;

class UserRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();

        if ($data) {
            return new User(
                id: (int)$data['id'],
                name: $data['name'],
                email: $data['email'],
                password: $data['password'],
                role: $data['role']
            );
        }

        return null;
    }

    public function findById(int $id): ?User{
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new User(
                id: (int)$data['id'],
                name: $data['name'],
                email: $data['email'],
                password: $data['password'],
                role: $data['role']
            );
        }

        return null;
    }



    public function findAll(): array{
        $stmt= $this->db->query('SELECT * FROM users');
        $users=[];
        while ($data = $stmt->fetch()) {
            $users[] = new User(
                id: (int)$data['id'],
                name: $data['name'],
                email: $data['email'],
                password: $data['password'],
                role: $data['role']
            );
        }

        return $users;
    }

    public function save(User $user): bool
    {
        if(!$user instanceof User){
            return false;
        }

        if($user->id){
            $stmt = $this->db->prepare('UPDATE users SET name = :name, email = :email, password = :password, role = :role WHERE id = :id');
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

    public function delete(int $id):bool{
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    


    

}















?>