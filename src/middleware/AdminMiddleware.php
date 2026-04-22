<?php

namespace App\Middleware;

class AdminMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            if (defined('BASE_URL')) {
                header('Location: ' . BASE_URL . '404');
                exit();
            }

            http_response_code(404);
            echo 'Recurso no encontrado.';
            exit();
        }

        return $next();
    }
}
