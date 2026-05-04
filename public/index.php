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
define('BASE_URL',    '/Proyecto-php/public/index.php?url=');
define('BASE_PATH',   '/Proyecto-php/public/');
define('UPLOADS_URL', '/Proyecto-php/public/uploads/images/');

// Crear el router y registrar rutas
$router = new Router();

require_once __DIR__ . '/../routes/web.php';

$router->dispatch();
