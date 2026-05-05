<?php

namespace App\Controllers;

use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;

class PedidoController extends BaseController
{
    private OrderRepository $orderRepository;
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
        $isAdmin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';

        if ($isAdmin) {
            $pedidos = $this->orderRepository->findAllConfirmed();
        } else {
            $pedidos = $this->orderRepository->findAllByUserId($userId);
        }
        // si es admin ve todos y si es usuario normal, ve solo los suyos con compact, que es
        // lo mismo que ['pedidos'=>$pedidos]
        $this->render('pedidos/index', compact('pedidos'));
    }

    /**
     * Muestra el detalle de un pedido concreto del usuario autenticado.
     */
    public function ver(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('login');
            return;
        }

        $pedidoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $userId   = (int)$_SESSION['user']['id'];
        $isAdmin  = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';

        if ($pedidoId <= 0) {
            $this->redirect('mis-pedidos');
            return;
        }

        if ($isAdmin) {
            $pedido = $this->orderRepository->findById($pedidoId);
            //si es normal usa el id y ademas comprueba q el pedido pertenece a ese usuarios
        } else {
            $pedido = $this->orderRepository->findByIdAndUserId($pedidoId, $userId);
        }

        if (!$pedido) {
            $this->redirect('mis-pedidos');
            return;
        }

        $items = $this->orderItemRepository->findDetailedByOrderId($pedido->id);

        $this->render('pedidos/ver', compact('pedido', 'items'));
    }

    /**
     * Actualiza el estado de un pedido (Solo Admin)
     */
    public function actualizarEstado(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('mis-pedidos');
            return;
        }

        $pedidoId = isset($_POST['pedido_id']) ? (int)$_POST['pedido_id'] : 0;
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';

        if ($pedidoId > 0 && !empty($status)) {
            $this->orderRepository->updateStatus($pedidoId, $status);
        }

        $this->redirect('mis-pedidos');
    }
}
