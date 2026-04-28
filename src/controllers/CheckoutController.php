<?php

namespace App\Controllers;

use App\Services\EmailService;
use App\Services\PayPalService;
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

    // ─── PayPal ──────────────────────────────────────────────────────────────

    /**
     * Guarda la dirección en sesión y crea la orden PayPal → redirige a PayPal.
     */
    public function paypalCrear(): void
    {
        if (!isset($_SESSION['user']) || empty($_SESSION['carrito'])) {
            $this->redirect('');
            return;
        }

        $direccion = trim(strip_tags($_POST['direccion'] ?? ''));
        if ($direccion === '') {
            $_SESSION['checkout_error'] = 'La dirección de envío es obligatoria.';
            $this->redirect('checkout');
            return;
        }

        $_SESSION['paypal_direccion'] = $direccion;

        $carrito   = $_SESSION['carrito'];
        $ids       = array_map('intval', array_keys($carrito));
        $productos = $this->productoService->obtenerPorIds($ids);
        $total     = 0.0;

        foreach ($productos as $p) {
            $total += $p->price * (int)($carrito[$p->id] ?? 0);
        }

        // Construir URLs absolutas para el callback de PayPal
        $scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base      = $scheme . '://' . $host . '/Proyecto-php/public/index.php?url=';
        $returnUrl = $base . 'checkout/paypal/exito';
        $cancelUrl = $base . 'checkout/paypal/cancelar';

        try {
            $paypalService = new PayPalService();
            $orden = $paypalService->crearOrden($total, $returnUrl, $cancelUrl);
            $_SESSION['paypal_order_id'] = $orden['id'];
            header('Location: ' . $orden['approvalUrl']);
            exit;
        } catch (\Exception $e) {
            error_log('[PayPal] Error creando orden: ' . $e->getMessage());
            $_SESSION['checkout_error'] = 'No se pudo conectar con PayPal. Inténtalo de nuevo.';
            $this->redirect('checkout');
        }
    }

    /**
     * PayPal redirige aquí tras la aprobación. Captura el pago y finaliza el pedido.
     */
    public function paypalExito(): void
    {
        if (!isset($_SESSION['user']) || empty($_SESSION['carrito'])) {
            $this->redirect('');
            return;
        }

        $paypalOrderId  = $_GET['token']           ?? '';
        $sessionOrderId = $_SESSION['paypal_order_id'] ?? '';

        if ($paypalOrderId === '' || $paypalOrderId !== $sessionOrderId) {
            $_SESSION['checkout_error'] = 'Referencia de pago no válida. Vuelve a intentarlo.';
            $this->redirect('checkout');
            return;
        }

        try {
            $paypalService = new PayPalService();
            $captura = $paypalService->capturarOrden($paypalOrderId);

            if (($captura['status'] ?? '') !== 'COMPLETED') {
                $_SESSION['checkout_error'] = 'El pago no fue completado por PayPal. Inténtalo de nuevo.';
                $this->redirect('checkout');
                return;
            }
        } catch (\Exception $e) {
            error_log('[PayPal] Error capturando orden: ' . $e->getMessage());
            $_SESSION['checkout_error'] = 'Error al procesar el pago con PayPal.';
            $this->redirect('checkout');
            return;
        }

        $userId    = (int)$_SESSION['user']['id'];
        $carrito   = $_SESSION['carrito'];
        $direccion = $_SESSION['paypal_direccion'] ?? 'No especificada';

        $pedido = $this->orderRepository->findPendingByUserId($userId);
        if (!$pedido) {
            $_SESSION['checkout_error'] = 'No se encontró un pedido activo.';
            $this->redirect('carrito');
            return;
        }

        $ids            = array_map('intval', array_keys($carrito));
        $listaProductos = $this->productoService->obtenerPorIds($ids);

        $exito = $this->orderRepository->finalizarPedido($pedido->id, $carrito);
        if (!$exito) {
            error_log('[PayPal] Pago capturado pero error al finalizar pedido #' . $pedido->id);
            $_SESSION['checkout_error'] = 'Pago recibido, pero ocurrió un error al confirmar el pedido. Contacta con soporte.';
            $this->redirect('checkout');
            return;
        }

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
            error_log('[EmailService] Error al enviar confirmación: ' . $e->getMessage());
        }

        $_SESSION['pedido_confirmado'] = [
            'order_id'  => $pedido->id,
            'total'     => $pedido->total,
            'direccion' => $direccion,
            'nombre'    => $_SESSION['user']['name'],
            'email'     => $_SESSION['user']['email'],
            'fecha'     => date('d/m/Y H:i'),
            'metodo'    => 'PayPal',
        ];

        unset($_SESSION['carrito'], $_SESSION['paypal_order_id'], $_SESSION['paypal_direccion']);
        $this->redirect('confirmacion');
    }

    /**
     * El usuario canceló el pago en PayPal. Vuelve al checkout con mensaje.
     */
    public function paypalCancelar(): void
    {
        unset($_SESSION['paypal_order_id'], $_SESSION['paypal_direccion']);
        $_SESSION['checkout_error'] = 'Has cancelado el pago con PayPal. Puedes intentarlo de nuevo.';
        $this->redirect('checkout');
    }
}
