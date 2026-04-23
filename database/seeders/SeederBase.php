<?php

/**
 * Clase base para todos los seeders.
 * Gestiona la conexión PDO leyendo las credenciales desde el .env del proyecto.
 */
abstract class SeederBase
{
    protected PDO $db;

    public function __construct()
    {
        $this->loadEnv();

        $host    = $_ENV['DB_HOST']    ?? '127.0.0.1';
        $port    = $_ENV['DB_PORT']    ?? '3306';
        $dbName  = $_ENV['DB_NAME']    ?? 'ecommerce_db';
        $user    = $_ENV['DB_USER']    ?? 'root';
        $pass    = $_ENV['DB_PASS']    ?? '';

        try {
            $this->db = new PDO(
                "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            echo "\033[31m[ERROR DB]\033[0m " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    abstract public function run(): void;

    protected function log(string $msg): void
    {
        echo "\033[32m[OK]\033[0m " . $msg . "\n";
    }

    protected function warn(string $msg): void
    {
        echo "\033[33m[SKIP]\033[0m " . $msg . "\n";
    }

    private function loadEnv(): void
    {
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
}
