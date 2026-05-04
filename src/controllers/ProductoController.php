<?php

namespace App\Controllers;

use App\Middleware\AdminMiddleware;
use App\Models\Category;
use App\Requests\ProductoRequest;
use App\Services\CategoriaService;
use App\Services\ProductoService;


class ProductoController extends BaseController
{
    private ProductoService $productoService;
    private CategoriaService $categoriaService;

    public function __construct()
    {
        $this->productoService = new ProductoService();
        $this->categoriaService = new CategoriaService();
    }

    private function requireAdmin(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle(fn() => true);
    }

    /**
     * Muestra la ficha detalle de un producto (GET /producto/{id}).
     *
     * @param int $id  ID del producto
     */
    public function show(int $id): void
    {
        $producto = $this->productoService->obtenerPorId($id);

        if (!$producto) {
            $this->redirect('404');
            return;
        }

        require __DIR__ . '/../views/pages/producto.php';
    }


    /** Muestra la tabla de gestión de productos (GET /productos/gestion). Solo admin. */
    public function gestion(): void
    {
        $this->requireAdmin();

        $productos = $this->productoService->listar();
        $categorias = $this->categoriaService->listar();

        $categoryMap = [];
        foreach ($categorias as $categoria) {
            $categoryMap[$categoria->id] = $categoria->name;
        }

        require __DIR__ . '/../views/productos/gestion.php';
    }

    /** Muestra el formulario de creación de producto (GET /productos/crear). Solo admin. */
    public function crear(): void
    {
        $this->requireAdmin();

        $categorias = $this->categoriaService->listar();
        $errores = $_SESSION['errores'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errores'], $_SESSION['old']);

        require __DIR__ . '/../views/productos/crear.php';
    }

    /** Procesa la creación del producto (POST /productos/crear). Solo admin. */
    public function guardar(): void
    {
        $this->requireAdmin();

        $request = new ProductoRequest();

        if (!$request->validate($_POST, $_FILES)) {
            $_SESSION['errores'] = $request->getErrors();
            $_SESSION['old'] = $_POST;
            $this->redirect('productos/crear');
            return;
        }

        $data = $request->sanitize($_POST);
        $imageFile = $_FILES['image'] ?? null;

        $result = $this->productoService->crear($data, $imageFile);

        if ($result === true) {
            $_SESSION['product_save'] = 'complete';
            $this->redirect('productos/gestion');
            return;
        }

        $_SESSION['errores'] = ['general' => is_string($result) ? $result : 'No se pudo guardar el producto.'];
        $_SESSION['old'] = $_POST;
        $this->redirect('productos/crear');
    }

    /**
     * Muestra el formulario de edición de un producto (GET /productos/editar/{id}). Solo admin.
     *
     * @param int $id  ID del producto a editar
     */
    public function editar(int $id): void
    {
        $this->requireAdmin();

        $producto = $this->productoService->obtenerPorId($id);
        if (!$producto) {
            $this->redirect('404');
            return;
        }

        $categorias = $this->categoriaService->listar();
        $errores = $_SESSION['errores'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errores'], $_SESSION['old']);

        require __DIR__ . '/../views/productos/editar.php';
    }

    /**
     * Procesa la actualización de un producto (POST /productos/editar/{id}). Solo admin.
     *
     * @param int $id  ID del producto a actualizar
     */
    public function actualizar(int $id): void
    {
        $this->requireAdmin();

        $request = new ProductoRequest();

        if (!$request->validate($_POST, $_FILES)) {
            $_SESSION['errores'] = $request->getErrors();
            $_SESSION['old'] = $_POST;
            $this->redirect('productos/editar/' . $id);
            return;
        }

        $data = $request->sanitize($_POST);
        $imageFile = $_FILES['image'] ?? null;

        $result = $this->productoService->editar($id, $data, $imageFile);

        if ($result === true) {
            $_SESSION['product_save'] = 'complete';
            $this->redirect('productos/gestion');
            return;
        }

        $_SESSION['errores'] = ['general' => is_string($result) ? $result : 'No se pudo actualizar el producto.'];
        $_SESSION['old'] = $_POST;
        $this->redirect('productos/editar/' . $id);
    }

    /**
     * Elimina un producto (POST /productos/eliminar/{id}). Solo admin.
     *
     * @param int $id  ID del producto a eliminar
     */
    public function eliminar(int $id): void
    {
        $this->requireAdmin();

        $result = $this->productoService->eliminar($id);

        if ($result === true) {
            $_SESSION['product_delete'] = 'complete';
        } else {
            $_SESSION['product_error'] = is_string($result) ? $result : 'No se pudo eliminar el producto.';
        }

        $this->redirect('productos/gestion');
    }

    /**
     * Muestra el listado de productos de una categoría con paginación
     * (GET /categoria/{categoryId}/productos).
     *
     * @param int $categoryId  ID de la categoría
     */
    public function porCategoria(int $categoryId): void
    {
        $categoryId = (int)$categoryId;
        $page = (int)($_GET['page'] ?? 1);

        /** @var Category|null $category */
        $category = $this->categoriaService->obtenerPorId($categoryId);

        if (!$category) {
            $this->redirect('404');
            return;
        }

        $result = $this->productoService->listarPorCategoriaPaginado($categoryId, $page, 6);
        $products = $result['products'];
        $paginator = $result['paginator'];

        require __DIR__ . '/../views/productos/categoria.php';
    }
}
