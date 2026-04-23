<?php
require_once __DIR__ . '/SeederBase.php';

/**
 * Seeder de productos.
 * Resuelve los IDs de categoría por nombre antes de insertar,
 * por lo que CategorySeeder debe ejecutarse primero.
 * No duplica si ya existe un producto con el mismo nombre.
 */
class ProductSeeder extends SeederBase
{
    public function run(): void
    {
        // Construimos un mapa nombre_categoria => id
        $stmt = $this->db->query('SELECT id, name FROM categories');
        $catMap = [];
        foreach ($stmt->fetchAll() as $row) {
            $catMap[$row['name']] = (int)$row['id'];
        }

        if (empty($catMap)) {
            echo "\033[31m[ERROR]\033[0m No hay categorías. Ejecuta CategorySeeder primero.\n";
            return;
        }

        $productos = [
            // ── Camisetas ─────────────────────────────────────────────────
            [
                'name'        => 'Camiseta Essential Blanca',
                'category'    => 'Camisetas',
                'description' => 'Camiseta básica de algodón 100%, corte regular, perfecta para el día a día.',
                'price'       => 19.99,
                'stock'       => 80,
                'image'       => '',
            ],
            [
                'name'        => 'Camiseta Oversize Negra',
                'category'    => 'Camisetas',
                'description' => 'Camiseta oversize de corte holgado, tendencia streetwear.',
                'price'       => 24.99,
                'stock'       => 60,
                'image'       => '',
            ],
            [
                'name'        => 'Camiseta Tie-Dye Multicolor',
                'category'    => 'Camisetas',
                'description' => 'Diseño teñido artesanal, edición limitada. Algodón orgánico.',
                'price'       => 29.99,
                'stock'       => 35,
                'image'       => '',
            ],
            [
                'name'        => 'Camiseta Manga Larga Rayas',
                'category'    => 'Camisetas',
                'description' => 'Camiseta de manga larga con rayas horizontales clásicas.',
                'price'       => 22.99,
                'stock'       => 50,
                'image'       => '',
            ],
            [
                'name'        => 'Polo Piqué Navy',
                'category'    => 'Camisetas',
                'description' => 'Polo clásico de piqué con botones, color azul marino.',
                'price'       => 34.99,
                'stock'       => 45,
                'image'       => '',
            ],

            // ── Pantalones ────────────────────────────────────────────────
            [
                'name'        => 'Jeans Slim Fit Azul',
                'category'    => 'Pantalones',
                'description' => 'Vaquero slim fit de denim elástico, tiro medio. Azul clásico.',
                'price'       => 49.99,
                'stock'       => 55,
                'image'       => '',
            ],
            [
                'name'        => 'Jeans Mom Fit Gris',
                'category'    => 'Pantalones',
                'description' => 'Vaquero mom fit de tiro alto con efecto desgastado.',
                'price'       => 54.99,
                'stock'       => 40,
                'image'       => '',
            ],
            [
                'name'        => 'Chino Beige',
                'category'    => 'Pantalones',
                'description' => 'Pantalón chino de corte recto en color beige, tejido ligero.',
                'price'       => 39.99,
                'stock'       => 60,
                'image'       => '',
            ],
            [
                'name'        => 'Jogger Cargo Verde',
                'category'    => 'Pantalones',
                'description' => 'Pantalón jogger con bolsillos cargo laterales. Estilo urbano.',
                'price'       => 44.99,
                'stock'       => 35,
                'image'       => '',
            ],
            [
                'name'        => 'Pantalón Palazzo Negro',
                'category'    => 'Pantalones',
                'description' => 'Pantalón de pierna ancha en tela fluida, negro liso.',
                'price'       => 42.99,
                'stock'       => 30,
                'image'       => '',
            ],

            // ── Vestidos y Faldas ─────────────────────────────────────────
            [
                'name'        => 'Vestido Floral Verano',
                'category'    => 'Vestidos y Faldas',
                'description' => 'Vestido midi con estampado floral, escote en V y tirantes finos.',
                'price'       => 39.99,
                'stock'       => 40,
                'image'       => '',
            ],
            [
                'name'        => 'Vestido Lencero Satinado',
                'category'    => 'Vestidos y Faldas',
                'description' => 'Vestido slip dress de satén color champán, corte al bies.',
                'price'       => 59.99,
                'stock'       => 25,
                'image'       => '',
            ],
            [
                'name'        => 'Falda Mini Cuadros',
                'category'    => 'Vestidos y Faldas',
                'description' => 'Falda mini con estampado de cuadros vichy, cintura alta.',
                'price'       => 27.99,
                'stock'       => 45,
                'image'       => '',
            ],
            [
                'name'        => 'Falda Midi Plisada Azul',
                'category'    => 'Vestidos y Faldas',
                'description' => 'Falda midi plisada de tul en azul cielo, perfecta para ocasiones especiales.',
                'price'       => 49.99,
                'stock'       => 20,
                'image'       => '',
            ],

            // ── Sudaderas y Hoodies ───────────────────────────────────────
            [
                'name'        => 'Sudadera Urban Grey',
                'category'    => 'Sudaderas y Hoodies',
                'description' => 'Sudadera de felpa francesa con cuello redondo. Gris marengo.',
                'price'       => 34.99,
                'stock'       => 70,
                'image'       => '',
            ],
            [
                'name'        => 'Hoodie Oversized Negro',
                'category'    => 'Sudaderas y Hoodies',
                'description' => 'Sudadera con capucha oversize, bolsillo canguro. Algodón premium.',
                'price'       => 44.99,
                'stock'       => 55,
                'image'       => '',
            ],
            [
                'name'        => 'Hoodie Tie-Dye Pastel',
                'category'    => 'Sudaderas y Hoodies',
                'description' => 'Hoodie con efecto tie-dye en tonos pastel. Edición limitada.',
                'price'       => 52.99,
                'stock'       => 25,
                'image'       => '',
            ],
            [
                'name'        => 'Sudadera Crop Morada',
                'category'    => 'Sudaderas y Hoodies',
                'description' => 'Sudadera cropped de felpa con capucha en tono lila.',
                'price'       => 38.99,
                'stock'       => 40,
                'image'       => '',
            ],
            [
                'name'        => 'Zip Hoodie College Verde',
                'category'    => 'Sudaderas y Hoodies',
                'description' => 'Sudadera con cremallera y capucha, estilo universitario americano.',
                'price'       => 49.99,
                'stock'       => 35,
                'image'       => '',
            ],

            // ── Chaquetas y Abrigos ───────────────────────────────────────
            [
                'name'        => 'Chaqueta Vaquera Clásica',
                'category'    => 'Chaquetas y Abrigos',
                'description' => 'Chaqueta de denim azul con botones metálicos. Corte regular.',
                'price'       => 69.99,
                'stock'       => 30,
                'image'       => '',
            ],
            [
                'name'        => 'Cazadora Bomber Negra',
                'category'    => 'Chaquetas y Abrigos',
                'description' => 'Cazadora bomber con cuello acanalado y cremallera central.',
                'price'       => 79.99,
                'stock'       => 25,
                'image'       => '',
            ],
            [
                'name'        => 'Abrigo Lana Camel',
                'category'    => 'Chaquetas y Abrigos',
                'description' => 'Abrigo largo de mezcla de lana en color camel. Corte recto.',
                'price'       => 129.99,
                'stock'       => 15,
                'image'       => '',
            ],
            [
                'name'        => 'Parka Técnica Kaki',
                'category'    => 'Chaquetas y Abrigos',
                'description' => 'Parka impermeable con capucha extraíble y relleno de plumón.',
                'price'       => 99.99,
                'stock'       => 20,
                'image'       => '',
            ],
            [
                'name'        => 'Chaqueta Cuero Sintético',
                'category'    => 'Chaquetas y Abrigos',
                'description' => 'Chaqueta efecto piel negra, forro interior de viscosa.',
                'price'       => 89.99,
                'stock'       => 20,
                'image'       => '',
            ],

            // ── Zapatillas ────────────────────────────────────────────────
            [
                'name'        => 'Zapatillas Ultra Boost Blancas',
                'category'    => 'Zapatillas',
                'description' => 'Zapatilla running con suela de amortiguación boost. Blancas.',
                'price'       => 119.99,
                'stock'       => 30,
                'image'       => '',
            ],
            [
                'name'        => 'Sneaker Low Negras',
                'category'    => 'Zapatillas',
                'description' => 'Zapatilla baja de lona negra con suela de goma vulcanizada.',
                'price'       => 49.99,
                'stock'       => 50,
                'image'       => '',
            ],
            [
                'name'        => 'Chunky Sneaker Beige',
                'category'    => 'Zapatillas',
                'description' => 'Zapatilla de plataforma gruesa con suela exagerada. Tendencia dad-shoe.',
                'price'       => 89.99,
                'stock'       => 25,
                'image'       => '',
            ],
            [
                'name'        => 'Deportivas Mesh Azules',
                'category'    => 'Zapatillas',
                'description' => 'Zapatilla transpirable de malla con sistema de amortiguación en gel.',
                'price'       => 74.99,
                'stock'       => 35,
                'image'       => '',
            ],
            [
                'name'        => 'Slip-On Leopardo',
                'category'    => 'Zapatillas',
                'description' => 'Zapatilla slip-on sin cordones con estampado animal print.',
                'price'       => 44.99,
                'stock'       => 20,
                'image'       => '',
            ],

            // ── Accesorios ────────────────────────────────────────────────
            [
                'name'        => 'Gorra Snapback Negra',
                'category'    => 'Accesorios',
                'description' => 'Gorra de 5 paneles con cierre snapback ajustable.',
                'price'       => 24.99,
                'stock'       => 60,
                'image'       => '',
            ],
            [
                'name'        => 'Mochila Urban 25L',
                'category'    => 'Accesorios',
                'description' => 'Mochila resistente al agua con compartimento para portátil de 15".',
                'price'       => 59.99,
                'stock'       => 30,
                'image'       => '',
            ],
            [
                'name'        => 'Cinturón Cuero Negro',
                'category'    => 'Accesorios',
                'description' => 'Cinturón de cuero genuino con hebilla rectangular plateada.',
                'price'       => 29.99,
                'stock'       => 50,
                'image'       => '',
            ],
            [
                'name'        => 'Bolso Bandolera Marrón',
                'category'    => 'Accesorios',
                'description' => 'Bolso cruzado de cuero sintético con solapa magnética.',
                'price'       => 39.99,
                'stock'       => 25,
                'image'       => '',
            ],
            [
                'name'        => 'Bufanda Cuadros Beis',
                'category'    => 'Accesorios',
                'description' => 'Bufanda grande de lana con estampado de cuadros clásicos.',
                'price'       => 22.99,
                'stock'       => 45,
                'image'       => '',
            ],

            // ── Ropa Interior ─────────────────────────────────────────────
            [
                'name'        => 'Pack 3 Boxers Algodón',
                'category'    => 'Ropa Interior',
                'description' => 'Pack de 3 boxers de algodón stretch. Colores surtidos.',
                'price'       => 19.99,
                'stock'       => 100,
                'image'       => '',
            ],
            [
                'name'        => 'Calcetines Largos x5',
                'category'    => 'Ropa Interior',
                'description' => 'Pack de 5 pares de calcetines de algodón hasta la rodilla.',
                'price'       => 14.99,
                'stock'       => 90,
                'image'       => '',
            ],
            [
                'name'        => 'Pijama Franela Cuadros',
                'category'    => 'Ropa Interior',
                'description' => 'Conjunto de pijama de franela con estampado de cuadros escoceses.',
                'price'       => 34.99,
                'stock'       => 40,
                'image'       => '',
            ],
            [
                'name'        => 'Camiseta Interior Termal',
                'category'    => 'Ropa Interior',
                'description' => 'Camiseta térmica de segunda piel, ideal para invierno.',
                'price'       => 18.99,
                'stock'       => 75,
                'image'       => '',
            ],
        ];

        $check  = $this->db->prepare('SELECT id FROM products WHERE name = :name LIMIT 1');
        $insert = $this->db->prepare(
            'INSERT INTO products (name, category_id, description, price, stock, image)
             VALUES (:name, :category_id, :description, :price, :stock, :image)'
        );

        foreach ($productos as $p) {
            // Resolver categoría
            $categoryId = $catMap[$p['category']] ?? null;
            if (!$categoryId) {
                $this->warn("Categoría no encontrada para: {$p['name']} ('{$p['category']}') — omitido");
                continue;
            }

            // Evitar duplicados
            $check->execute(['name' => $p['name']]);
            if ($check->fetch()) {
                $this->warn("Producto ya existe: {$p['name']}");
                continue;
            }

            $insert->execute([
                'name'        => $p['name'],
                'category_id' => $categoryId,
                'description' => $p['description'],
                'price'       => $p['price'],
                'stock'       => $p['stock'],
                'image'       => $p['image'],
            ]);
            $this->log("Producto creado: {$p['name']} [{$p['category']}] — {$p['price']} €");
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    (new ProductSeeder())->run();
    echo "\n✅ ProductSeeder completado.\n";
}
