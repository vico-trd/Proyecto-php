<?php
/** @var App\Models\Product[] $productos */
/** @var array<int, string> $categoryMap */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Productos</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f5f5f5; font-weight: bold; }
        tr:hover { background-color: #fafafa; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; color: #fff; font-size: 14px; border: none; cursor: pointer; }
        .btn-primary { background-color: #0066cc; }
        .alert { padding: 12px 16px; border-radius: 4px; margin: 16px 0; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .thumb { width: 52px; height: 52px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
        .muted { color: #666; }
    </style>
</head>
<body>
    <h1>Inventario de Productos</h1>

    <a href="<?= BASE_URL ?>productos/crear" class="btn btn-primary">+ Nuevo Producto</a>

    <?php if (isset($_SESSION['product_save']) && $_SESSION['product_save'] === 'complete'): ?>
        <div class="alert alert-success">Producto guardado correctamente.</div>
        <?php unset($_SESSION['product_save']); ?>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
        <p class="muted">No hay productos registrados.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoria</th>
                    <th>Precio</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= (int)$producto->id ?></td>
                        <td>
                            <?php if (!empty($producto->image)): ?>
                                <img class="thumb" src="/Proyecto-php/public/uploads/images/<?= htmlspecialchars($producto->image, ENT_QUOTES, 'UTF-8') ?>" alt="Imagen de producto">
                            <?php else: ?>
                                <span class="muted">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($producto->name, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($categoryMap[$producto->category_id] ?? 'Sin categoria', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= number_format((float)$producto->price, 2) ?> EUR</td>
                        <td><?= (int)$producto->stock ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
