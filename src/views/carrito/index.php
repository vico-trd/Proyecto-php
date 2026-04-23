<?php
/**
 * @var array<int, int>         $carrito       Mapa producto_id => cantidad
 * @var App\Models\Product[]    $productos     Objetos producto con detalles
 * @var int                     $totalArticulos Suma de todas las unidades
 * @var float                   $importeTotal   Suma de precio * cantidad
 */

$error = $_SESSION['carrito_error'] ?? null;
unset($_SESSION['carrito_error']);

// Indexamos los productos por id para acceso rápido en la tabla
$productosIndexados = [];
foreach ($productos as $p) {
    $productosIndexados[$p->id] = $p;
}
?>

<h1 class="mb-4">Tu Carrito</h1>

<?php if ($error): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>

<?php if (empty($carrito)): ?>
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
        <h3 class="mt-3 text-muted">Tu carrito está vacío</h3>
        <a href="<?= BASE_URL ?>" class="btn btn-dark mt-3">Seguir comprando</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <!-- Tabla de productos -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">Producto</th>
                                <th class="text-center">Precio</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Subtotal</th>
                                <th class="text-center">Quitar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carrito as $productoId => $cantidad):
                                $productoId = (int)$productoId;
                                $p = $productosIndexados[$productoId] ?? null;
                                if (!$p) continue;
                                $subtotal = $p->price * $cantidad;
                            ?>
                            <tr>
                                <!-- Imagen + nombre -->
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (!empty($p->image)): ?>
                                            <img
                                                src="/Proyecto-php/public/uploads/images/<?= htmlspecialchars($p->image, ENT_QUOTES, 'UTF-8') ?>"
                                                alt="<?= htmlspecialchars($p->name, ENT_QUOTES, 'UTF-8') ?>"
                                                style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                                        <?php else: ?>
                                            <div style="width:60px;height:60px;background:#eee;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="fw-semibold"><?= htmlspecialchars($p->name, ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </td>

                                <!-- Precio unitario -->
                                <td class="text-center"><?= number_format($p->price, 2) ?> €</td>

                                <!-- Controles de cantidad -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <!-- Decrementar -->
                                        <form method="POST" action="<?= BASE_URL ?>carrito/decrementar">
                                            <input type="hidden" name="producto_id" value="<?= $productoId ?>">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm px-2 py-0" title="Reducir cantidad">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                        </form>

                                        <span class="px-2 fw-bold"><?= (int)$cantidad ?></span>

                                        <!-- Incrementar -->
                                        <form method="POST" action="<?= BASE_URL ?>carrito/incrementar">
                                            <input type="hidden" name="producto_id" value="<?= $productoId ?>">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm px-2 py-0" title="Aumentar cantidad"
                                                <?= ((int)$cantidad >= $p->stock) ? 'disabled title="Stock máximo alcanzado"' : '' ?>>
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <?php if ((int)$cantidad >= $p->stock): ?>
                                        <small class="text-danger d-block mt-1">Stock máx.</small>
                                    <?php endif; ?>
                                </td>

                                <!-- Subtotal -->
                                <td class="text-center fw-semibold"><?= number_format($subtotal, 2) ?> €</td>

                                <!-- Eliminar -->
                                <td class="text-center">
                                    <form method="POST" action="<?= BASE_URL ?>carrito/eliminar"
                                          onsubmit="return confirm('¿Quitar este producto del carrito?');">
                                        <input type="hidden" name="producto_id" value="<?= $productoId ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar del carrito">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Vaciar carrito -->
            <div class="mt-2 text-end">
                <form method="POST" action="<?= BASE_URL ?>carrito/vaciar"
                      onsubmit="return confirm('¿Vaciar todo el carrito?');">
                    <button type="submit" class="btn btn-link text-danger text-decoration-none p-0">
                        <i class="bi bi-x-circle me-1"></i>Vaciar carrito
                    </button>
                </form>
            </div>
        </div>

        <!-- Resumen del pedido -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">
                    Resumen del pedido
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-7">Total artículos:</dt>
                        <dd class="col-5 text-end"><?= (int)$totalArticulos ?></dd>

                        <dt class="col-7 fs-5">Importe total:</dt>
                        <dd class="col-5 text-end fs-5 fw-bold text-danger"><?= number_format($importeTotal, 2) ?> €</dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="<?= BASE_URL ?>checkout" class="btn btn-dark w-100">
                            <i class="bi bi-credit-card me-2"></i>Finalizar Pedido
                        </a>
                    <?php else: ?>
                        <p class="text-muted small mb-2">Debes identificarte para finalizar tu compra.</p>
                        <a href="<?= BASE_URL ?>login" class="btn btn-dark w-100 mb-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                        </a>
                        <a href="<?= BASE_URL ?>register" class="btn btn-outline-dark w-100">
                            <i class="bi bi-person-plus me-2"></i>Crear Cuenta
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary w-100 mt-2">
                <i class="bi bi-arrow-left me-1"></i>Seguir comprando
            </a>
        </div>
    </div>
<?php endif; ?>
