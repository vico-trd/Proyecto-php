<?php

/**
 * DatabaseSeeder — punto de entrada principal.
 * Ejecuta todos los seeders en el orden correcto.
 *
 * Uso desde la raíz del proyecto:
 *   php database/seeders/DatabaseSeeder.php
 */

require_once __DIR__ . '/SeederBase.php';
require_once __DIR__ . '/CategorySeeder.php';
require_once __DIR__ . '/UserSeeder.php';
require_once __DIR__ . '/ProductSeeder.php';

echo "\n\033[36m=== Ejecutando seeders ===\033[0m\n\n";

(new CategorySeeder())->run();
echo "\n";

(new UserSeeder())->run();
echo "\n";

(new ProductSeeder())->run();
echo "\n";

echo "\033[36m=== Seeders completados ===\033[0m\n\n";
