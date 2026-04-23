<?php
/** @var App\Models\Product[] $productos */
/** @var array<int, string> $categoryMap */
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-box-seam me-2"></i>Inventario de Productos</h1>
    <a href="<?= BASE_URL ?>productos/crear" class="btn btn-dark">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
    </a>
</div>

<?php if (isset($_SESSION['product_save']) && $_SESSION['product_save'] === 'complete'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>Producto guardado correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['product_save']); ?>
<?php endif; ?>

<?php if (empty($productos)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1"></i>
        <p class="mt-2">No hay productos registrados.</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:80px">Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td class="text-muted"><?= (int)$producto->id ?></td>
                        <td>
                            <?php if (!empty($producto->image)): ?>
                                <img src="/Proyecto-php/public/uploads/images/<?= htmlspecialchars($producto->image, ENT_QUOTES, 'UTF-8') ?>"
                                     alt="Imagen"
                                     class="rounded" style="width:52px;height:52px;object-fit:cover;">
                            <?php else: ?>
                                <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:52px;height:52px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold"><?= htmlspecialchars($producto->name, ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="badge bg-secondary">
                                <?= htmlspecialchars($categoryMap[$producto->category_id] ?? 'Sin categoría', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td class="fw-semibold text-danger"><?= number_format((float)$producto->price, 2) ?> &euro;</td>
                        <td>
                            <span class="badge <?= (int)$producto->stock > 0 ? 'bg-success' : 'bg-danger' ?>">
                                <?= (int)$producto->stock ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
