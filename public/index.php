<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Iniciar sesión
session_start();

$router = new Router();

require_once __DIR__ . '/../routes/web.php';

$router->dispatch();