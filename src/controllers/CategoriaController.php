<?php

namespace App\Controllers;

use App\Middleware\AdminMiddleware;
use App\Services\CategoriaService;
use App\Requests\CategoriaRequest;

class CategoriaController
{
    private CategoriaService $service;

    public function __construct()
    {
    $this->service = new CategoriaService();

    $middleware = new AdminMiddleware();
    $middleware->handle(fn() => true);
    }

    /**
     * Listado de categorías (GET /categorias)
     */
    public function index(): void
    {
        $categorias = $this->service->listar();
        require __DIR__ . '/../views/categoria/index.php';
    }

    /**
     * Mostrar formulario de creación (GET /categorias/crear)
     */
    public function crear(): void
    {
        $errores = $_SESSION['errores'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errores'], $_SESSION['old']);

        require __DIR__ . '/../views/categoria/crear.php';
    }

    /**
     * Procesar creación de categoría (POST /categorias/crear)
     */
    public function guardar(): void
    {
        $request = new CategoriaRequest();

        if (!$request->validate($_POST)) {
            $_SESSION['errores'] = $request->getErrors();
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . 'categorias/crear');
            exit();
        }

        $data = $request->sanitize($_POST);
        $resultado = $this->service->crear($data);

        if ($resultado === true) {
            $_SESSION['mensaje'] = 'Categoría creada correctamente.';
            header('Location: ' . BASE_URL . 'categorias');
        } else {
            $_SESSION['errores'] = ['name' => is_string($resultado) ? $resultado : 'Error al crear la categoría.'];
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . 'categorias/crear');
        }
        exit();
    }

    /**
     * Mostrar formulario de edición (GET /categorias/editar/{id})
     */
    public function editar(int $id): void
    {
        $categoria = $this->service->obtenerPorId((int)$id);

        if (!$categoria) {
            http_response_code(404);
            echo 'Categoría no encontrada.';
            return;
        }

        $errores = $_SESSION['errores'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errores'], $_SESSION['old']);

        require __DIR__ . '/../views/categoria/editar.php';
    }

    /**
     * Procesar edición de categoría (POST /categorias/editar/{id})
     */
    public function actualizar(int $id): void
    {
        $id = (int)$id;
        $request = new CategoriaRequest();

        if (!$request->validate($_POST)) {
            $_SESSION['errores'] = $request->getErrors();
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . 'categorias/editar/' . $id);
            exit();
        }

        $data = $request->sanitize($_POST);
        $resultado = $this->service->editar($id, $data);

        if ($resultado === true) {
            $_SESSION['mensaje'] = 'Categoría actualizada correctamente.';
            header('Location: ' . BASE_URL . 'categorias');
        } else {
            $_SESSION['errores'] = ['name' => is_string($resultado) ? $resultado : 'Error al actualizar la categoría.'];
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . 'categorias/editar/' . $id);
        }
        exit();
    }

    /**
     * Eliminar categoría (POST /categorias/eliminar/{id})
     */
    public function eliminar(int $id): void
    {
        $id = (int)$id;
        $resultado = $this->service->eliminar($id);

        if ($resultado) {
            $_SESSION['mensaje'] = 'Categoría eliminada correctamente.';
        } else {
            $_SESSION['errores'] = ['general' => 'No se pudo eliminar la categoría.'];
        }

        header('Location: ' . BASE_URL . 'categorias');
        exit();
    }
    public function ver(): void
    {
        // Por ahora, como es maquetado, no necesitamos llamar al service
        require __DIR__ . '/../views/categoria/ver.php';
    }
}
