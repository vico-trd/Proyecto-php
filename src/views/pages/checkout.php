<?php
/** @var array  $carrito   ['product_id' => cantidad] */
/** @var array  $productos Lista de objetos producto */
/** @var float  $total     Importe total */
$error = $_SESSION['checkout_error'] ?? null;
unset($_SESSION['checkout_error']);
?>

<div class="container py-5">
    <h2 class="mb-4 fw-bold">Finalizar compra</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Formulario de envío -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Datos de envío</h5>
                    <form action="<?= BASE_URL ?>checkout/confirmar" method="POST">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input type="text" id="nombre" class="form-control"
                                   value="<?= htmlspecialchars($_SESSION['user']['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" id="email" class="form-control"
                                   value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   readonly>
                        </div>

                        <div class="mb-4">
                            <label for="direccion" class="form-label">Dirección de envío <span class="text-danger">*</span></label>
                            <textarea id="direccion" name="direccion" class="form-control" rows="3"
                                      placeholder="Calle, número, piso, ciudad, código postal…" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">
                            Confirmar pedido
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Resumen del pedido -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Resumen del pedido</h5>
                    <ul class="list-group list-group-flush mb-3">
                        <?php foreach ($productos as $producto): ?>
                            <?php $cantidad = (int)($carrito[$producto->id] ?? 0); ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="fw-semibold"><?= htmlspecialchars($producto->name, ENT_QUOTES, 'UTF-8') ?></span>
                                    <small class="d-block text-muted">x <?= $cantidad ?></small>
                                </div>
                                <span><?= number_format($producto->price * $cantidad, 2, ',', '.') ?> €</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-3">
                        <span>Total</span>
                        <span><?= number_format($total, 2, ',', '.') ?> €</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
