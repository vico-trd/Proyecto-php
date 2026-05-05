<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{   
    //pertenece a la clase e inicia con null porque al principio no hay ninguna conexion
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        // Si $_ENV no está cargado, estos valores por defecto funcionarán en XAMPP
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $db   = $_ENV['DB_NAME'] ?? 'ecommerce_db';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? ''; // XAMPP por defecto no tiene contraseña
        $charset = 'utf8mb4';

        //string de conexion para saber a q base de datos conectarse
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            //para cada consulta hace un array asociativo
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            //usa prepares reales no emulados por php, mas seguro q sql
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // Intentamos la conexión
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Si falla, lanzamos un mensaje más descriptivo para debuggear
            throw new PDOException("Error de Conexión: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Devuelve la única instancia de la clase (Singleton).
     * Si no existe, la crea conectando a la base de datos.
     *
     * @return self
     */
    public static function getInstance(): Database
    {
        //crea un objeto database nuevo y lo devuelve
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Devuelve la conexión PDO activa.
     *
     * @return PDO
     */

    //para sacar el PDO
    public function getConnection(): PDO
    {
        return $this->connection;
    }
    //eso hace que nadie pueda romper el singleton, clonar la base
    private function __clone() {}
}