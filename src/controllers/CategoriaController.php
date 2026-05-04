<?php

namespace App\Controllers;

use App\Middleware\AdminMiddleware;
use App\Services\CategoriaService;
use App\Requests\CategoriaRequest;

class CategoriaController extends BaseController
{
    private CategoriaService $service;

    public function __construct()
    {
        $this->service = new CategoriaService();
    }

    private function requireAdmin(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle(fn() => true);
    }

    /**
     * Listado de categorías (GET /categorias)
     */
    public function index(): void
    {
        $this->requireAdmin();
        $categorias = $this->service->listar();
        $this->render('categoria/index', compact('categorias'));
    }

    /**
     * Mostrar formulario de creación (GET /categorias/crear)
     */
    public function crear(): void
    {
        $this->requireAdmin();
        $errores = $_SESSION['errores'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errores'], $_SESSION['old']);

        $this->render('categoria/crear', compact('errores', 'old'));
    }

    /**
     * Procesar creación de categoría (POST /categorias/crear)
     */
    public function guardar(): void
    {
        $this->requireAdmin();
        $request = new CategoriaRequest();

        if (!$request->validate($_POST)) {
            $_SESSION['errores'] = $request->getErrors();
            $_SESSION['old'] = $_POST;
            $this->redirect('categorias/crear');
        }

        $data = $request->sanitize($_POST);
        $resultado = $this->service->crear($data);

        if ($resultado === true) {
            $_SESSION['mensaje'] = 'Categoría creada correctamente.';
            $this->redirect('categorias');
        } else {
            $_SESSION['errores'] = ['name' => is_string($resultado) ? $resultado : 'Error al crear la categoría.'];
            $_SESSION['old'] = $_POST;
            $this->redirect('categorias/crear');
        }
    }

    /**
     * Mostrar formulario de edición (GET /categorias/editar/{id})
     */
    public function editar(int $id): void
    {
        $this->requireAdmin();
        $categoria = $this->service->obtenerPorId((int)$id);

        if (!$categoria) {
            $this->redirect('404');
            return;
        }

        $errores = $_SESSION['errores'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errores'], $_SESSION['old']);

        $this->render('categoria/editar', compact('categoria', 'errores', 'old'));
    }

    /**
     * Procesar edición de categoría (POST /categorias/editar/{id})
     */
    public function actualizar(int $id): void
    {
        $this->requireAdmin();
        $id = (int)$id;
        $request = new CategoriaRequest();

        if (!$request->validate($_POST)) {
            $_SESSION['errores'] = $request->getErrors();
            $_SESSION['old'] = $_POST;
            $this->redirect('categorias/editar/' . $id);
        }

        $data = $request->sanitize($_POST);
        $resultado = $this->service->editar($id, $data);

        if ($resultado === true) {
            $_SESSION['mensaje'] = 'Categoría actualizada correctamente.';
            $this->redirect('categorias');
        } else {
            $_SESSION['errores'] = ['name' => is_string($resultado) ? $resultado : 'Error al actualizar la categoría.'];
            $_SESSION['old'] = $_POST;
            $this->redirect('categorias/editar/' . $id);
        }
    }

    /**
     * Eliminar categoría (POST /categorias/eliminar/{id})
     */
    public function eliminar(int $id): void
    {
        $this->requireAdmin();
        $id = (int)$id;
        $resultado = $this->service->eliminar($id);

        if ($resultado === true) {
            $_SESSION['mensaje'] = 'Categoría eliminada correctamente.';
        } else {
            $_SESSION['errores'] = ['general' => is_string($resultado) ? $resultado : 'No se pudo eliminar la categoría.'];
        }

        $this->redirect('categorias');
    }
}
