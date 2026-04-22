<?php

namespace App\Controllers;

class CheckoutController
{
    public function index(): void
    {
        require __DIR__ . '/../views/pages/checkout.php';
    }

    public function confirmacion(): void
    {
        require __DIR__ . '/../views/pages/confirmacion.php';
    }
}
