<?php

namespace App\Controllers;

class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "Vista no encontrada: $view";
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
