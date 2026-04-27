<div class="container mt-5 mb-5">
    <h2 class="mb-4">Finalizar compra</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Columna izquierda: datos del comprador y dirección -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Datos del comprador</h5>

                    <p class="mb-4">
                        Comprando como
                        <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong>
                        (<?= htmlspecialchars($_SESSION['user']['email']) ?>)
                    </p>

                    <form action="<?= BASE_URL ?>checkout/procesar" method="POST">
                        <div class="mb-3">
                            <label for="direccion" class="form-label fw-semibold">Dirección de envío</label>
                            <textarea
                                id="direccion"
                                name="direccion"
                                class="form-control"
                                rows="3"
                                placeholder="Calle, número, ciudad, código postal..."
                                required
                            ></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-bag-check-fill me-1"></i> Confirmar pedido
                            </button>
                            <a href="<?= BASE_URL ?>carrito" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Volver al carrito
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna derecha: resumen del pedido -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Resumen del pedido</h5>

                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">Precio/ud.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carrito as $id => $cantidad): ?>
                                <?php $producto = $productos[$id] ?? null; ?>
                                <?php if ($producto): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($producto->name) ?></td>
                                        <td class="text-center"><?= $cantidad ?></td>
                                        <td class="text-end"><?= number_format($producto->price, 2) ?> €</td>
                                        <td class="text-end"><?= number_format($producto->price * $cantidad, 2) ?> €</td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-end"><?= number_format($total, 2) ?> €</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>