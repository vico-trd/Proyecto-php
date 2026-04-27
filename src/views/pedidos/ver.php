<?php
/** @var App\Models\Order $pedido */
/** @var object[]          $items  Cada item tiene: product_name, quantity, price, product_image */

$etiquetas = [
    'pending'    => ['texto' => 'Pendiente',  'clase' => 'secondary'],
    'confirmado' => ['texto' => 'Confirmado', 'clase' => 'success'],
    'confirmed'  => ['texto' => 'Confirmado', 'clase' => 'success'],
    'enviado'    => ['texto' => 'Enviado',     'clase' => 'primary'],
    'entregado'  => ['texto' => 'Entregado',   'clase' => 'dark'],
    'cancelado'  => ['texto' => 'Cancelado',   'clase' => 'danger'],
];

$info = $etiquetas[$pedido->status] ?? ['texto' => ucfirst($pedido->status), 'clase' => 'secondary'];
?>

<div class="container py-5">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= BASE_URL ?>mis-pedidos" class="btn btn-sm btn-outline-secondary">← Mis pedidos</a>
        <h2 class="fw-bold mb-0">Pedido #<?= $pedido->id ?></h2>
        <span class="badge bg-<?= $info['clase'] ?> fs-6"><?= $info['texto'] ?></span>
    </div>

    <div class="row g-4">

        <!-- Líneas del pedido -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio unit.</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="ps-4">
                                        <?php if ($item->product_image): ?>
                                            <img src="<?= BASE_URL ?>uploads/images/<?= htmlspecialchars($item->product_image, ENT_QUOTES, 'UTF-8') ?>"
                                                 alt="" width="48" height="48"
                                                 class="rounded me-2 object-fit-cover">
                                        <?php endif; ?>
                                        <?= htmlspecialchars($item->product_name, ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="text-center"><?= (int)$item->quantity ?></td>
                                    <td class="text-end"><?= number_format($item->price, 2, ',', '.') ?> €</td>
                                    <td class="text-end pe-4">
                                        <?= number_format($item->price * $item->quantity, 2, ',', '.') ?> €
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold table-light">
                                <td colspan="3" class="ps-4">Total</td>
                                <td class="text-end pe-4 text-danger fs-5">
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
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Información del pedido</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Nº pedido</dt>
                        <dd class="col-sm-7">#<?= $pedido->id ?></dd>

                        <dt class="col-sm-5">Fecha</dt>
                        <dd class="col-sm-7">
                            <?php
                                $fecha = $pedido->created_at
                                    ? date('d/m/Y H:i', strtotime($pedido->created_at))
                                    : '—';
                                echo htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8');
                            ?>
                        </dd>

                        <dt class="col-sm-5">Estado</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-<?= $info['clase'] ?>"><?= $info['texto'] ?></span>
                        </dd>

                        <dt class="col-sm-5 border-top pt-2 mt-1 fw-bold">Total</dt>
                        <dd class="col-sm-7 border-top pt-2 mt-1 fw-bold text-danger">
                            <?= number_format($pedido->total, 2, ',', '.') ?> €
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

    </div>
</div>
