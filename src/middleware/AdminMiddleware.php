<?php

namespace App\Middleware;

class AdminMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $identity = $_SESSION['identity'] ?? null;
        $role = null;

        if (is_object($identity)) {
            $role = $identity->role ?? $identity->rol ?? null;
        } elseif (is_array($identity)) {
            $role = $identity['role'] ?? $identity['rol'] ?? null;
        }

        if ($role !== 'admin') {
            http_response_code(403);
            echo "Acceso denegado. Solo administradores.";
            exit();
        }

        // Ejecuta la siguiente capa (controlador)
        return $next();
    }
}
