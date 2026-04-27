<?php

use App\Controllers\HomeController;
use App\Controllers\AuthController;

$router->get('', [HomeController::class, 'index']);

$router->get('register', [AuthController::class, 'showRegister']);
$router->post('register', [AuthController::class, 'register']);

$router->get('login', [AuthController::class, 'showLogin']);
$router->post('login', [AuthController::class, 'login']);

$router->get('logout', [AuthController::class, 'logout']);

$router->get('auth/google', [AuthController::class, 'googleRedirect']);
$router->get('auth/google/callback', [AuthController::class, 'googleCallback']);

$router->get('admin/users/create', [AuthController::class, 'showCreateUser']);
$router->post('admin/users/create', [AuthController::class, 'createUser']);

$router->get('inicio', [\App\Controllers\HomeController::class, 'index']);
$router->get('carrito', [\App\Controllers\CarritoController::class, 'index']);
$router->post('carrito/agregar', [\App\Controllers\CarritoController::class, 'agregar']);
$router->post('carrito/incrementar', [\App\Controllers\CarritoController::class, 'incrementar']);
$router->post('carrito/decrementar', [\App\Controllers\CarritoController::class, 'decrementar']);
$router->post('carrito/eliminar', [\App\Controllers\CarritoController::class, 'eliminar']);
$router->post('carrito/vaciar', [\App\Controllers\CarritoController::class, 'vaciar']);


// Rutas para el maquetado que estamos creando
$router->get('producto', [\App\Controllers\ProductoController::class, 'show']);
$router->get('checkout', [\App\Controllers\CheckoutController::class, 'index']);
$router->post('checkout/confirmar', [\App\Controllers\CheckoutController::class, 'confirmar']);
$router->get('confirmacion', [\App\Controllers\CheckoutController::class, 'confirmacion']);