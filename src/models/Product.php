<?php
namespace App\Models;

class Product
{
    public ?int $id; //se lo asigna la base de datos al crearlo
    public string $name;
    public int $category_id;
    public string $description;
    public float $price;
    public int $stock;
    public string $image;

    public function __construct(
        string $name,
        int $category_id,
        string $description,
        float $price,
        int $stock,
        string $image = '', //porque alguno podria no tener imagen
        ?int $id = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->category_id = $category_id;
        $this->description = $description;
        $this->price = $price;
        $this->stock = $stock;
        $this->image = $image;
    }
}