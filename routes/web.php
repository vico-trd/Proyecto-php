<?php

use App\Controllers\HomeController;
use App\Controllers\AuthController;

$router->get('', [HomeController::class, 'index']);

$router->get('register', [AuthController::class, 'showRegister']);
$router->post('register', [AuthController::class, 'register']);

$router->get('login', [AuthController::class, 'showLogin']);
$router->post('login', [AuthController::class, 'login']);

$router->get('logout', [AuthController::class, 'logout']);

$router->get('admin/users/create', [AuthController::class, 'showCreateUser']);
$router->post('admin/users/create', [AuthController::class, 'createUser']);

$router->get('inicio', [\App\Controllers\HomeController::class, 'index']);
$router->get('carrito', [\App\Controllers\CarritoController::class, 'index']);


// Rutas para el maquetado que estamos creando
$router->get('producto', [\App\Controllers\ProductoController::class, 'show']);
$router->get('checkout', [\App\Controllers\CheckoutController::class, 'index']);
$router->get('confirmacion', [\App\Controllers\CheckoutController::class, 'confirmacion']);