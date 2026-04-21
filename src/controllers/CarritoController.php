<?php

namespace App\Controllers;

class CarritoController
{
    public function index(): void
    {
        // Cargamos la vista del carrito (maquetado)
        require __DIR__ . '/../views/pages/carrito.php';
    }
}