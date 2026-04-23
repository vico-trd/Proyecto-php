<style>
    /* Estilos exclusivos para la portada */
    .home-wrapper {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }
    
    /* Sección Hero (El banner principal) */
    .hero-banner {
        background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?auto=format&fit=crop&w=1200&q=80');
        background-size: cover;
        background-position: center;
        height: 60vh;
        min-height: 400px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
        border-radius: 12px;
        margin-bottom: 50px;
        padding: 20px;
    }

    .hero-banner h1 {
        font-size: 3.5rem;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .hero-banner p {
        font-size: 1.2rem;
        margin-bottom: 25px;
    }

    /* Botón principal */
    .btn-comprar {
        background-color: #2c3e50;
        color: white;
        padding: 12px 30px;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.1rem;
        border-radius: 30px;
        transition: background-color 0.3s, transform 0.2s;
    }

    .btn-comprar:hover {
        background-color: #e74c3c;
        transform: scale(1.05);
        color: white;
    }

    /* Títulos de sección */
    .seccion-titulo {
        text-align: center;
        margin-bottom: 30px;
        font-size: 2rem;
        position: relative;
    }

    /* Grid de Categorías */
    .grid-categorias {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 60px;
    }

    .categoria-card {
        height: 250px;
        background-size: cover;
        background-position: center;
        border-radius: 10px;
        display: flex;
        align-items: flex-end;
        padding: 20px;
        text-decoration: none;
        color: white;
        font-size: 1.5rem;
        font-weight: bold;
        box-shadow: inset 0 -60px 50px -10px rgba(0,0,0,0.7);
        transition: transform 0.3s;
    }

    .categoria-card:hover {
        transform: translateY(-10px);
    }

    /* Grid de Productos */
    .grid-productos {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 25px;
        margin-bottom: 50px;
    }

    .producto-card {
        background: white;
        border: 1px solid #eaeaea;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        transition: box-shadow 0.3s;
    }

    .producto-card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .producto-card img {
        width: 100%;
        height: 280px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .producto-card h3 {
        font-size: 1.1rem;
        margin: 10px 0;
        color: #333;
    }

    .precio {
        color: #e74c3c;
        font-weight: bold;
        font-size: 1.3rem;
        margin-bottom: 15px;
    }

    .btn-carrito {
        display: block;
        background-color: #f8f9fa;
        color: #333;
        border: 1px solid #ddd;
        padding: 10px;
        text-decoration: none;
        font-weight: bold;
        border-radius: 5px;
        transition: 0.3s;
    }

    .btn-carrito:hover {
        background-color: #2c3e50;
        color: white;
    }
</style>

<div class="home-wrapper">
    <div class="hero-banner">
        <h1>Colección Primavera 2026</h1>
        <p>Estilo, comodidad y tendencia en cada prenda.</p>
        <a href="<?= BASE_URL ?>categorias" class="btn-comprar">Ver Catálogo</a>
    </div>

    <h2 class="seccion-titulo">Compra por Categoría</h2>
    <?php if (!empty($categorias)): ?>
    <div class="grid-categorias">
        <?php
        $bgImages = [
            'https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&w=600&q=80',
        ];
        ?>
        <?php foreach ($categorias as $i => $cat): ?>
            <a href="<?= BASE_URL ?>categoria/<?= (int)$cat->id ?>/productos" class="categoria-card"
               style="background-image: url('<?= $bgImages[$i % count($bgImages)] ?>')">
                <?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center text-muted mb-5">Próximamente nuevas categorías.</p>
    <?php endif; ?>
    <h2 class="seccion-titulo">Últimas Novedades</h2>
    <?php if (!empty($productos)): ?>
    <div class="grid-productos">
        <?php foreach ($productos as $producto): ?>
        <div class="producto-card">
            <?php if (!empty($producto->image)): ?>
                <img src="/Proyecto-php/public/uploads/images/<?= htmlspecialchars($producto->image, ENT_QUOTES, 'UTF-8') ?>"
                     alt="<?= htmlspecialchars($producto->name, ENT_QUOTES, 'UTF-8') ?>">
            <?php else: ?>
                <div style="width:100%;height:280px;background:#f0f0f0;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:15px;">
                    <span style="color:#aaa;font-size:2rem;">&#128247;</span>
                </div>
            <?php endif; ?>
            <h3><?= htmlspecialchars($producto->name, ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="precio"><?= number_format((float)$producto->price, 2) ?> &euro;</p>
            <a href="<?= BASE_URL ?>producto" class="btn-carrito">Añadir al carrito</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center text-muted mb-5">No hay productos disponibles todavía.</p>
    <?php endif; ?>
</div>