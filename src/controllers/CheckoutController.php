<?php

namespace App\Controllers;

use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;

class CheckoutController extends BaseController
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
    }

    /**
     * Muestra la pasarela de pago / resumen del pedido
     */
    public function index(): void
    {
        // El proceso requiere que el usuario esté logueado
        if (!isset($_SESSION['user'])) {
            $this->redirect('login');
            return;
        }

        $this->render('pages/checkout');
    }

    /**
     * Procesa la compra: Transacción, Stock y Vaciar Carrito
     */
    public function procesar(): void
    {
        // 1. Verificaciones de seguridad
        if (!isset($_SESSION['user']) || empty($_SESSION['carrito'])) {
            $this->redirect('');
            return;
        }

        $userId = (int)$_SESSION['user']['id'];
        
        // Buscamos el pedido 'pending' que hemos mantenido sincronizado
        $pedido = $this->orderRepository->findPendingByUserId($userId);

        if ($pedido) {
            // 2. Ejecutamos la transacción (Cambio de estado y decremento de stock)
            // Le pasamos el carrito actual para saber cuánto descontar de stock
            $exito = $this->orderRepository->finalizarPedido($pedido->id, $_SESSION['carrito']);

            if ($exito) {
        // Recuperamos los productos para el email antes de borrar el carrito
        $carrito = $_SESSION['carrito'];
        $totalPedido = $pedido->total;
        $numPedido = $pedido->id;
        $emailUsuario = $_SESSION['user']['email'];
        $nombreUsuario = $_SESSION['user']['name'];

        // 1. Construir el cuerpo del mensaje
        $asunto = "Confirmación de Pedido #$numPedido - Clothing Store";
        
        $mensaje = "Hola $nombreUsuario,\n\n";
        $mensaje .= "¡Gracias por tu compra! Hemos recibido tu pedido correctamente.\n";
        $mensaje .= "------------------------------------------\n";
        $mensaje .= "Detalles del Pedido #$numPedido:\n";
        
        // Aquí podrías añadir un bucle si quieres listar nombres de productos, 
        // pero con las cantidades y el total ya cumples el requisito básico.
        foreach ($carrito as $id => $cantidad) {
            $mensaje .= "- Producto ID: $id | Cantidad: $cantidad\n";
        }

        $mensaje .= "------------------------------------------\n";
        $mensaje .= "TOTAL DE LA COMPRA: " . number_format($totalPedido, 2) . "€\n";
        $mensaje .= "Lugar de envío: Calle Falsa 123 (Dato de prueba)\n\n";
        $mensaje .= "Nos pondremos en contacto contigo cuando el paquete salga del almacén.";

        // 2. Cabeceras para que no llegue como SPAM (opcional)
        $headers = "From: no-reply@clothingstore.com\r\n";
        $headers .= "Reply-To: soporte@clothingstore.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // 3. Enviar el mail
        @mail($emailUsuario, $asunto, $mensaje, $headers);

        // 4. AHORA SÍ, vaciamos el carrito y redirigimos
        unset($_SESSION['carrito']);
        $this->redirect('confirmacion?id=' . $numPedido);
    }
        }
    }

    /**
     * Muestra la página de éxito final
     */
    public function confirmacion(): void
    {
        $pedidoId = $_GET['id'] ?? null;
        $this->render('pages/confirmacion', ['pedidoId' => $pedidoId]);
    }
}