<?php

namespace App\Controllers;

use App\Services\EmailService;
use App\Services\ProductoService;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use PHPMailer\PHPMailer\Exception;

class CheckoutController extends BaseController
{
    private OrderRepository $orderRepository;
    private ProductoService $productoService;
    private UserRepository  $userRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->productoService = new ProductoService();
        $this->userRepository  = new UserRepository();
    }

    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('login');
            return;
        }

        $carrito = $_SESSION['carrito'] ?? [];

        if (empty($carrito)) {
            $this->redirect('carrito');
            return;
        }

        $ids      = array_map('intval', array_keys($carrito));
        $productos = $this->productoService->obtenerPorIds($ids);
        $total    = 0.0;

        foreach ($productos as $producto) {
            $total += $producto->price * (int)($carrito[$producto->id] ?? 0);
        }

        $this->render('pages/checkout', compact('carrito', 'productos', 'total'));
    }

    public function confirmar(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('login');
            return;
        }

        $carrito = $_SESSION['carrito'] ?? [];

        if (empty($carrito)) {
            $this->redirect('carrito');
            return;
        }

        // Sanitizar y validar dirección de envío
        $direccion = trim(strip_tags($_POST['direccion'] ?? ''));
        if ($direccion === '') {
            $_SESSION['checkout_error'] = 'La dirección de envío es obligatoria.';
            $this->redirect('checkout');
            return;
        }

        $userId = (int)$_SESSION['user']['id'];
        $order  = $this->orderRepository->findPendingByUserId($userId);

        if (!$order) {
            $_SESSION['checkout_error'] = 'No se encontró un pedido activo. Por favor, vuelve al carrito.';
            $this->redirect('carrito');
            return;
        }

        // Confirmar el pedido en la base de datos
        $this->orderRepository->confirmarPedido($order->id);
        $order->status = 'confirmed';

        // Hidratar productos para el email
        $ids       = array_map('intval', array_keys($carrito));
        $listaProductos = $this->productoService->obtenerPorIds($ids);
        $mapaProductos  = [];
        foreach ($listaProductos as $p) {
            $mapaProductos[$p->id] = $p;
        }

        // Enviar correo de confirmación
        $clienteEmail  = $_SESSION['user']['email'];
        $clienteNombre = $_SESSION['user']['name'];

        try {
            $emailService = new EmailService();
            $emailService->enviarConfirmacionPedido(
                $order,
                $carrito,
                $listaProductos,
                $clienteEmail,
                $clienteNombre,
                $direccion
            );
        } catch (Exception $e) {
            // El pedido ya está confirmado; registramos el fallo sin bloquearlo
            error_log('[EmailService] No se pudo enviar el correo de confirmación: ' . $e->getMessage());
        }

        // Limpiar carrito de sesión
        $_SESSION['carrito'] = [];

        // Pasar datos a la vista de confirmación a través de la sesión
        $_SESSION['pedido_confirmado'] = [
            'order_id'  => $order->id,
            'total'     => $order->total,
            'direccion' => $direccion,
            'nombre'    => $clienteNombre,
            'email'     => $clienteEmail,
            'fecha'     => date('d/m/Y H:i'),
        ];

        $this->redirect('confirmacion');
    }

    public function confirmacion(): void
    {
        $pedido = $_SESSION['pedido_confirmado'] ?? null;
        unset($_SESSION['pedido_confirmado']);

        $this->render('pages/confirmacion', compact('pedido'));
    }
}
