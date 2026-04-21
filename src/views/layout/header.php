<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Ropa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <a class="nav-link" href="<?= BASE_URL ?>categoria">Tienda</a>

            <a class="nav-link me-3" href="<?= BASE_URL ?>carrito">
                🛒 <span class="cart-badge">1</span>
            </a>

            <?php if (isset($_SESSION['user'])): ?>
                <span class="nav-link text-secondary me-2">Hola, <?= $_SESSION['user']['name'] ?></span>
                <a class="nav-link btn btn-outline-danger btn-sm" href="<?= BASE_URL ?>logout">Salir</a>
            <?php else: ?>
                <a class="nav-link" href="<?= BASE_URL ?>login">Login</a>
                <a class="nav-link" href="<?= BASE_URL ?>register">Registro</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container">