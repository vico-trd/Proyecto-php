<?php

namespace App\Middleware;

class AdminMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['identity']) || $_SESSION['identity']->role !== 'admin') {
            http_response_code(403);
            echo "Acceso denegado. Solo administradores.";
            exit();
        }

        // Ejecuta la siguiente capa (controlador)
        return $next();
    }
}
