<?php
/** @var App\Models\Category[] $categorias */
$mensaje = $_SESSION['mensaje'] ?? null;
$errorGeneral = $_SESSION['errores']['general'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['errores']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 30px auto; padding: 0 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f5f5f5; font-weight: bold; }
        tr:hover { background-color: #fafafa; }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; color: #fff; font-size: 14px; border: none; cursor: pointer; }
        .btn-primary { background-color: #0066cc; }
        .btn-warning { background-color: #e6a817; }
        .btn-danger { background-color: #cc3333; }
        .btn:hover { opacity: 0.85; }
        .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
        .actions form { display: inline; }
    </style>
</head>
<body>
    <h1>Categorías</h1>

    <a href="<?= BASE_URL ?>categorias/crear" class="btn btn-primary">+ Nueva Categoría</a>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if ($errorGeneral): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorGeneral, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (empty($categorias)): ?>
        <p>No hay categorías registradas.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <td><?= (int)$cat->id ?></td>
                        <td><?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($cat->description, ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="actions">
                            <a href="<?= BASE_URL ?>categoria/<?= (int)$cat->id ?>/productos" class="btn btn-primary">Ver productos</a>
                            <a href="<?= BASE_URL ?>categorias/editar/<?= (int)$cat->id ?>" class="btn btn-warning">Editar</a>
                            <form method="POST" action="<?= BASE_URL ?>categorias/eliminar/<?= (int)$cat->id ?>" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?');">
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
