<?php 
// 1. Cargamos la cabecera (esto trae el menú y el diseño superior)
include_once __DIR__ . '/../layouts/header.php'; 
?>

<section class="hero" style="text-align: center; padding: 40px; background: #e3f2fd;">
    <h1>¡Bienvenido a nuestra Tienda!</h1>
    <p>Explora nuestra nueva colección de productos exclusivos.</p>
    <button style="padding: 10px 20px; cursor: pointer;">Ver Catálogo</button>
</section>

<section class="productos" style="display: flex; gap: 20px; padding: 20px; justify-content: center;">
    <div class="card" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; width: 200px;">
        <div style="background: #eee; height: 150px; margin-bottom: 10px;"></div>
        <h3>Producto A</h3>
        <p>Precio: 25.00€</p>
        <button>Añadir</button>
    </div>

    <div class="card" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; width: 200px;">
        <div style="background: #eee; height: 150px; margin-bottom: 10px;"></div>
        <h3>Producto B</h3>
        <p>Precio: 40.00€</p>
        <button>Añadir</button>
    </div>
</section>

<?php 
// 3. Cargamos el pie de página
include_once __DIR__ . '/../layouts/footer.php'; 
?>