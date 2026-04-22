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
            http_response_code(403);
            echo "Acceso denegado. Solo administradores.";
        $identity = $_SESSION['identity'] ?? null;
        $role = null;

        if (is_object($identity)) {
            $role = $identity->role ?? $identity->rol ?? null;
        } elseif (is_array($identity)) {
            $role = $identity['role'] ?? $identity['rol'] ?? null;
        }

        if ($role !== 'admin') {
            if (defined('BASE_URL')) {
                header('Location: ' . BASE_URL . '404');
                exit();
            }

            header('HTTP/1.1 404 Not Found');
            echo 'Recurso no encontrado.';
            exit();
        }

        // Ejecuta la siguiente capa (controlador)
        return $next();
    }
}
