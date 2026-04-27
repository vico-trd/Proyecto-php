<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Mis pedidos</h2>
        <a href="<?= BASE_URL ?>" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Seguir comprando
        </a>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bag-x display-4 d-block mb-3"></i>
            <p class="fs-5">Todavía no tienes ningún pedido realizado.</p>
            <a href="<?= BASE_URL ?>" class="btn btn-dark mt-2">Ir a la tienda</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <?php
                            $badgeClass = match($pedido->status) {
                                'confirmado', 'confirmed' => 'success',
                                'enviado'                 => 'primary',
                                'entregado'               => 'dark',
                                'cancelado'               => 'danger',
                                default                   => 'secondary',
                            };
                        ?>
                        <tr>
                            <td class="fw-semibold">#<?= $pedido->id ?></td>
                            <td>
                                <?= $pedido->created_at
                                    ? date('d/m/Y H:i', strtotime($pedido->created_at))
                                    : '—' ?>
                            </td>
                            <td><?= number_format($pedido->total, 2, ',', '.') ?> €</td>
                            <td>
                                <span class="badge bg-<?= $badgeClass ?>">
                                    <?= htmlspecialchars(ucfirst($pedido->status)) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>mis-pedidos/ver&id=<?= $pedido->id ?>"
                                   class="btn btn-sm btn-outline-dark">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
