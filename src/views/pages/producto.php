<?php include_once __DIR__ . '/../layout/header.php'; ?>

<main class="container" style="padding: 50px 20px;">
    <div style="display: flex; gap: 40px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <img src="https://via.placeholder.com/500" alt="Producto" style="width: 100%; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        </div>

        <div style="flex: 1; min-width: 300px;">
            <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Zapatillas Ultra Boost 2026</h1>
            <p style="color: #e74c3c; font-size: 2rem; font-weight: bold; margin-bottom: 20px;">89.99€</p>
            <p style="line-height: 1.6; color: #555; margin-bottom: 30px;">
                Diseñadas para ofrecer la máxima comodidad y estilo. Perfectas tanto para el deporte como para tu día a día. Edición limitada de la nueva colección Primavera.
            </p>
            
            <a href="/Proyecto-php/carrito" class="btn-carrito" style="text-decoration: none; display: inline-block; text-align: center;">
    Añadir al Carrito
</a>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>