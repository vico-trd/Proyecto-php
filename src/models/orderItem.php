<?php
namespace App\Models;

class OrderItem{


    public int $id;
    public int $order_id;
    public int $product_id;
    public int $quantity;
    public float $price;

    public function __construct(int $quantity, float $price){
        $this->id=$id;
        $this->order_id=$order_id;
        $this->product_id=$product_id;
        $this->quantity=$quantity;
        $this->price=$price;



    }



}