<?php

namespace App\Controllers;

use App\Request\UserRequest;
use App\Request\LoginRequest;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Controllers\CarritoController;
use App\Services\EmailService;
use PHPMailer\PHPMailer\Exception;

class AuthController extends BaseController
{
     private UserRepository $userRepository;

    public function __construct()
    {
          $this->userRepository = new UserRepository();
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

    // --- RECUPERACIÓN DEL CARRITO ---
    $orderRepo = new \App\Repositories\OrderRepository();
    $orderItemRepo = new \App\Repositories\OrderItemRepository();

    $pedidoPendiente = $orderRepo->findPendingByUserId($user->id);

    if ($pedidoPendiente) {
        $itemsGuardados = $orderItemRepo->findByOrderId($pedidoPendiente->id);
        
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        foreach ($itemsGuardados as $item) {
            $idProd = (int)$item->product_id;
            $cantidad = (int)$item->quantity;

            if (isset($_SESSION['carrito'][$idProd])) {
                $_SESSION['carrito'][$idProd] += $cantidad;
            } else {
                $_SESSION['carrito'][$idProd] = $cantidad;
            }
        }

        // Sincronizamos de vuelta: Si el usuario tenía cosas como invitado,
        // ahora se guardarán oficialmente en la base de datos bajo su ID.
        $carritoCtrl = new CarritoController();
        $carritoCtrl->sincronizarConDB();
    }

    // Redirigir siempre al finalizar el proceso de login exitoso
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

        $this->redirect('');
    }
}
