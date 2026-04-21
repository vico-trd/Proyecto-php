<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Iniciar sesión
session_start();

// URL base del proyecto (ajustar si cambia la ubicación)
define('BASE_URL', '/Proyecto-php/public/index.php?url=');

// Crear el router y registrar rutas
$router = new Router();

require_once __DIR__ . '/../routes/web.php';


// --- Rutas de Categorías ---
$router->get('categorias', [\App\Controllers\CategoriaController::class, 'index']);
$router->get('categorias/crear', [\App\Controllers\CategoriaController::class, 'crear']);
$router->post('categorias/crear', [\App\Controllers\CategoriaController::class, 'guardar']);
$router->get('categorias/editar/{id}', [\App\Controllers\CategoriaController::class, 'editar']);
$router->post('categorias/editar/{id}', [\App\Controllers\CategoriaController::class, 'actualizar']);
$router->post('categorias/eliminar/{id}', [\App\Controllers\CategoriaController::class, 'eliminar']);

$router->dispatch();
