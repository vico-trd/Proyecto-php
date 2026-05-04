<?php

namespace App\Controllers;

class BaseController
{
    /**
     * Renderiza una vista envolviéndola entre header y footer.
     * Extrae $data como variables locales accesibles en la vista.
     *
     * @param string $view  Ruta relativa de la vista sin extensión (ej: 'auth/login')
     * @param array  $data  Variables que se pasarán a la vista
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            if ($view !== 'errors/404' && defined('BASE_URL')) {
                header('Location: ' . BASE_URL . '404');
                exit;
            }

            header('HTTP/1.1 404 Not Found');
            echo 'Recurso no encontrado.';
            return;
        }

        require __DIR__ . '/../views/layout/header.php';
        require $viewPath;
        require __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Redirige a una URL relativa dentro del proyecto y termina la ejecución.
     *
     * @param string $url  Segmento de ruta relativo (ej: 'login', 'productos/gestion')
     * @return void
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_PATH . $url);
        exit;
    }
}
