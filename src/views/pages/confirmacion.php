<?php
/** @var array|null $pedido Datos del pedido confirmado */
?>

<div class="container py-5">
    <?php if ($pedido): ?>
        <div class="text-center mb-5">
            <div class="display-1 text-success mb-3">✓</div>
            <h2 class="fw-bold">¡Pedido confirmado!</h2>
            <p class="text-muted">
                Hemos enviado un correo de confirmación a
                <strong><?= htmlspecialchars($pedido['email'], ENT_QUOTES, 'UTF-8') ?></strong>.
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">Detalles del pedido</h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Nº de pedido</dt>
                            <dd class="col-sm-7">#<?= (int)$pedido['order_id'] ?></dd>

                            <dt class="col-sm-5">Fecha</dt>
                            <dd class="col-sm-7"><?= htmlspecialchars($pedido['fecha'], ENT_QUOTES, 'UTF-8') ?></dd>

                            <dt class="col-sm-5">Cliente</dt>
                            <dd class="col-sm-7"><?= htmlspecialchars($pedido['nombre'], ENT_QUOTES, 'UTF-8') ?></dd>

                            <dt class="col-sm-5">Dirección de envío</dt>
                            <dd class="col-sm-7"><?= nl2br(htmlspecialchars($pedido['direccion'], ENT_QUOTES, 'UTF-8')) ?></dd>

                            <dt class="col-sm-5 border-top pt-2 mt-1 fw-bold">Total</dt>
                            <dd class="col-sm-7 border-top pt-2 mt-1 fw-bold text-danger fs-5">
                                <?= number_format((float)$pedido['total'], 2, ',', '.') ?> €
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= BASE_URL ?>" class="btn btn-dark px-5">Seguir comprando</a>
                </div>
            </div>
        </div>

    <?php else: ?>

        <div class="text-center py-5">
            <p class="text-muted">No hay ningún pedido reciente para mostrar.</p>
            <a href="<?= BASE_URL ?>" class="btn btn-dark">Ir a la tienda</a>
        </div>

    <?php endif; ?>
</div>
