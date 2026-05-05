<?php
namespace App\Models;

//REPRESENTA UNA FILA DE LA TABLA USERS, el campo role, solo tiene dos valores 'user o admin'
class User{
    public int $id ;
    public string $name;
    public string $email;
    public string $password;
    public string $role;

    public function __construct(int $id, string $name, string $email, string $password, string $role){
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }
}
