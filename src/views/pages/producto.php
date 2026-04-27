<?php include_once __DIR__ . '/../layout/header.php'; ?>

<main class="container" style="padding: 50px 20px;">
    <div style="display: flex; gap: 40px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <img src="https://via.placeholder.com/500" alt="Producto" style="width: 100%; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        </div>

        <div style="flex: 1; min-width: 300px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Producto: <?= htmlspecialchars($producto->name) ?></h1>
            <p style="color: #e74c3c; font-size: 2rem; font-weight: bold; margin-bottom: 20px;"><?= htmlspecialchars($producto->price) ?></p>
            <p style="line-height: 1.6; color: #555; margin-bottom: 30px;">
                <?= htmlspecialchars($producto->description) ?>
            </p>
            
            <form method="POST" action="<?= BASE_URL ?>carrito/agregar" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <input type="hidden" name="producto_id" value="1">
                <input type="number" name="cantidad" value="1" min="1" max="99"
                    style="width:70px; padding:8px; border:1px solid #ddd; border-radius:5px; font-size:1rem;">
                <button type="submit" class="btn-carrito" style="flex:1; border:none; cursor:pointer; padding:12px 20px;">
                    Añadir al Carrito
                </button>
            </form>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>