<?php

namespace App\Controllers;

class ProductoController
{
    /**
     * Esta función es la que busca el Router cuando pones /producto
     */
    public function show(): void
    {
        // Esto le dice a PHP: "Ve a la carpeta de vistas y escupe el HTML de producto.php"
        require __DIR__ . '/../views/pages/producto.php';
    }
}