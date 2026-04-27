<?php

namespace App\Controllers;

use App\Services\ProductoService;
use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;
use App\Controllers\BaseController;

class CarritoController extends BaseController
{
    private ProductoService $productoService;
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;

    public function __construct()
    {
        $this->productoService     = new ProductoService();
        $this->orderRepository     = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
    }

    public function index(): void
    {
        if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $carrito        = $_SESSION['carrito'];
        $productos      = [];
        $totalArticulos = 0;
        $importeTotal   = 0.0;

        if (!empty($carrito)) {
            $ids       = array_map('intval', array_keys($carrito));
            $productos = $this->productoService->obtenerPorIds($ids);

            foreach ($productos as $producto) {
                $cantidad        = (int)($carrito[$producto->id] ?? 0);
                $totalArticulos += $cantidad;
                $importeTotal   += $producto->price * $cantidad;
            }
        }

        $this->render('carrito/index', compact('carrito', 'productos', 'totalArticulos', 'importeTotal'));
    }

    public function agregar(): void
    {
        $productoId = (int)($_POST['producto_id'] ?? 0);
        $cantidad   = (int)($_POST['cantidad']    ?? 1);

        if ($productoId <= 0 || $cantidad <= 0) {
            $this->redirect('carrito');
            return;
        }

        $producto = $this->productoService->obtenerPorId($productoId);
        if (!$producto) {
            $this->redirect('carrito');
            return;
        }

        if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $cantidadActual = (int)($_SESSION['carrito'][$productoId] ?? 0);
        $nuevaCantidad  = $cantidadActual + $cantidad;

        if ($nuevaCantidad > $producto->stock) {
            $_SESSION['carrito_error'] = 'No hay suficiente stock para "' . htmlspecialchars($producto->name, ENT_QUOTES, 'UTF-8') . '".';
            $this->redirect('carrito');
            return;
        }

        $_SESSION['carrito'][$productoId] = $nuevaCantidad;
        $this->sincronizarConDB();

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer !== '') {
            header('Location: ' . $referer);
            exit();
        }

        $this->redirect('carrito');
    }

    public function incrementar(): void
    {
        $productoId = (int)($_POST['producto_id'] ?? 0);

        if ($productoId <= 0 || !isset($_SESSION['carrito'][$productoId])) {
            $this->redirect('carrito');
            return;
        }

        $producto = $this->productoService->obtenerPorId($productoId);
        if (!$producto) {
            $this->redirect('carrito');
            return;
        }

        $cantidadActual = (int)$_SESSION['carrito'][$productoId];

        if ($cantidadActual + 1 > $producto->stock) {
            $_SESSION['carrito_error'] = 'Stock maximo alcanzado para "' . htmlspecialchars($producto->name, ENT_QUOTES, 'UTF-8') . '".';
        } else {
            $_SESSION['carrito'][$productoId] = $cantidadActual + 1;
            $this->sincronizarConDB();
        }

        $this->redirect('carrito');
    }

    public function decrementar(): void
    {
        $productoId = (int)($_POST['producto_id'] ?? 0);

        if ($productoId <= 0 || !isset($_SESSION['carrito'][$productoId])) {
            $this->redirect('carrito');
            return;
        }

        $cantidadActual = (int)$_SESSION['carrito'][$productoId];

        if ($cantidadActual <= 1) {
            unset($_SESSION['carrito'][$productoId]);
        } else {
            $_SESSION['carrito'][$productoId] = $cantidadActual - 1;
        }

        $this->sincronizarConDB();
        $this->redirect('carrito');
    }

    public function eliminar(): void
    {
        $productoId = (int)($_POST['producto_id'] ?? 0);

        if ($productoId > 0) {
            unset($_SESSION['carrito'][$productoId]);
        }

        $this->sincronizarConDB();
        $this->redirect('carrito');
    }

    public function vaciar(): void
    {
        $_SESSION['carrito'] = [];
        $this->sincronizarConDB();
        $this->redirect('carrito');
    }

   public function sincronizarConDB(): void
{
    $userId = $_SESSION['user']['id'] ?? null;

    // Los invitados no tienen usuario; el carrito vive solo en sesión.
    // Al hacer login, AuthController migra el carrito a la BD.
    if (!$userId) {
        return;
    }

    $carrito = $_SESSION['carrito'] ?? [];

    // 1. Buscar orden pendiente del usuario
    $order = $this->orderRepository->findPendingByUserId((int)$userId);

    // 2. Si el carrito está vacío, borramos la orden de la DB
    if (empty($carrito)) {
        if ($order) {
            $this->orderItemRepository->deleteByOrderId($order->id);
            $this->orderRepository->delete($order->id);
        }
        return;
    }

    // 3. Crear o limpiar la orden existente
    if (!$order) {
        $orderId = $this->orderRepository->createPendingOrder((int)$userId);
    } else {
        $orderId = $order->id;
        $this->orderItemRepository->deleteByOrderId($orderId);
    }

    // 4. Insertar los productos y calcular el total sobre la marcha
    $ids = array_map('intval', array_keys($carrito));
    $productosList = $this->productoService->obtenerPorIds($ids);
    
    $productMap = [];
    foreach ($productosList as $p) {
        $productMap[$p->id] = $p;
    }

    $totalAcumulado = 0.0;
    foreach ($carrito as $productoId => $cantidad) {
        $productoId = (int)$productoId;
        $cantidad = (int)$cantidad;
        $p = $productMap[$productoId] ?? null;

        if ($p) {
            $this->orderItemRepository->insertItem($orderId, $productoId, $cantidad, $p->price);
            $totalAcumulado += $p->price * $cantidad;
        }
    }

    // 5. Actualizar el total en la tabla 'orders' usando el repositorio
    $this->orderRepository->updateTotal($orderId, $totalAcumulado);
}
}
