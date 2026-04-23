<?php
/** @var App\Models\Category $category */
/** @var App\Models\Product[] $products */
/** @var JasonGrimes\Paginator $paginator */
$pages = $paginator->getPages();
$prevUrl = $paginator->getPrevUrl();
$nextUrl = $paginator->getNextUrl();
?>
<?php include_once __DIR__ . '/../layout/header.php'; ?>

<section style="margin-bottom: 24px;">
    <h1 style="margin-bottom: 8px;">Categoria: <?= htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8') ?></h1>
    <p style="color: #666; margin: 0;">
        <?= htmlspecialchars($category->description ?: 'Productos disponibles en esta categoria.', ENT_QUOTES, 'UTF-8') ?>
    </p>
</section>

<?php if (empty($products)): ?>
    <p>No hay productos disponibles en esta categoria.</p>
<?php else: ?>
    <section style="display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 18px;">
        <?php foreach ($products as $product): ?>
            <article style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: #fff;">
                <?php if (!empty($product->image)): ?>
                    <img
                        src="/Proyecto-php/public/uploads/images/<?= htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>"
                        style="width: 100%; height: 170px; object-fit: cover;"
                    >
                <?php else: ?>
                    <div style="height: 170px; background: #f3f3f3;"></div>
                <?php endif; ?>

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
            </article>
        <?php endforeach; ?>
    </section>

    <?php if ($pages): ?>
        <nav aria-label="Paginacion" style="margin-top: 26px;">
            <ul style="display: flex; gap: 8px; list-style: none; padding: 0; margin: 0; flex-wrap: wrap;">
                <?php if ($prevUrl): ?>
                    <li>
                        <a href="<?= htmlspecialchars($prevUrl, ENT_QUOTES, 'UTF-8') ?>" style="display: inline-block; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; text-decoration: none; color: #222;">Anterior</a>
                    </li>
                <?php endif; ?>

                <?php foreach ($pages as $page): ?>
                    <li>
                        <?php if ($page['url'] === null): ?>
                            <span style="display: inline-block; padding: 8px 12px; color: #555;">...</span>
                        <?php else: ?>
                            <a
                                href="<?= htmlspecialchars($page['url'], ENT_QUOTES, 'UTF-8') ?>"
                                style="display: inline-block; padding: 8px 12px; border-radius: 6px; text-decoration: none; border: 1px solid <?= $page['isCurrent'] ? '#111' : '#ccc' ?>; background: <?= $page['isCurrent'] ? '#111' : '#fff' ?>; color: <?= $page['isCurrent'] ? '#fff' : '#222' ?>;"
                            >
                                <?= htmlspecialchars((string)$page['num'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>

                <?php if ($nextUrl): ?>
                    <li>
                        <a href="<?= htmlspecialchars($nextUrl, ENT_QUOTES, 'UTF-8') ?>" style="display: inline-block; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; text-decoration: none; color: #222;">Siguiente</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>
