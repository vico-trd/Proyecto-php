<?php
require_once __DIR__ . '/SeederBase.php';

/**
 * Seeder de usuarios.
 * Crea un admin y varios usuarios de prueba.
 * No duplica si el email ya existe.
 */
class UserSeeder extends SeederBase
{
    public function run(): void
    {
        $usuarios = [
            [
                'name'     => 'Admin',
                'email'    => 'admin@gmail.com',
                'password' => password_hash('admin1234', PASSWORD_BCRYPT),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Carlos García',
                'email'    => 'carlos@example.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'role'     => 'user',
            ],
            [
                'name'     => 'Laura Martínez',
                'email'    => 'laura@example.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'role'     => 'user',
            ],
            [
                'name'     => 'Miguel Torres',
                'email'    => 'miguel@example.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'role'     => 'user',
            ],
            [
                'name'     => 'Sofía López',
                'email'    => 'sofia@example.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'role'     => 'user',
            ],
            [
                'name'     => 'Javier Ruiz',
                'email'    => 'javier@example.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'role'     => 'user',
            ],
        ];

        $check  = $this->db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $insert = $this->db->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
        );

        foreach ($usuarios as $u) {
            $check->execute(['email' => $u['email']]);
            if ($check->fetch()) {
                $this->warn("Usuario ya existe: {$u['email']}");
                continue;
            }
            $insert->execute($u);
            $this->log("Usuario creado: {$u['email']} [{$u['role']}]");
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    (new UserSeeder())->run();
    echo "\n✅ UserSeeder completado.\n";
}
