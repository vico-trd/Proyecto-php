<?php
/** @var App\Models\Category[] $categorias */
/** @var array $errores */
/** @var array $old */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 30px auto; padding: 0 20px; }
        h1 { color: #333; }
        label { display: block; margin-top: 16px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 10px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        textarea { resize: vertical; min-height: 90px; }
        .error { color: #cc3333; font-size: 13px; margin-top: 4px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 4px; text-decoration: none; color: #fff; font-size: 14px; border: none; cursor: pointer; margin-top: 20px; }
        .btn-primary { background-color: #0066cc; }
        .btn-secondary { background-color: #666; }
    </style>
</head>
<body>
    <h1>Nuevo Producto</h1>

    <?php if (!empty($errores['general'])): ?>
        <div class="error"><?= htmlspecialchars($errores['general'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>productos/guardar" enctype="multipart/form-data">
        <label for="name">Nombre *</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        <?php if (!empty($errores['name'])): ?><div class="error"><?= htmlspecialchars($errores['name'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

        <label for="category_id">Categoria *</label>
        <select id="category_id" name="category_id" required>
            <option value="">Selecciona una categoria</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= (int)$categoria->id ?>" <?= ((int)($old['category_id'] ?? 0) === (int)$categoria->id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categoria->name, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errores['category_id'])): ?><div class="error"><?= htmlspecialchars($errores['category_id'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

        <label for="description">Descripcion</label>
        <textarea id="description" name="description"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

        <label for="price">Precio *</label>
        <input type="number" step="0.01" min="0" id="price" name="price" value="<?= htmlspecialchars($old['price'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        <?php if (!empty($errores['price'])): ?><div class="error"><?= htmlspecialchars($errores['price'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

        <label for="stock">Stock *</label>
        <input type="number" min="0" id="stock" name="stock" value="<?= htmlspecialchars($old['stock'] ?? '0', ENT_QUOTES, 'UTF-8') ?>" required>
        <?php if (!empty($errores['stock'])): ?><div class="error"><?= htmlspecialchars($errores['stock'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

        <label for="image">Imagen</label>
        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp,.gif">
        <?php if (!empty($errores['image'])): ?><div class="error"><?= htmlspecialchars($errores['image'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

        <br>
        <button type="submit" class="btn btn-primary">Guardar Producto</button>
        <a href="<?= BASE_URL ?>productos/gestion" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
