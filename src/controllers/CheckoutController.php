<?php

namespace App\Controllers;

use App\Services\EmailService;
use App\Services\ProductoService;
use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;
use PHPMailer\PHPMailer\Exception;

class CheckoutController extends BaseController
{
    private OrderRepository    $orderRepository;
    private OrderItemRepository $orderItemRepository;
    private ProductoService    $productoService;

    public function __construct()
    {
        $this->orderRepository     = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
        $this->productoService     = new ProductoService();
    }

    /**
     * Muestra el resumen del pedido con productos, cantidades y total real.
     */
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

        $error = $_SESSION['checkout_error'] ?? null;
        unset($_SESSION['checkout_error']);

        $this->render('pages/checkout', compact('carrito', 'productos', 'total', 'error'));
    }

    /**
     * Procesa la compra: transacción, decremento de stock y notificación por email.
     */
    public function procesar(): void
    {
        if (!isset($_SESSION['user']) || empty($_SESSION['carrito'])) {
            $this->redirect('');
            return;
        }

        // Sanitizar dirección
        $direccion = trim(strip_tags($_POST['direccion'] ?? ''));
        if ($direccion === '') {
            $_SESSION['checkout_error'] = 'La dirección de envío es obligatoria.';
            $this->redirect('checkout');
            return;
        }

        $userId  = (int)$_SESSION['user']['id'];
        $carrito = $_SESSION['carrito'];

        $pedido = $this->orderRepository->findPendingByUserId($userId);

        if (!$pedido) {
            $_SESSION['checkout_error'] = 'No se encontró un pedido activo. Vuelve al carrito.';
            $this->redirect('carrito');
            return;
        }

        // Hidratar productos para el email ANTES de finalizar el pedido
        $ids            = array_map('intval', array_keys($carrito));
        $listaProductos = $this->productoService->obtenerPorIds($ids);

        // Transacción: cambio de estado + decremento de stock
        $exito = $this->orderRepository->finalizarPedido($pedido->id, $carrito);

        if (!$exito) {
            $_SESSION['checkout_error'] = 'Ha ocurrido un error al procesar tu pedido. Inténtalo de nuevo.';
            $this->redirect('checkout');
            return;
        }

        // Enviar email de confirmación con PHPMailer y EmailService
        try {
            $emailService = new EmailService();
            $emailService->enviarConfirmacionPedido(
                $pedido,
                $carrito,
                $listaProductos,
                $_SESSION['user']['email'],
                $_SESSION['user']['name'],
                $direccion
            );
        } catch (Exception $e) {
            error_log('[EmailService] Error al enviar confirmación de pedido: ' . $e->getMessage());
        }

        // Guardar datos para la vista de confirmación (sesión flash)
        $_SESSION['pedido_confirmado'] = [
            'order_id'  => $pedido->id,
            'total'     => $pedido->total,
            'direccion' => $direccion,
            'nombre'    => $_SESSION['user']['name'],
            'email'     => $_SESSION['user']['email'],
            'fecha'     => date('d/m/Y H:i'),
        ];

        unset($_SESSION['carrito']);
        $this->redirect('confirmacion');
    }

    /**
     * Muestra la página de confirmación con los datos del pedido.
     */
    public function confirmacion(): void
    {
        $pedido = $_SESSION['pedido_confirmado'] ?? null;
        unset($_SESSION['pedido_confirmado']);

        $this->render('pages/confirmacion', compact('pedido'));
    }
}
