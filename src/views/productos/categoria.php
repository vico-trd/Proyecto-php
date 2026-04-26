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

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-4">
        <?php foreach ($products as $product): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <a href="<?= BASE_URL ?>producto/<?= $product->id ?>">
                        <?php if (!empty($product->image)): ?>
                            <img src="/Proyecto-php/public/uploads/images/<?= htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>"
                                 class="card-img-top" style="height:220px;object-fit:cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:220px;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </a>

                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title fw-semibold mb-1">
                            <a href="<?= BASE_URL ?>producto/<?= $product->id ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h6>
                        <p class="card-text text-muted small flex-grow-1">
                            <?= htmlspecialchars(mb_strimwidth($product->description, 0, 80, '…'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold text-danger fs-5"><?= number_format((float)$product->price, 2) ?> &euro;</span>

                            <?php if ($product->stock > 0): ?>
                                <form method="POST" action="<?= BASE_URL ?>carrito/agregar" class="d-flex gap-2 align-items-center m-0">
                                    <input type="hidden" name="producto_id" value="<?= (int)$product->id ?>">
                                    <input type="number" name="cantidad" value="1" min="1" max="<?= (int)$product->stock ?>" class="form-control form-control-sm" style="width:75px;">
                                    <button type="submit" class="btn btn-sm btn-dark">
                                        <i class="bi bi-cart-plus me-1"></i>Añadir
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-danger">Sin stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
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