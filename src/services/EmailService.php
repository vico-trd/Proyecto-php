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
     * @param Order  $order    El pedido confirmado.
     * @param array  $carrito  ['product_id' => cantidad, ...]
     * @param array  $productos Lista de objetos producto indexada por id.
     * @param string $clienteEmail  Email del cliente.
     * @param string $clienteNombre Nombre del cliente.
     * @param string $direccionEnvio Dirección de envío introducida en el checkout.
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
        $this->mailer->Subject = 'Confirmación de pedido #' . $order->id . ' – Clothing Store';
        $this->mailer->Body    = $this->construirCuerpoHtml(
            $order,
            $carrito,
            $productos,
            $clienteNombre,
            $direccionEnvio
        );
        $this->mailer->AltBody = $this->construirCuerpoTexto(
            $order,
            $carrito,
            $productos,
            $clienteNombre,
            $direccionEnvio
        );

        $this->mailer->send();
    }

    /**
     * Envía un correo de bienvenida al usuario recién registrado.
     *
     * @throws Exception Si el envío falla.
     */
    public function enviarBienvenida(string $destinatarioEmail, string $destinatarioNombre): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($destinatarioEmail, $destinatarioNombre);

        $nombreEsc = htmlspecialchars($destinatarioNombre, ENT_QUOTES, 'UTF-8');

        $this->mailer->isHTML(true);
        $this->mailer->Subject = '¡Bienvenido/a a Clothing Store, ' . $destinatarioNombre . '!';
        $this->mailer->Body    = <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8">
        <style>
            body       { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
            .container { max-width:600px; margin:30px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.1); }
            .header    { background:#1a1a1a; color:#fff; padding:24px 32px; }
            .header h1 { margin:0; font-size:22px; letter-spacing:1px; }
            .body      { padding:32px; color:#333; line-height:1.7; }
            .btn       { display:inline-block; margin-top:20px; padding:12px 28px; background:#ff4757; color:#fff; text-decoration:none; border-radius:4px; font-weight:bold; }
            .footer    { background:#f4f4f4; text-align:center; padding:16px; font-size:12px; color:#999; }
        </style>
        </head>
        <body>
            <div class="container">
                <div class="header"><h1>🛍 Clothing Store</h1></div>
                <div class="body">
                    <p>Hola, <strong>{$nombreEsc}</strong>.</p>
                    <p>Tu cuenta ha sido creada correctamente. Ya puedes explorar nuestro catálogo y realizar tus primeras compras.</p>
                    <a href="http://localhost/Proyecto-php/public/" class="btn">Ir a la tienda</a>
                </div>
                <div class="footer">&copy; Clothing Store · Este correo se generó automáticamente.</div>
            </div>
        </body>
        </html>
        HTML;
        $this->mailer->AltBody = "Hola, {$destinatarioNombre}.\n\nTu cuenta en Clothing Store ha sido creada correctamente.\nVisítanos en: http://localhost/Proyecto-php/public/";

        $this->mailer->send();
    }

    // ─── Cuerpo HTML ───────────────────────────────────────────────────────────

    private function construirCuerpoHtml(
        Order $order,
        array $carrito,
        array $productos,
        string $clienteNombre,
        string $direccionEnvio
    ): string {
        $filas = $this->generarFilasTablaHtml($carrito, $productos);
        $total = number_format($order->total, 2, ',', '.');
        $fecha = date('d/m/Y H:i');

        $direccionEnvioEsc = htmlspecialchars($direccionEnvio, ENT_QUOTES, 'UTF-8');
        $clienteNombreEsc  = htmlspecialchars($clienteNombre,  ENT_QUOTES, 'UTF-8');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <style>
                body        { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
                .container  { max-width:600px; margin:30px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.1); }
                .header     { background:#1a1a1a; color:#fff; padding:24px 32px; }
                .header h1  { margin:0; font-size:22px; letter-spacing:1px; }
                .body       { padding:32px; color:#333; }
                .body p     { line-height:1.6; }
                table       { width:100%; border-collapse:collapse; margin:20px 0; }
                th          { background:#1a1a1a; color:#fff; padding:10px 12px; text-align:left; font-size:14px; }
                td          { padding:10px 12px; border-bottom:1px solid #eee; font-size:14px; }
                tr:last-child td { border-bottom:none; }
                .total-row td   { font-weight:bold; background:#f9f9f9; }
                .info-box   { background:#f9f9f9; border-left:4px solid #ff4757; padding:12px 16px; margin:20px 0; border-radius:4px; }
                .footer     { background:#f4f4f4; text-align:center; padding:16px; font-size:12px; color:#999; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🛍 Clothing Store</h1>
                    <p style="margin:4px 0 0;font-size:14px;color:#ccc;">Confirmación de pedido</p>
                </div>
                <div class="body">
                    <p>Hola, <strong>{$clienteNombreEsc}</strong>.</p>
                    <p>Tu pedido ha sido confirmado correctamente. A continuación encontrarás el resumen de tu compra:</p>

                    <div class="info-box">
                        <strong>Nº de pedido:</strong> #{$order->id}<br>
                        <strong>Fecha:</strong> {$fecha}<br>
                        <strong>Dirección de envío:</strong> {$direccionEnvioEsc}
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio unit.</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$filas}
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="3">Total</td>
                                <td>{$total} €</td>
                            </tr>
                        </tfoot>
                    </table>

                    <p>Gracias por tu compra. Recibirás una notificación cuando tu pedido sea enviado.</p>
                </div>
                <div class="footer">
                    &copy; Clothing Store · Este correo es un comprobante de tu compra.
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function generarFilasTablaHtml(array $carrito, array $productos): string
    {
        $html = '';
        foreach ($productos as $producto) {
            $cantidad  = (int)($carrito[$producto->id] ?? 0);
            $subtotal  = $producto->price * $cantidad;
            $nombre    = htmlspecialchars($producto->name, ENT_QUOTES, 'UTF-8');
            $precio    = number_format($producto->price, 2, ',', '.');
            $subtotalF = number_format($subtotal,        2, ',', '.');

            $html .= <<<HTML
            <tr>
                <td>{$nombre}</td>
                <td>{$precio} €</td>
                <td>{$cantidad}</td>
                <td>{$subtotalF} €</td>
            </tr>
            HTML;
        }
        return $html;
    }

    // ─── Cuerpo texto plano (alternativa) ──────────────────────────────────────

    private function construirCuerpoTexto(
        Order $order,
        array $carrito,
        array $productos,
        string $clienteNombre,
        string $direccionEnvio
    ): string {
        $lineas = [];
        $lineas[] = "CLOTHING STORE – Confirmación de pedido";
        $lineas[] = str_repeat('-', 40);
        $lineas[] = "Hola, {$clienteNombre}.";
        $lineas[] = "Tu pedido #{$order->id} ha sido confirmado el " . date('d/m/Y H:i') . ".";
        $lineas[] = "Dirección de envío: {$direccionEnvio}";
        $lineas[] = "";
        $lineas[] = "RESUMEN DEL PEDIDO";
        $lineas[] = str_repeat('-', 40);

        foreach ($productos as $producto) {
            $cantidad  = (int)($carrito[$producto->id] ?? 0);
            $subtotal  = number_format($producto->price * $cantidad, 2, ',', '.');
            $precio    = number_format($producto->price, 2, ',', '.');
            $lineas[]  = "- {$producto->name} x{$cantidad}  ({$precio} €/ud.)  Subtotal: {$subtotal} €";
        }

        $lineas[] = str_repeat('-', 40);
        $lineas[] = "TOTAL: " . number_format($order->total, 2, ',', '.') . " €";
        $lineas[] = "";
        $lineas[] = "Gracias por tu compra.";

        return implode("\n", $lineas);
    }
}
