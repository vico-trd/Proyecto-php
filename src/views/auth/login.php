<?php 
// 1. Incluimos el cabecero (ajusta la ruta si es necesario)
include_once __DIR__ . '/../layout/header.php';
?>

<main class="container">
    <section class="login-form" style="padding: 50px 0; max-width: 400px; margin: 0 auto;">
        <h2>Iniciar Sesión</h2>
        <form action="#" method="POST">
            <div style="margin-bottom: 15px;">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" style="width: 100%; padding: 8px;">
            </div>
            <button type="submit" class="btn-carrito" style="cursor: pointer; width: 100%;">Entrar</button>
        </form>
        <p style="margin-top: 15px;">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
    </section>
</main>

<?php