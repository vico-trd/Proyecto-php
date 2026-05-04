<?php

namespace App\Controllers;

use App\Requests\UserRequest;
use App\Requests\LoginRequest;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\PasswordResetRepository;
use App\Services\EmailService;
use PHPMailer\PHPMailer\Exception;

class AuthController extends BaseController
{
    private UserRepository $userRepository;
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;
    private PasswordResetRepository $passwordResetRepository;

    public function __construct()
    {
        $this->userRepository          = new UserRepository();
        $this->orderRepository         = new OrderRepository();
        $this->orderItemRepository     = new OrderItemRepository();
        $this->passwordResetRepository = new PasswordResetRepository();
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
                'old'    => $request->all(),
            ]);
            return;
        }

        $user = $this->saveNewUser($request, 'user');

        if (!$user) {
            $this->render('auth/register', [
                'errors' => ['email' => 'Este email ya está registrado.'],
                'old'    => $request->all(),
            ]);
            return;
        }

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
        // Pre-rellena el email si el usuario marcó "Recuérdame" anteriormente
        $rememberedEmail = isset($_COOKIE['remember_email'])
            ? htmlspecialchars($_COOKIE['remember_email'], ENT_QUOTES, 'UTF-8')
            : '';

        $this->render('auth/login', ['remembered_email' => $rememberedEmail]);
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

        // --- COOKIE "RECUÉRDAME" ---
        if (!empty($_POST['remember_me'])) {
            // Guarda el email 30 días en una cookie segura (httponly)
            setcookie('remember_email', $user->email, [
                'expires'  => time() + (30 * 24 * 60 * 60),
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        } else {
            // Si no marcó el checkbox, elimina la cookie anterior si existía
            setcookie('remember_email', '', ['expires' => time() - 3600, 'path' => '/']);
        }

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
                'old'    => $request->all(),
            ]);
            return;
        }

        $role = in_array($_POST['role'] ?? '', ['admin', 'user']) ? $_POST['role'] : 'user';
        $user = $this->saveNewUser($request, $role);

        if (!$user) {
            $this->render('admin/create-user', [
                'errors' => ['email' => 'Este email ya está registrado.'],
                'old'    => $request->all(),
            ]);
            return;
        }

        $this->redirect('admin/users/create');
    }

    private function saveNewUser(UserRequest $request, string $role): ?User
    {
        if ($this->userRepository->findByEmail($request->get('email'))) {
            return null;
        }

        $user = new User(
            id: 0,
            name: $request->get('name'),
            email: $request->get('email'),
            password: password_hash($request->get('password'), PASSWORD_BCRYPT),
            role: $role
        );

        $this->userRepository->save($user);
        return $user;
    }

    private function isAdmin(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    // ─── Recuperación de contraseña ────────────────────────────────────────────

    public function showForgotPassword(): void
    {
        $this->render('auth/forgot-password');
    }

    public function forgotPassword(): void
    {
        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/forgot-password', [
                'errors' => ['email' => 'Introduce un email válido.'],
                'old'    => ['email' => $email],
            ]);
            return;
        }

        // Respuesta genérica para no revelar si el email existe
        $user = $this->userRepository->findByEmail($email);

        if ($user) {
            $token     = bin2hex(random_bytes(32));
            $expiresAt = new \DateTimeImmutable('+1 hour');

            $this->passwordResetRepository->create($email, $token, $expiresAt);

            $resetUrl = 'http://' . $_SERVER['HTTP_HOST'] . BASE_URL . 'reset-password&token=' . urlencode($token);

            try {
                $emailService = new EmailService();
                $emailService->enviarRecuperacion($user->email, $user->name, $resetUrl);
            } catch (Exception $e) {
                error_log('[EmailService] No se pudo enviar el correo de recuperación: ' . $e->getMessage());
            }
        }

        $this->render('auth/forgot-password', [
            'success' => 'Si ese email está registrado, recibirás un enlace para restablecer tu contraseña.',
        ]);
    }

    public function showResetPassword(): void
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $this->redirect('forgot-password');
            return;
        }

        $record = $this->passwordResetRepository->findValidToken($token);

        if (!$record) {
            $this->render('auth/forgot-password', [
                'errors' => ['email' => 'El enlace no es válido o ha expirado. Solicita uno nuevo.'],
            ]);
            return;
        }

        $this->render('auth/reset-password', ['token' => htmlspecialchars($token, ENT_QUOTES, 'UTF-8')]);
    }

    public function resetPassword(): void
    {
        $token    = $_POST['token']            ?? '';
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        $record = $this->passwordResetRepository->findValidToken($token);

        if (!$record) {
            $this->render('auth/forgot-password', [
                'errors' => ['email' => 'El enlace no es válido o ha expirado. Solicita uno nuevo.'],
            ]);
            return;
        }

        $errors = [];

        if (strlen($password) < 8) {
            $errors['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if ($password !== $confirm) {
            $errors['confirm_password'] = 'Las contraseñas no coinciden.';
        }

        if ($errors) {
            $this->render('auth/reset-password', [
                'token'  => htmlspecialchars($token, ENT_QUOTES, 'UTF-8'),
                'errors' => $errors,
            ]);
            return;
        }

        $this->userRepository->updatePassword($record['email'], password_hash($password, PASSWORD_BCRYPT));
        $this->passwordResetRepository->deleteByToken($token);

        $this->render('auth/login', [
            'success' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.',
        ]);
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