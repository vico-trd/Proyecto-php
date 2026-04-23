<?php
/** @var App\Models\Category $category */
/** @var App\Models\Product[] $products */
/** @var JasonGrimes\Paginator $paginator */
$pages    = $paginator->getPages();
$prevUrl  = $paginator->getPrevUrl();
$nextUrl  = $paginator->getNextUrl();
?>
<?php include_once __DIR__ . '/../layout/header.php'; ?>

<!-- Cabecera de categoría -->
<div class="mb-4 pb-3 border-bottom">
    <a href="<?= BASE_URL ?>" class="text-decoration-none text-muted small">
        <i class="bi bi-house me-1"></i>Inicio
    </a>
    <span class="text-muted small mx-1">/</span>
    <span class="small fw-semibold"><?= htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8') ?></span>

    <h1 class="h3 mt-2 mb-1"><?= htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8') ?></h1>
    <?php if ($category->description): ?>
        <p class="text-muted mb-0"><?= htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</div>

<?php if (empty($products)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-bag-x fs-1"></i>
        <p class="mt-2">No hay productos disponibles en esta categoría.</p>
        <a href="<?= BASE_URL ?>" class="btn btn-dark mt-1">Volver al inicio</a>
    </div>
<?php else: ?>

                <div style="padding: 12px;">
                    <h3 style="font-size: 1rem; margin: 0 0 8px;">
                        <?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                    <p style="margin: 0 0 10px; color: #444; min-height: 38px;">
                        <?= htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p style="margin: 0 0 12px; font-weight: 700; color: #1f2937;">
                        <?= number_format((float)$product->price, 2) ?> EUR
                    </p>

                    <?php if ($product->stock > 0): ?>
                        <form method="POST" action="<?= BASE_URL ?>carrito/agregar"
                              style="display:flex; gap:6px; align-items:center;">
                            <input type="hidden" name="producto_id" value="<?= (int)$product->id ?>">
                            <input type="number" name="cantidad" value="1" min="1"
                                   max="<?= (int)$product->stock ?>"
                                   style="width:55px; padding:5px 6px; border:1px solid #ccc; border-radius:5px; font-size:0.9rem;">
                            <button type="submit"
                                    style="flex:1; background:#1f2937; color:#fff; border:none; padding:7px 10px;
                                           border-radius:5px; cursor:pointer; font-size:0.9rem; font-weight:600;">
                                🛒 Añadir
                            </button>
                        </form>
                    <?php else: ?>
                        <p style="color:#e74c3c; font-size:0.85rem; margin:0;">Sin stock</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginación Bootstrap -->
    <?php if ($pages): ?>
        <nav aria-label="Paginación de productos">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $prevUrl ? '' : 'disabled' ?>">
                    <a class="page-link" href="<?= $prevUrl ? htmlspecialchars($prevUrl, ENT_QUOTES, 'UTF-8') : '#' ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                <?php foreach ($pages as $page): ?>
                    <li class="page-item <?= $page['isCurrent'] ? 'active' : '' ?> <?= $page['url'] === null ? 'disabled' : '' ?>">
                        <?php if ($page['url'] === null): ?>
                            <span class="page-link">&hellip;</span>
                        <?php else: ?>
                            <a class="page-link" href="<?= htmlspecialchars($page['url'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= (int)$page['num'] ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>

                <li class="page-item <?= $nextUrl ? '' : 'disabled' ?>">
                    <a class="page-link" href="<?= $nextUrl ? htmlspecialchars($nextUrl, ENT_QUOTES, 'UTF-8') : '#' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

<?php endif; ?>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>
