<?php
/** @var array $errores */
/** @var array $old */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Categoría</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 30px auto; padding: 0 20px; }
        h1 { color: #333; }
        label { display: block; margin-top: 16px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        textarea { resize: vertical; min-height: 80px; }
        .error { color: #cc3333; font-size: 13px; margin-top: 4px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 4px; text-decoration: none; color: #fff; font-size: 14px; border: none; cursor: pointer; margin-top: 20px; }
        .btn-primary { background-color: #0066cc; }
        .btn-secondary { background-color: #666; }
        .btn:hover { opacity: 0.85; }
        .form-actions { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Crear Categoría</h1>

    <form method="POST" action="<?= BASE_URL ?>categorias/crear">
        <label for="name">Nombre *</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        <?php if (!empty($errores['name'])): ?>
            <div class="error"><?= htmlspecialchars($errores['name'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <label for="description">Descripción</label>
        <textarea id="description" name="description"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="<?= BASE_URL ?>categorias" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</body>
</html>
