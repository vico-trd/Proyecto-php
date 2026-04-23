<?php
/** @var App\Models\Product $producto */
/** @var App\Models\Category[] $categorias */
/** @var array $errores */
/** @var array $old */
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h4>
            </div>
            <div class="card-body p-4">

                <?php if (!empty($errores['general'])): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($errores['general'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>productos/editar/<?= (int)$producto->id ?>" enctype="multipart/form-data">

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                class="form-control <?= !empty($errores['name']) ? 'is-invalid' : '' ?>"
                                value="<?= htmlspecialchars($old['name'] ?? $producto->name, ENT_QUOTES, 'UTF-8') ?>" required>
                            <?php if (!empty($errores['name'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errores['name'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="category_id" class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                            <select id="category_id" name="category_id"
                                    class="form-select <?= !empty($errores['category_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">Selecciona…</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= (int)$categoria->id ?>"
                                        <?= ((int)($old['category_id'] ?? $producto->category_id) === (int)$categoria->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria->name, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errores['category_id'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errores['category_id'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Descripción</label>
                        <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($old['description'] ?? $producto->description, ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label fw-semibold">Precio (&euro;) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" id="price" name="price"
                                   class="form-control <?= !empty($errores['price']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars((string)($old['price'] ?? $producto->price), ENT_QUOTES, 'UTF-8') ?>" required>
                            <?php if (!empty($errores['price'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errores['price'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="stock" class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                            <input type="number" min="0" id="stock" name="stock"
                                   class="form-control <?= !empty($errores['stock']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars((string)($old['stock'] ?? $producto->stock), ENT_QUOTES, 'UTF-8') ?>" required>
                            <?php if (!empty($errores['stock'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errores['stock'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="image" class="form-label fw-semibold">Nueva imagen</label>
                            <input type="file" id="image" name="image"
                                   class="form-control <?= !empty($errores['image']) ? 'is-invalid' : '' ?>"
                                   accept=".jpg,.jpeg,.png,.webp,.gif">
                            <?php if (!empty($errores['image'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errores['image'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                            <?php if (!empty($producto->image)): ?>
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <img src="/Proyecto-php/public/uploads/images/<?= htmlspecialchars($producto->image, ENT_QUOTES, 'UTF-8') ?>"
                                         alt="Imagen actual" class="rounded" style="width:56px;height:56px;object-fit:cover;">
                                    <span class="form-text">Imagen actual (déjalo vacío para mantenerla)</span>
                                </div>
                            <?php else: ?>
                                <div class="form-text">Sin imagen. Formatos: jpg, jpeg, png, webp, gif</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-save me-1"></i>Guardar cambios
                        </button>
                        <a href="<?= BASE_URL ?>productos/gestion" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
