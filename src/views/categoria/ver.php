<?php 
// 1. Incluimos el cabecero desde la carpeta layout
include_once __DIR__ . '/../layout/header.php'; 
?>

<main class="container" style="padding: 20px;">
    <header class="category-header" style="margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
        <h1>Categoría: <span style="color: #e74c3c;">Nombre de la Categoría</span></h1>
        <p>Explora nuestra selección de productos destacados en esta sección.</p>
    </header>

    <section class="productos-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px;">
        
        <article class="producto-card">
            <img src="https://via.placeholder.com/280x200" alt="Producto 1">
            <h3>Producto Destacado A</h3>
            <p class="precio">29.99€</p>
            <a href="/Proyecto-php/producto" class="btn-carrito" style="display: block; text-align: center; text-decoration: none;">Ver Detalles</a>
        </article>

        <article class="producto-card">
            <img src="https://via.placeholder.com/280x200" alt="Producto 2">
            <h3>Producto Destacado B</h3>
            <p class="precio">45.50€</p>
            <a href="/Proyecto-php/producto" class="btn-carrito" style="display: block; text-align: center; text-decoration: none;">Ver Detalles</a>
        </article>

        <article class="producto-card">
            <img src="https://via.placeholder.com/280x200" alt="Producto 3">
            <h3>Producto Destacado C</h3>
            <p class="precio">19.95€</p>
            <a href="/Proyecto-php/producto" class="btn-carrito" style="display: block; text-align: center; text-decoration: none;">Ver Detalles</a>
        </article>

        </section>
</main>

<?php 
// 2. Incluimos el pie de página
include_once __DIR__ . '/../layout/footer.php'; 
?>