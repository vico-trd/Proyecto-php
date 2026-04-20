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
