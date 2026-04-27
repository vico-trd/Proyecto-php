<div class="container mt-5 text-center" style="min-height: 50vh;">
    <div class="py-5">
        <h1 class="display-4 text-success">¡Gracias por tu compra! ✅</h1>
        <p class="lead">Tu pedido ha sido procesado correctamente.</p>
        <hr class="my-4">
        <p>Número de pedido: <strong>#<?= $_GET['id'] ?? 'N/A' ?></strong></p>
        <p>Recibirás un correo electrónico con los detalles de tu pedido y el seguimiento del envío.</p>
        
        <div class="mt-4">
            <a class="btn btn-primary btn-lg" href="<?= BASE_URL ?>inicio" role="button">Volver a la tienda</a>
            <a class="btn btn-outline-secondary btn-lg" href="<?= BASE_URL ?>mis-pedidos" role="button">Ver mis pedidos</a>
        </div>
    </div>
</div>