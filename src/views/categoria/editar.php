<?php
/** @var App\Models\Category $categoria */
/** @var array $errores */
/** @var array $old */
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Categoría</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= BASE_URL ?>categorias/editar/<?= (int)$categoria->id ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name"
                               class="form-control <?= !empty($errores['name']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($old['name'] ?? $categoria->name, ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php if (!empty($errores['name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errores['name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Descripción</label>
                        <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($old['description'] ?? $categoria->description, ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-save me-1"></i>Guardar cambios
                        </button>
                        <a href="<?= BASE_URL ?>categorias" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
