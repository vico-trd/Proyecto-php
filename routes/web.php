<?php

use App\Controllers\HomeController;
use App\Controllers\AuthController;

$router->get('', [HomeController::class, 'index']);

$router->get('register', [AuthController::class, 'showRegister']);
$router->post('register', [AuthController::class, 'register']);

$router->get('login', [AuthController::class, 'showLogin']);
$router->post('login', [AuthController::class, 'login']);

$router->get('logout', [AuthController::class, 'logout']);

$router->get('forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('forgot-password', [AuthController::class, 'forgotPassword']);

$router->get('reset-password', [AuthController::class, 'showResetPassword']);
$router->post('reset-password', [AuthController::class, 'resetPassword']);

$router->get('auth/google', [AuthController::class, 'googleRedirect']);
$router->get('auth/google/callback', [AuthController::class, 'googleCallback']);

$router->get('admin/users/create', [AuthController::class, 'showCreateUser']);
$router->post('admin/users/create', [AuthController::class, 'createUser']);

$router->get('carrito', [\App\Controllers\CarritoController::class, 'index']);
$router->post('carrito/agregar', [\App\Controllers\CarritoController::class, 'agregar']);
$router->post('carrito/incrementar', [\App\Controllers\CarritoController::class, 'incrementar']);
$router->post('carrito/decrementar', [\App\Controllers\CarritoController::class, 'decrementar']);
$router->post('carrito/eliminar', [\App\Controllers\CarritoController::class, 'eliminar']);
$router->post('carrito/vaciar', [\App\Controllers\CarritoController::class, 'vaciar']);


// Rutas para el maquetado que estamos creando
$router->get('checkout', [\App\Controllers\CheckoutController::class, 'index']);
$router->post('checkout/procesar', [\App\Controllers\CheckoutController::class, 'procesar']);
$router->post('checkout/paypal/crear', [\App\Controllers\CheckoutController::class, 'paypalCrear']);
$router->get('checkout/paypal/exito', [\App\Controllers\CheckoutController::class, 'paypalExito']);
$router->get('checkout/paypal/cancelar', [\App\Controllers\CheckoutController::class, 'paypalCancelar']);
$router->get('confirmacion', [\App\Controllers\CheckoutController::class, 'confirmacion']);
$router->get('producto/{id}', [\App\Controllers\ProductoController::class, 'show']);
$router->get('mis-pedidos', [\App\Controllers\PedidoController::class, 'index']);
$router->get('mis-pedidos/ver', [\App\Controllers\PedidoController::class, 'ver']);

// --- Rutas de Categorías ---
$router->get('categorias', [\App\Controllers\CategoriaController::class, 'index']);
$router->get('categorias/crear', [\App\Controllers\CategoriaController::class, 'crear']);
$router->post('categorias/crear', [\App\Controllers\CategoriaController::class, 'guardar']);
$router->get('categorias/editar/{id}', [\App\Controllers\CategoriaController::class, 'editar']);
$router->post('categorias/editar/{id}', [\App\Controllers\CategoriaController::class, 'actualizar']);
$router->post('categorias/eliminar/{id}', [\App\Controllers\CategoriaController::class, 'eliminar']);

// --- Rutas de Productos (inventario, protegidas por AdminMiddleware) ---
$router->get('productos/gestion', [\App\Controllers\ProductoController::class, 'gestion']);
$router->get('productos/crear', [\App\Controllers\ProductoController::class, 'crear']);
$router->post('productos/guardar', [\App\Controllers\ProductoController::class, 'guardar']);
$router->get('productos/editar/{id}', [\App\Controllers\ProductoController::class, 'editar']);
$router->post('productos/editar/{id}', [\App\Controllers\ProductoController::class, 'actualizar']);
$router->post('productos/eliminar/{id}', [\App\Controllers\ProductoController::class, 'eliminar']);

// --- Ruta pública de productos por categoría con paginación ---
$router->get('categoria/{id}/productos', [\App\Controllers\ProductoController::class, 'porCategoria']);

// --- Ruta de error ---
$router->get('404', [\App\Controllers\ErrorController::class, 'notFound']);
