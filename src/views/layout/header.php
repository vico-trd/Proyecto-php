<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Ropa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .navbar { background-color: #1a1a1a; }
        .nav-link { color: #ffffff !important; }
        .nav-link:hover { color: #ff4757 !important; }
        /* Estilo para el contador del carrito */
        .cart-badge {
            background-color: #ff4757;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            vertical-align: top;
            margin-left: -5px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>">CLOTHING STORE</a>
        
        <div class="navbar-nav ms-auto align-items-center">
            <a class="nav-link me-3" href="<?= BASE_URL ?>carrito">
                🛒
                <?php
                $_cartCount = 0;
                if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
                    $_cartCount = array_sum($_SESSION['carrito']);
                }
                ?>
                <?php if ($_cartCount > 0): ?>
                    <span class="cart-badge"><?= (int)$_cartCount ?></span>
                <?php endif; ?>
            </a>

            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a class="nav-link" href="<?= BASE_URL ?>categorias">Gestión Categorías</a>
                    <a class="nav-link" href="<?= BASE_URL ?>productos/gestion">Gestión Productos</a>
                <?php endif; ?>
                <a class="nav-link" href="<?= BASE_URL ?>mis-pedidos">Mis pedidos</a>
                <span class="nav-link text-secondary me-2">Hola, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                <a class="nav-link btn btn-outline-danger btn-sm" href="<?= BASE_URL ?>logout">Salir</a>
            <?php else: ?>
                <a class="nav-link" href="<?= BASE_URL ?>login">Login</a>
                <a class="nav-link" href="<?= BASE_URL ?>register">Registro</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container">