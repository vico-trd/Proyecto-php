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
        <a href="categoria" class="btn-comprar">Ver Catálogo</a>
    </div>

    <h2 class="seccion-titulo">Compra por Categoría</h2>
    <div class="grid-categorias">
        <a href="#" class="categoria-card" style="background-image: url('https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=600&q=80');">
            Hombre
        </a>
        <a href="#" class="categoria-card" style="background-image: url('https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=600&q=80');">
            Mujer
        </a>
        <a href="#" class="categoria-card" style="background-image: url('https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?auto=format&fit=crop&w=600&q=80');">
            Zapatillas
        </a>
    </div>

    <h2 class="seccion-titulo">Últimas Novedades</h2>
    <div class="grid-productos">
        <div class="producto-card">
            <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=400&q=80" alt="Camiseta Básica">
            <h3>Camiseta Essential Blanca</h3>
            <p class="precio">19.99 €</p>
            <a href="#" class="btn-carrito">Añadir al carrito</a>
        </div>

        <div class="producto-card">
            <img src="https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&w=400&q=80" alt="Pantalón Vaquero">
            <h3>Jeans Slim Fit</h3>
            <p class="precio">39.99 €</p>
            <a href="#" class="btn-carrito">Añadir al carrito</a>
        </div>

        <div class="producto-card">
            <img src="https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=400&q=80" alt="Sudadera">
            <h3>Sudadera Urban Grey</h3>
            <p class="precio">29.99 €</p>
            <a href="#" class="btn-carrito">Añadir al carrito</a>
        </div>

        <div class="producto-card">
            <img src="https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=400&q=80" alt="Vestido">
            <h3>Vestido Floral Verano</h3>
            <p class="precio">34.99 €</p>
            <a href="#" class="btn-carrito">Añadir al carrito</a>
        </div>
    </div>
</div>