<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/Proyecto-php/public/">E-commerce</a>
        <div class="navbar-nav ms-auto">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a class="nav-link" href="/Proyecto-php/public/admin/users/create">Crear usuario</a>
                <?php endif; ?>
                <span class="nav-link text-light"><?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                <a class="nav-link" href="/Proyecto-php/public/logout">Cerrar sesión</a>
            <?php else: ?>
                <a class="nav-link" href="/Proyecto-php/public/login">Login</a>
                <a class="nav-link" href="/Proyecto-php/public/register">Registro</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container mt-4">
