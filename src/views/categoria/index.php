<?php
/** @var App\Models\Category[] $categorias */
$mensaje = $_SESSION['mensaje'] ?? null;
$errorGeneral = $_SESSION['errores']['general'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['errores']);
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-tags me-2"></i>Gestión de Categorías</h1>
    <a href="<?= BASE_URL ?>categorias/crear" class="btn btn-dark">
        <i class="bi bi-plus-lg me-1"></i>Nueva Categoría
    </a>
</div>

<?php if ($mensaje): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($errorGeneral): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($errorGeneral, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if (empty($categorias)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1"></i>
        <p class="mt-2">No hay categorías registradas.</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width:60px">#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <td class="text-muted"><?= (int)$cat->id ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-muted"><?= htmlspecialchars($cat->description, ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-end">
                            <a href="<?= BASE_URL ?>categoria/<?= (int)$cat->id ?>/productos" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-eye me-1"></i>Productos
                            </a>
                            <a href="<?= BASE_URL ?>categorias/editar/<?= (int)$cat->id ?>" class="btn btn-sm btn-warning me-1">
                                <i class="bi bi-pencil me-1"></i>Editar
                            </a>
                            <form method="POST" action="<?= BASE_URL ?>categorias/eliminar/<?= (int)$cat->id ?>" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría?');">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash me-1"></i>Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
