<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Iniciar sesión
session_start();

// Crear el router y registrar rutas
$router = new Router();

// --- Rutas ---
$router->get('', [App\Controllers\HomeController::class, 'index']);

// Despachar la petición
$router->dispatch();