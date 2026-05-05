<?php

namespace App\Models;

class Category
{
    public ?int $id; // lo mismo lo asigna la base de datos
    public string $name;
    public string $description;

    public function __construct(string $name, string $description = '', ?int $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description; //es opcional
    }
}

