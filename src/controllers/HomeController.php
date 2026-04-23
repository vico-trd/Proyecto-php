<?php

namespace App\Controllers;

use App\Services\ProductoService;

class HomeController extends BaseController
{
    private ProductoService $productoService;

    public function __construct()
    {
        $this->productoService = new ProductoService();
    }

    public function index(): void
    {
        $productos = $this->productoService->listarRecientes(4);
        $this->render('home', compact('productos'));
    }
}
