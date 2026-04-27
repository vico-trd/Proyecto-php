<div class="container mt-5">
    <h2>Resumen de tu Pedido</h2>
    <hr>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5>Detalles de la compra</h5>
            <p>Estás a punto de confirmar tu pedido. Una vez pulses el botón, se procesará el pago y se descontará el stock de nuestros almacenes.</p>
            
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total a pagar:</span>
                    <strong>
                        <?php 
                        // Aquí podrías calcular el total de nuevo o traerlo del repositorio
                        // Por ahora, mostramos un mensaje genérico o el total si lo pasas desde el controller
                        echo "Se procesará el total acumulado en tu carrito.";
                        ?>
                    </strong>
                </li>
            </ul>

            <form action="checkout/procesar" method="POST">
                <div class="form-group mb-3">
                    <label>Dirección de Envío (Opcional para esta tarea):</label>
                    <input type="text" name="direccion" class="form-control" placeholder="Calle Falsa 123" required>
                </div>
                
                <button type="submit" class="btn btn-success btn-lg btn-block">
                    ✅ Confirmar y Finalizar Compra
                </button>
                <a href="/Proyecto-php/public/index.php?url=carrito" class="btn btn-link">Volver al carrito</a>
            </form>
        </div>
    </div>
</div>
