<?php

namespace App\Services;

use App\Models\Order;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['SMTP_HOST']       ?? 'sandbox.smtp.mailtrap.io';
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['SMTP_USER']       ?? '';
        $this->mailer->Password   = $_ENV['SMTP_PASS']       ?? '';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = (int)($_ENV['SMTP_PORT'] ?? 2525);
        $this->mailer->CharSet    = 'UTF-8';

        $fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@clothingstore.com';
        $fromName  = $_ENV['SMTP_FROM_NAME']  ?? 'Clothing Store';
        $this->mailer->setFrom($fromEmail, $fromName);
    }

    /**
     * Envía el correo de confirmación de pedido al cliente.
     *
     * @param Order  $order          El pedido confirmado.
     * @param array  $carrito        ['product_id' => cantidad, ...]
     * @param array  $productos      Objetos producto indexados por id.
     * @param string $clienteEmail   Email del cliente.
     * @param string $clienteNombre  Nombre del cliente.
     * @param string $direccionEnvio Dirección de envío.
     *
     * @throws Exception Si el envío falla.
     */
    public function enviarConfirmacionPedido(
        Order $order,
        array $carrito,
        array $productos,
        string $clienteEmail,
        string $clienteNombre,
        string $direccionEnvio
    ): void {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($clienteEmail, $clienteNombre);

        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'Confirmación de Pedido #' . $order->id . ' - Clothing Store';
        $this->mailer->Body    = $this->construirCuerpoHtml($order, $carrito, $productos, $clienteNombre, $direccionEnvio);
        $this->mailer->AltBody = $this->construirCuerpoTexto($order, $carrito, $productos, $clienteNombre, $direccionEnvio);

        $this->mailer->send();
    }

    /**
     * Envía el correo de bienvenida tras el registro.
     *
     * @throws Exception Si el envío falla.
     */
    public function enviarBienvenida(string $email, string $nombre): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($email, $nombre);

        $this->mailer->isHTML(true);
        $this->mailer->Subject = '¡Bienvenido/a a Clothing Store, ' . $nombre . '!';
        $this->mailer->Body    = "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
                <h2 style='color:#198754;'>¡Bienvenido/a, {$nombre}!</h2>
                <p>Tu cuenta ha sido creada correctamente en <strong>Clothing Store</strong>.</p>
                <p>Ya puedes iniciar sesión y explorar nuestro catálogo.</p>
                <hr>
                <p style='font-size:12px;color:#888;'>Si no creaste esta cuenta, ignora este correo.</p>
            </div>";
        $this->mailer->AltBody = "¡Bienvenido/a, {$nombre}! Tu cuenta en Clothing Store ha sido creada correctamente.";

        $this->mailer->send();
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    private function construirCuerpoHtml(
        Order $order,
        array $carrito,
        array $productos,
        string $clienteNombre,
        string $direccionEnvio
    ): string {
        $filasHtml = $this->generarFilasTablaHtml($carrito, $productos);
        $total     = number_format($order->total, 2);
        $fecha     = date('d/m/Y H:i');

        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
            <h2 style='color:#198754;'>¡Gracias por tu compra, {$clienteNombre}!</h2>
            <p>Hemos recibido tu pedido correctamente. Aquí tienes el resumen:</p>

            <table style='width:100%;border-collapse:collapse;margin:16px 0;'>
                <tr style='background:#f8f9fa;'>
                    <th style='padding:8px;border:1px solid #dee2e6;text-align:left;'>Producto</th>
                    <th style='padding:8px;border:1px solid #dee2e6;text-align:center;'>Cant.</th>
                    <th style='padding:8px;border:1px solid #dee2e6;text-align:right;'>Precio/ud.</th>
                    <th style='padding:8px;border:1px solid #dee2e6;text-align:right;'>Subtotal</th>
                </tr>
                {$filasHtml}
                <tr style='font-weight:bold;background:#f8f9fa;'>
                    <td colspan='3' style='padding:8px;border:1px solid #dee2e6;text-align:right;'>Total</td>
                    <td style='padding:8px;border:1px solid #dee2e6;text-align:right;'>{$total} €</td>
                </tr>
            </table>

            <p><strong>Dirección de envío:</strong> {$direccionEnvio}</p>
            <p><strong>Fecha:</strong> {$fecha}</p>
            <p><strong>Número de pedido:</strong> #{$order->id}</p>

            <hr>
            <p style='font-size:12px;color:#888;'>Nos pondremos en contacto cuando el paquete salga del almacén.</p>
        </div>";
    }

    private function generarFilasTablaHtml(array $carrito, array $productos): string
    {
        $filas = '';
        foreach ($carrito as $id => $cantidad) {
            $producto = $productos[$id] ?? null;
            if (!$producto) {
                continue;
            }
            $nombre   = htmlspecialchars($producto->name);
            $precio   = number_format($producto->price, 2);
            $subtotal = number_format($producto->price * $cantidad, 2);
            $filas   .= "
                <tr>
                    <td style='padding:8px;border:1px solid #dee2e6;'>{$nombre}</td>
                    <td style='padding:8px;border:1px solid #dee2e6;text-align:center;'>{$cantidad}</td>
                    <td style='padding:8px;border:1px solid #dee2e6;text-align:right;'>{$precio} €</td>
                    <td style='padding:8px;border:1px solid #dee2e6;text-align:right;'>{$subtotal} €</td>
                </tr>";
        }
        return $filas;
    }

    private function construirCuerpoTexto(
        Order $order,
        array $carrito,
        array $productos,
        string $clienteNombre,
        string $direccionEnvio
    ): string {
        $lineas = "¡Gracias por tu compra, {$clienteNombre}!\n\n";
        $lineas .= "Pedido #{$order->id} — " . date('d/m/Y H:i') . "\n";
        $lineas .= str_repeat('-', 40) . "\n";

        foreach ($carrito as $id => $cantidad) {
            $producto = $productos[$id] ?? null;
            if (!$producto) {
                continue;
            }
            $subtotal = number_format($producto->price * $cantidad, 2);
            $lineas  .= "{$producto->name} x{$cantidad} — {$subtotal} €\n";
        }

        $lineas .= str_repeat('-', 40) . "\n";
        $lineas .= 'TOTAL: ' . number_format($order->total, 2) . " €\n\n";
        $lineas .= "Dirección de envío: {$direccionEnvio}\n";
        $lineas .= "Nos pondremos en contacto cuando el paquete salga del almacén.\n";

        return $lineas;
    }
}
