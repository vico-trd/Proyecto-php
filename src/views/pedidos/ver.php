<div class="container py-5">

    <div class="mb-4">
        <a href="<?= BASE_URL ?>mis-pedidos" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver a mis pedidos
        </a>
    </div>

    <h2 class="fw-bold mb-4">Detalle del pedido #<?= $pedido->id ?></h2>

    <div class="row g-4">

        <!-- Tabla de productos -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio/ud.</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="ps-3">
                                        <?php if (!empty($item->product_image)): ?>
                                            <img src="/Proyecto-php/public/uploads/images/<?= htmlspecialchars($item->product_image) ?>"
                                                 alt="<?= htmlspecialchars($item->product_name) ?>"
                                                 width="50" height="50"
                                                 class="rounded me-2 object-fit-cover">
                                        <?php endif; ?>
                                        <?= htmlspecialchars($item->product_name) ?>
                                    </td>
                                    <td class="text-center"><?= (int)$item->quantity ?></td>
                                    <td class="text-end"><?= number_format((float)$item->price, 2, ',', '.') ?> €</td>
                                    <td class="text-end pe-3">
                                        <?= number_format((float)$item->price * (int)$item->quantity, 2, ',', '.') ?> €
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end ps-3">Total</td>
                                <td class="text-end pe-3 text-danger fs-5">
                                    <?= number_format($pedido->total, 2, ',', '.') ?> €
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Resumen lateral -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Información del pedido</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Nº pedido</dt>
                        <dd class="col-sm-7">#<?= $pedido->id ?></dd>

                        <dt class="col-sm-5">Fecha</dt>
                        <dd class="col-sm-7">
                            <?= $pedido->created_at
                                ? date('d/m/Y H:i', strtotime($pedido->created_at))
                                : '—' ?>
                        </dd>

                        <dt class="col-sm-5">Estado</dt>
                        <dd class="col-sm-7">
                            <?php
                                $badgeClass = match($pedido->status) {
                                    'confirmado', 'confirmed' => 'success',
                                    'enviado'                 => 'primary',
                                    'entregado'               => 'dark',
                                    'cancelado'               => 'danger',
                                    default                   => 'secondary',
                                };
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <?= htmlspecialchars(ucfirst($pedido->status)) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-5 border-top pt-2 mt-2 fw-bold">Total</dt>
                        <dd class="col-sm-7 border-top pt-2 mt-2 fw-bold text-danger">
                            <?= number_format($pedido->total, 2, ',', '.') ?> €
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

    </div>
</div>
