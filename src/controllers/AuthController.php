<?php

namespace App\Controllers;

use App\Request\UserRequest;
use App\Request\LoginRequest;
use App\Repositories\UserRepository;
use App\Models\User;

class AuthController extends BaseController

//HE COMENTADO USERREPOSITORY PRA QUE FUNCIONE EN LA PAGINA, Y VER EL MAQUETADO
{
 //   private UserRepository $userRepository;

    public function __construct()
    {
       // $this->userRepository = new UserRepository();
    }

    public function showRegister(): void
    {
        $this->render('auth/register');
    }

    public function register(): void
    {
        $request = new UserRequest($_POST);

        if (!$request->validateRegister()) {
            $this->render('auth/register', [
                'errors' => $request->getErrors(),
                'old' => $request->all(),
            ]);
            return;
        }

        $existingUser = $this->userRepository->findByEmail($request->get('email'));
        if ($existingUser) {
            $this->render('auth/register', [
                'errors' => ['email' => 'Este email ya está registrado.'],
                'old' => $request->all(),
            ]);
            return;
        }

        $user = new User(
            id: 0,
            name: $request->get('name'),
            email: $request->get('email'),
            password: password_hash($request->get('password'), PASSWORD_BCRYPT),
            role: 'user'
        );

        $this->userRepository->save($user);
        $this->redirect('login');
    }

    public function showLogin(): void
    {
        $this->render('auth/login');
    }

    public function login(): void
    {
        $request = new LoginRequest($_POST);

        if (!$request->validate()) {
            $this->render('auth/login', [
                'errors' => $request->getErrors(),
                'old' => $request->all(),
            ]);
            return;
        }

        $user = $this->userRepository->findByEmail($request->get('email'));

        if (!$user || !password_verify($request->get('password'), $user->password)) {
            $this->render('auth/login', [
                'errors' => ['email' => 'Credenciales incorrectas.'],
                'old' => $request->all(),
            ]);
            return;
        }

        $_SESSION['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $this->redirect('');
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        $this->redirect('login');
    }

    public function showCreateUser(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('');
            return;
        }

        $this->render('admin/create-user');
    }

    public function createUser(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('');
            return;
        }

        $request = new UserRequest($_POST);

        if (!$request->validateRegister()) {
            $this->render('admin/create-user', [
                'errors' => $request->getErrors(),
                'old' => $request->all(),
            ]);
            return;
        }

        $existingUser = $this->userRepository->findByEmail($request->get('email'));
        if ($existingUser) {
            $this->render('admin/create-user', [
                'errors' => ['email' => 'Este email ya está registrado.'],
                'old' => $request->all(),
            ]);
            return;
        }

        $role = in_array($_POST['role'] ?? '', ['admin', 'user']) ? $_POST['role'] : 'user';

        $user = new User(
            id: 0,
            name: $request->get('name'),
            email: $request->get('email'),
            password: password_hash($request->get('password'), PASSWORD_BCRYPT),
            role: $role
        );

        $this->userRepository->save($user);
        $this->redirect('admin/users/create');
    }

    private function isAdmin(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }
}
