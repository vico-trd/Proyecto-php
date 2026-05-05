<?php
namespace App\Models;

class Order
{
    public int $id;
    public ?int $user_id; //ES ASI PORQUE UN PEDIDO PUEDE PERTENECER A UN USUARIO SIN CUENTA(INVITADO)
    public float $total;
    public string $status;
    public ?string $created_at; //la pone la base de datos

    public function __construct(
        int $id,
        ?int $user_id,
        float $total,
        string $status,
        ?string $created_at = null
    ) {
        $this->id         = $id;
        $this->user_id    = $user_id;
        $this->total      = $total;
        $this->status     = $status;
        $this->created_at = $created_at;
    }
}
