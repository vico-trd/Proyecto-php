<?php
namespace App\Models;

class Order{
    public int $id;
    public int $user_id;
    public float $total;
    public string $status;


    public function __construct(int $id, int $user_id, float $total, string $status){

    $this->id=$id;
    $this->user_id=$user_id;
    $this->total=$total;
    $this->status=$status;





    }

}












?>