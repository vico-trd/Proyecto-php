<?php
/** @var array  $carrito   ['product_id' => cantidad] */
/** @var array  $productos Lista de objetos producto */
/** @var float  $total     Importe total */
/** @var string|null $error Mensaje de error */
?>

<div class="container py-5">
    <h2 class="mb-4 fw-bold">Finalizar compra</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Formulario de datos de envío -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Datos de envío</h5>
                    <form action="<?= BASE_URL ?>checkout/procesar" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" class="form-control"
                                   value="<?= htmlspecialchars($_SESSION['user']['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control"
                                   value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   readonly>
                        </div>

                        <div class="mb-4">
                            <label for="direccion" class="form-label">
                                Dirección de envío <span class="text-danger">*</span>
                            </label>
                            <textarea id="direccion" name="direccion" class="form-control" rows="3"
                                      placeholder="Calle, número, piso, ciudad, código postal…" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">
                            ✅ Confirmar y finalizar compra
                        </button>
                        <a href="<?= BASE_URL ?>carrito" class="btn btn-link w-100 text-center mt-2">
                            ← Volver al carrito
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Resumen detallado del pedido -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Resumen del pedido</h5>

                    <table class="table table-sm mb-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto): ?>
                                <?php $cantidad = (int)($carrito[$producto->id] ?? 0); ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($producto->name, ENT_QUOTES, 'UTF-8') ?>
                                        <small class="d-block text-muted">
                                            <?= number_format($producto->price, 2, ',', '.') ?> € / ud.
                                        </small>
                                    </td>
                                    <td class="text-center"><?= $cantidad ?></td>
                                    <td class="text-end">
                                        <?= number_format($producto->price * $cantidad, 2, ',', '.') ?> €
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="2">Total</td>
                                <td class="text-end text-danger fs-5">
                                    <?= number_format($total, 2, ',', '.') ?> €
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
