<?php include_once __DIR__ . '/../layout/header.php'; ?>

<main class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px;">Tu Carrito</h1>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 1px solid #ddd;">
                    <th style="padding: 15px;">Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr id="fila-producto-1" style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                        <img src="https://via.placeholder.com/60" alt="Producto" style="border-radius: 4px;">
                        <strong>Zapatillas Ultra Boost</strong>
                    </td>
                    <td>1</td>
                    <td>89.99€</td>
                    <td>89.99€</td>
                    <td>
                        <button onclick="eliminarDelCarrito('fila-producto-1')" style="color: #e74c3c; background: none; border: none; cursor: pointer; font-weight: bold;">
                            Eliminar
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: right;">
            <h2 style="margin-bottom: 20px;">Total: <span id="total-carrito" style="color: #e74c3c;">89.99€</span></h2>
            <br>
            <a href="<?= BASE_URL ?>checkout" class="btn btn-dark" style="padding: 15px 30px; text-decoration: none; border-radius: 5px;">
                Realizar Pedido
            </a>
        </div>
    </div>
</main>

<script>
    function eliminarDelCarrito(idFila) {
        if (confirm('¿Seguro que quieres quitar este producto?')) {
            // Buscamos la fila y la borramos visualmente
            const fila = document.getElementById(idFila);
            if (fila) {
                fila.remove();
                
                // Como es maquetado, actualizamos el total a 0 manualmente
                document.getElementById('total-carrito').innerText = '0.00€';
                
                // Opcional: mostrar un mensaje si el carrito queda vacío
                if (document.querySelectorAll('tbody tr').length === 0) {
                    document.querySelector('table').innerHTML = '<p style="padding: 20px;">El carrito está vacío.</p>';
                }
            }
        }
    }
</script>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>