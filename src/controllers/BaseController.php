<?php

namespace App\Controllers;

class BaseController
{
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

    protected function redirect(string $url): void
    {
        header("Location: /Proyecto-php/public/$url");
        exit;
    }
}
