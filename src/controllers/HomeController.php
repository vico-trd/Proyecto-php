<?php

namespace App\Controllers;

use App\Services\CategoriaService;
use App\Services\ProductoService;

class HomeController extends BaseController
{
    private CategoriaService $categoriaService;
    private ProductoService $productoService;

    public function __construct()
    {
        $this->categoriaService = new CategoriaService();
        $this->productoService  = new ProductoService();
    }

    public function index(): void
    {
        $categorias = $this->categoriaService->listar();
        $productos  = array_slice($this->productoService->listar(), 0, 8);

        $this->render('home', [
            'categorias' => $categorias,
            'productos'  => $productos,
        ]);
    }
}
