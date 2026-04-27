<?php

namespace App\Controllers;

use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;
use App\Services\EmailService;
use App\Services\ProductoService;
use PHPMailer\PHPMailer\Exception;

class CheckoutController extends BaseController
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;

    public function __construct()
    {
        $this->orderRepository    = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
    }

    /**
     * Muestra el resumen del pedido con tabla de productos
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

        $productoService = new ProductoService();
        $productosLista  = $productoService->obtenerPorIds(array_keys($carrito));

        $productos = [];
        foreach ($productosLista as $p) {
            $productos[$p->id] = $p;
        }

        $total = 0;
        foreach ($carrito as $id => $cantidad) {
            if (isset($productos[$id])) {
                $total += $productos[$id]->price * $cantidad;
            }
        }

        $this->render('pages/checkout', compact('carrito', 'productos', 'total'));
    }

    /**
     * Procesa la compra: transacción, stock, email y redirección
     */
    public function procesar(): void
    {
        if (!isset($_SESSION['user']) || empty($_SESSION['carrito'])) {
            $this->redirect('');
            return;
        }

        $userId    = (int)$_SESSION['user']['id'];
        $direccion = trim(strip_tags($_POST['direccion'] ?? ''));

        if (empty($direccion)) {
            $this->redirect('checkout');
            return;
        }

        $pedido = $this->orderRepository->findPendingByUserId($userId);

        if (!$pedido) {
            $this->redirect('carrito');
            return;
        }

        $exito = $this->orderRepository->finalizarPedido($pedido->id, $_SESSION['carrito']);

        if (!$exito) {
            $this->redirect('carrito');
            return;
        }

        // Hidratar productos para el email
        $carrito         = $_SESSION['carrito'];
        $productoService = new ProductoService();
        $productosLista  = $productoService->obtenerPorIds(array_keys($carrito));
        $productos       = [];
        foreach ($productosLista as $p) {
            $productos[$p->id] = $p;
        }

        // Enviar email de confirmación (no bloquea si falla)
        try {
            $emailService = new EmailService();
            $emailService->enviarConfirmacionPedido(
                $pedido,
                $carrito,
                $productos,
                $_SESSION['user']['email'],
                $_SESSION['user']['name'],
                $direccion
            );
        } catch (Exception $e) {
            // El pedido ya está confirmado; el email es opcional
        } catch (\Exception $e) {
            // Ídem
        }

        // Guardar datos flash para la vista de confirmación
        $_SESSION['pedido_confirmado'] = [
            'id'        => $pedido->id,
            'nombre'    => $_SESSION['user']['name'],
            'email'     => $_SESSION['user']['email'],
            'direccion' => $direccion,
            'total'     => $pedido->total,
            'fecha'     => date('Y-m-d H:i:s'),
        ];

        unset($_SESSION['carrito']);
        $this->redirect('confirmacion');
    }

    /**
     * Muestra la página de éxito con los datos del pedido
     */
    public function confirmacion(): void
    {
        $pedido = $_SESSION['pedido_confirmado'] ?? null;
        unset($_SESSION['pedido_confirmado']);

        $this->render('pages/confirmacion', compact('pedido'));
    }
}