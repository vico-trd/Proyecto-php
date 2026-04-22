<?php

namespace App\Controllers;

use App\Middleware\AdminMiddleware;
use App\Models\Category;
use App\Requests\ProductoRequest;
use App\Services\CategoriaService;
use App\Services\ProductoService;

class ProductoController
{
    private ProductoService $productoService;
    private CategoriaService $categoriaService;

    public function __construct()
    {
        $this->productoService = new ProductoService();
        $this->categoriaService = new CategoriaService();
    }

    private function authorizeAdmin(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle(fn() => true);
    }

    /**
     * Esta función es la que busca el Router cuando pones /producto
     */
    public function show(): void
    {
        // Esto le dice a PHP: "Ve a la carpeta de vistas y escupe el HTML de producto.php"
        require __DIR__ . '/../views/pages/producto.php';
    }

    public function gestion(): void
    {
        $this->authorizeAdmin();

        $productos = $this->productoService->listar();
        $categorias = $this->categoriaService->listar();

        $categoryMap = [];
        foreach ($categorias as $categoria) {
            $categoryMap[$categoria->id] = $categoria->name;
        }

        require __DIR__ . '/../views/productos/gestion.php';
    }

    public function crear(): void
    {
        $this->authorizeAdmin();

        $categorias = $this->categoriaService->listar();
        $errores = $_SESSION['errores'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errores'], $_SESSION['old']);

        require __DIR__ . '/../views/productos/crear.php';
    }

    public function guardar(): void
    {
        $this->authorizeAdmin();

        $request = new ProductoRequest();

        if (!$request->validate($_POST, $_FILES)) {
            $_SESSION['errores'] = $request->getErrors();
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . 'productos/crear');
            exit();
        }

        $data = $request->sanitize($_POST);
        $imageFile = $_FILES['image'] ?? null;

        $result = $this->productoService->crear($data, $imageFile);

        if ($result === true) {
            $_SESSION['product_save'] = 'complete';
            header('Location: ' . BASE_URL . 'productos/gestion');
            exit();
        }

        $_SESSION['errores'] = ['general' => is_string($result) ? $result : 'No se pudo guardar el producto.'];
        $_SESSION['old'] = $_POST;
        header('Location: ' . BASE_URL . 'productos/crear');
        exit();
    }

    public function porCategoria(int $categoryId): void
    {
        $categoryId = (int)$categoryId;
        $page = (int)($_GET['page'] ?? 1);

        /** @var Category|null $category */
        $category = $this->categoriaService->obtenerPorId($categoryId);

        if (!$category) {
            header('Location: ' . BASE_URL . '404');
            exit();
            return;
        }

        $result = $this->productoService->listarPorCategoriaPaginado($categoryId, $page, 6);
        $products = $result['products'];
        $paginator = $result['paginator'];

        require __DIR__ . '/../views/productos/categoria.php';
    }
}