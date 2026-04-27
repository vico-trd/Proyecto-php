<?php

namespace App\Controllers;

use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;

class PedidoController extends BaseController
{
    private OrderRepository     $orderRepository;
    private OrderItemRepository $orderItemRepository;

    public function __construct()
    {
        $this->orderRepository     = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
    }

    /**
     * Lista todos los pedidos confirmados del usuario autenticado.
     */
    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('login');
            return;
        }

        $userId  = (int)$_SESSION['user']['id'];
        $pedidos = $this->orderRepository->findAllByUserId($userId);

        $this->render('pedidos/index', compact('pedidos'));
    }

    /**
     * Muestra el detalle de un pedido concreto (solo si pertenece al usuario).
     */
    public function ver(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('login');
            return;
        }

        $pedidoId = (int)($_GET['id'] ?? 0);
        $userId   = (int)$_SESSION['user']['id'];

        if ($pedidoId <= 0) {
            $this->redirect('mis-pedidos');
            return;
        }

        $pedido = $this->orderRepository->findByIdAndUserId($pedidoId, $userId);

        if (!$pedido) {
            $this->redirect('mis-pedidos');
            return;
        }

        $items = $this->orderItemRepository->findDetailedByOrderId($pedido->id);

        $this->render('pedidos/ver', compact('pedido', 'items'));
    }
}
