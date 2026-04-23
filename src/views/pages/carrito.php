<?php include_once __DIR__ . '/../layout/header.php'; ?>

<h1 class="h3 mb-4 pb-3 border-bottom">
    <i class="bi bi-cart3 me-2"></i>Tu Carrito
</h1>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="fila-producto-1">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="https://via.placeholder.com/60" alt="Producto"
                                     class="rounded" style="width:60px;height:60px;object-fit:cover;">
                                <strong>Zapatillas Ultra Boost</strong>
                            </div>
                        </td>
                        <td>1</td>
                        <td>89.99&euro;</td>
                        <td class="fw-semibold text-danger">89.99&euro;</td>
                        <td>
                            <button onclick="eliminarDelCarrito('fila-producto-1')"
                                    class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-end">
    <div class="card shadow-sm border-0 p-4" style="min-width:280px;">
        <div class="d-flex justify-content-between mb-3 fs-5">
            <span>Total:</span>
            <span class="fw-bold text-danger" id="total-carrito">89.99&euro;</span>
        </div>
        <a href="<?= BASE_URL ?>checkout" class="btn btn-dark w-100">
            <i class="bi bi-bag-check me-2"></i>Realizar Pedido
        </a>
    </div>
</div>

<script>
    function eliminarDelCarrito(idFila) {
        if (confirm('¿Seguro que quieres quitar este producto?')) {
            const fila = document.getElementById(idFila);
            if (fila) {
                fila.remove();
                document.getElementById('total-carrito').innerText = '0.00€';
                if (document.querySelectorAll('tbody tr').length === 0) {
                    document.querySelector('.table-responsive').innerHTML =
                        '<p class="text-muted p-4">El carrito está vacío.</p>';
                }
            }
        }
    }
</script>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>