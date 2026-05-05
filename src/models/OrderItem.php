<?php
namespace App\Models;

//REPRESENTA UNA LINEA DE PEDIDO
class OrderItem{


    public int $id;
    public int $order_id;
    public int $product_id;
    public int $quantity;
    public float $price; //el precio actual del producto, si mañana se actualiza, no se cambia

    public function __construct(int $id, int $order_id, int $product_id, int $quantity, float $price){
        $this->id=$id;
        $this->order_id=$order_id;
        $this->product_id=$product_id;
        $this->quantity=$quantity;
        $this->price=$price;
    }
}