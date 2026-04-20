<?php
namespace App\Models;


class product {

public int $id;
public string $name;
public int $category_id;
public string $text;
public float $price;
public int $stock;
public string $image;


public function __construct(string $name, id $category_id, string $text, float $price, int $stock, string $image ){

$this->id=$id;
$this->name=$name;
$this->category_id=$category_id;
$this->text=$text;
$this->price=$price;
$this->stock=$stock;
$this->image=$image;



}


}












?>