CREATE DATABASE IF NOT EXISTS ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user'
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(128) NULL DEFAULT NULL,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email)
);

INSERT INTO users (name, email, password, role)
VALUES ('admin', 'admin@gmail.com', '$2y$10$P9d4f5vmBqjDepCbDxeXWevFrmVIxWhTtAHrxNUVMdznmhrIZk3zG', 'admin');


INSERT INTO products (name, category_id, description, price, stock, image) VALUES
('Camiseta Básica Blanca', 2, 'Camiseta de algodón 100%, corte regular.', 19.99, 50, ''),
('Camiseta Básica Negra', 2, 'Camiseta de algodón 100%, corte regular.', 19.99, 50, ''),
('Camiseta Rayas Marineras', 2, 'Estilo náutico, tela suave.', 24.99, 30, ''),
('Camiseta Oversize Gris', 2, 'Corte oversized, muy cómoda.', 22.99, 40, ''),
('Camiseta Estampada Floral', 2, 'Print floral exclusivo.', 27.99, 25, ''),
('Camiseta Logo Vintage', 2, 'Diseño retro con logo bordado.', 29.99, 20, ''),
('Camiseta Tie-Dye', 2, 'Teñido manual, única.', 34.99, 15, ''),
('Camiseta Polo Azul', 2, 'Polo clásico de piqué.', 39.99, 35, ''),
('Camiseta Deportiva Dry-Fit', 2, 'Tejido transpirable para deporte.', 32.99, 45, ''),
('Camiseta Cuello V Roja', 2, 'Cuello en V, fit slim.', 21.99, 60, ''),
('Camiseta Lino Verano', 2, 'Tela de lino fresca y ligera.', 36.99, 20, ''),
('Camiseta Manga Larga Azul', 2, 'Perfecta para entretiempo.', 28.99, 30, ''),
('Camiseta Henley Beige', 2, 'Con botones en el cuello.', 31.99, 25, ''),
('Camiseta Hoodie Gris', 2, 'Con capucha, tejido grueso.', 49.99, 18, ''),
('Camiseta Tie Knot Rosa', 2, 'Nudo frontal, tendencia actual.', 26.99, 22, ''),
('Camiseta Crop Top Blanca', 2, 'Corte cropped, estilo moderno.', 23.99, 40, ''),
('Camiseta Cuadros Escoceses', 2, 'Patrón clásico tartan.', 33.99, 15, ''),
('Camiseta Estampado Geométrico', 2, 'Diseño geométrico minimalista.', 29.99, 28, ''),
('Camiseta Terciopelo Verde', 2, 'Tejido de terciopelo suave.', 44.99, 10, ''),
('Camiseta Denim Look', 2, 'Efecto tela vaquera sin serlo.', 37.99, 20, ''),
('Camiseta Rayas Verticales', 2, 'Estiliza la figura.', 25.99, 35, ''),
('Camiseta Animal Print', 2, 'Estampado leopardo.', 31.99, 18, ''),
('Camiseta Bordada Flor', 2, 'Bordado artesanal en el pecho.', 42.99, 12, ''),
('Camiseta Neon Amarilla', 2, 'Color fluorescente, muy visible.', 20.99, 50, ''),
('Camiseta Premium Merino', 2, 'Lana merino, calidad superior.', 59.99, 8, '');