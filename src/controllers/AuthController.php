<?php

namespace App\Controllers;

use App\Request\UserRequest;
use App\Request\LoginRequest;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Controllers\CarritoController;
use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;

class AuthController extends BaseController
{
    private UserRepository $userRepository;
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->orderRepository = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
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

        try {
            $emailService = new EmailService();
            $emailService->enviarBienvenida($user->email, $user->name);
        } catch (Exception $e) {
            error_log('[EmailService] No se pudo enviar el correo de bienvenida: ' . $e->getMessage());
        }

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

        // --- SESIÓN DEL USUARIO ---
        $_SESSION['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        // --- LÓGICA DE PERSISTENCIA DEL CARRITO ---
        $oldSessionId = $_SESSION['carrito_temporal_id'] ?? null;

        // 1. Si era invitado, migramos su carrito en la DB
        if ($oldSessionId) {
            $this->orderItemRepository->migrarCarrito($user->id, $oldSessionId);
            unset($_SESSION['carrito_temporal_id']);
        }

        // 2. Cargamos el carrito de la DB a la Sesión (para que no aparezca vacío)
        $pedidoPendiente = $this->orderRepository->findPendingByUserId($user->id);
        if ($pedidoPendiente) {
            $itemsGuardados = $this->orderItemRepository->findByOrderId($pedidoPendiente->id);
            $_SESSION['carrito'] = []; 
            foreach ($itemsGuardados as $item) {
                $_SESSION['carrito'][(int)$item->product_id] = (int)$item->quantity;
            }
        }

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

    private function buildGoogleClient(): \Google\Client
    {
        $client = new \Google\Client();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
        $client->addScope(\Google\Service\Oauth2::USERINFO_EMAIL);
        $client->addScope(\Google\Service\Oauth2::USERINFO_PROFILE);
        return $client;
    }

    public function googleRedirect(): void
    {
        $client = $this->buildGoogleClient();
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        $client->setState($state);
        header('Location: ' . $client->createAuthUrl());
        exit();
    }

    public function googleCallback(): void
    {
        $receivedState = $_GET['state'] ?? '';
        $expectedState = $_SESSION['oauth_state'] ?? '';

        if (empty($receivedState) || !hash_equals($expectedState, $receivedState)) {
            unset($_SESSION['oauth_state']);
            $this->render('auth/login', [
                'errors' => ['email' => 'Estado de seguridad inválido. Inténtalo de nuevo.'],
            ]);
            return;
        }
        unset($_SESSION['oauth_state']);

        $code = $_GET['code'] ?? '';
        if (empty($code)) {
            $this->render('auth/login', [
                'errors' => ['email' => 'Google no envió el código de autorización.'],
            ]);
            return;
        }

        $client = $this->buildGoogleClient();
        $token  = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            $this->render('auth/login', [
                'errors' => ['email' => 'Error al autenticar con Google: ' . htmlspecialchars($token['error'])],
            ]);
            return;
        }

        $client->setAccessToken($token);
        $oauth2Service = new \Google\Service\Oauth2($client);
        $googleUser    = $oauth2Service->userinfo->get();

        $email = $googleUser->getEmail();
        $name  = $googleUser->getName() ?? $email;

        if (!$email) {
            $this->render('auth/login', [
                'errors' => ['email' => 'No se pudo obtener el email desde Google.'],
            ]);
            return;
        }

        $user = $this->userRepository->findOrCreateGoogleUser($email, $name);

        $_SESSION['user'] = [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ];

        // --- LÓGICA DE PERSISTENCIA DEL CARRITO ---
        $oldSessionId = $_SESSION['carrito_temporal_id'] ?? null;

        if ($oldSessionId) {
            $this->orderItemRepository->migrarCarrito($user->id, $oldSessionId);
            unset($_SESSION['carrito_temporal_id']);
        }

        $order = $this->orderRepository->findPendingByUserId($user->id);
        if ($order) {
            $items = $this->orderItemRepository->findByOrderId($order->id);
            $_SESSION['carrito'] = [];
            foreach ($items as $item) {
                $_SESSION['carrito'][(int)$item->product_id] = (int)$item->quantity;
            }
        }

        $this->redirect('');
    }
}