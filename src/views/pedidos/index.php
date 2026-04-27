<?php
/** @var App\Models\Order[] $pedidos */

$etiquetas = [
    'pending'    => ['texto' => 'Pendiente',   'clase' => 'secondary'],
    'confirmado' => ['texto' => 'Confirmado',  'clase' => 'success'],
    'confirmed'  => ['texto' => 'Confirmado',  'clase' => 'success'],
    'enviado'    => ['texto' => 'Enviado',      'clase' => 'primary'],
    'entregado'  => ['texto' => 'Entregado',    'clase' => 'dark'],
    'cancelado'  => ['texto' => 'Cancelado',    'clase' => 'danger'],
];
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Mis pedidos</h2>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info">
            Todavía no has realizado ningún pedido.
            <a href="<?= BASE_URL ?>" class="alert-link">¡Empieza a comprar!</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nº pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <?php
                            $info   = $etiquetas[$pedido->status] ?? ['texto' => ucfirst($pedido->status), 'clase' => 'secondary'];
                        ?>
                        <tr>
                            <td><strong>#<?= $pedido->id ?></strong></td>
                            <td><?= htmlspecialchars($pedido->created_at ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= number_format($pedido->total, 2, ',', '.') ?> €</td>
                            <td>
                                <span class="badge bg-<?= $info['clase'] ?>">
                                    <?= $info['texto'] ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>mis-pedidos/ver?id=<?= $pedido->id ?>"
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
