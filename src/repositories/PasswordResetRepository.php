<?php

namespace App\Repositories;

use PDO;
use App\Core\Database;

/**
 * No implementa RepositoryInterface porque esa interfaz define CRUD por ID
 * entero (findById, save, delete), semántica incompatible con tokens de un
 * solo uso identificados por string. Esta clase sigue el principio de
 * Segregación de Interfaces (ISP-SOLID): es mejor una interfaz específica
 * que forzar implementaciones sin sentido.
 */


//ES LA UNICA QUE NO IMPLEMENTA INTERFACE PORQUE TRABAJA CON TOKENS DE TEXTO, NO CON ID NUMERICOS
class PasswordResetRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }


    //crear nuevo token
    public function create(string $email, string $token, \DateTimeImmutable $expiresAt): void
    {
        // Eliminar tokens anteriores del mismo email
        $this->deleteByEmail($email);

        $stmt = $this->db->prepare(
            'INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)'
        );
        $stmt->execute([
            'email'      => $email,
            'token'      => $token,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }


    // esto lo q hace es ver si ha caducado el token
    public function findValidToken(string $token): ?array
    {
        // el expires_at es el que marca el tiempo que tienes para crear ese nuevo token
        $stmt = $this->db->prepare(
            'SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1'
        );
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    //cuando el usuario cambia su contraseña el token se consume y se borra
    public function deleteByToken(string $token): void
    {
        $stmt = $this->db->prepare('DELETE FROM password_resets WHERE token = :token');
        $stmt->execute(['token' => $token]);
    }

    public function deleteByEmail(string $email): void
    {
        $stmt = $this->db->prepare('DELETE FROM password_resets WHERE email = :email');
        $stmt->execute(['email' => $email]);
    }
}
