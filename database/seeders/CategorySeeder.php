<?php
require_once __DIR__ . '/SeederBase.php';

/**
 * Seeder de categorías.
 * Inserta las categorías de ropa si no existen ya (por nombre único).
 */
class CategorySeeder extends SeederBase
{
    public function run(): void
    {
        $categorias = [
            [
                'name'        => 'Camisetas',
                'description' => 'Camisetas de manga corta, básicas y de diseño para hombre y mujer.',
            ],
            [
                'name'        => 'Pantalones',
                'description' => 'Vaqueros, chinos, joggers y pantalones de vestir.',
            ],
            [
                'name'        => 'Vestidos y Faldas',
                'description' => 'Vestidos casuales, de fiesta y faldas para todas las ocasiones.',
            ],
            [
                'name'        => 'Sudaderas y Hoodies',
                'description' => 'Sudaderas con y sin capucha, oversize y ajustadas.',
            ],
            [
                'name'        => 'Chaquetas y Abrigos',
                'description' => 'Chaquetas de cuero, abrigos de lana, parkas y cortavientos.',
            ],
            [
                'name'        => 'Zapatillas',
                'description' => 'Zapatillas deportivas, casuales y de moda urbana.',
            ],
            [
                'name'        => 'Accesorios',
                'description' => 'Gorras, cinturones, bolsos, mochilas y complementos.',
            ],
            [
                'name'        => 'Ropa Interior',
                'description' => 'Camisetas interiores, boxers, calcetines y pijamas.',
            ],
        ];

        $check  = $this->db->prepare('SELECT id FROM categories WHERE name = :name LIMIT 1');
        $insert = $this->db->prepare(
            'INSERT INTO categories (name, description) VALUES (:name, :description)'
        );

        foreach ($categorias as $cat) {
            $check->execute(['name' => $cat['name']]);
            if ($check->fetch()) {
                $this->warn("Categoría ya existe: {$cat['name']}");
                continue;
            }
            $insert->execute($cat);
            $this->log("Categoría creada: {$cat['name']}");
        }
    }
}

// Ejecutar si se llama directamente
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    (new CategorySeeder())->run();
    echo "\n✅ CategorySeeder completado.\n";
}
