<?php

namespace App\Services;

use App\Models\Order;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    /**
     * Genera el PDF de confirmación de pedido y lo devuelve como string binario.
     * No escribe nada en disco; el string se puede adjuntar directamente al correo.
     *
     * @param Order  $order          El pedido confirmado.
     * @param array  $carrito        ['product_id' => cantidad, ...]
     * @param array  $productos      Lista de objetos producto indexada por id.
     * @param string $clienteNombre  Nombre del cliente.
     * @param string $direccionEnvio Dirección de envío.
     *
     * @return string Contenido binario del PDF.
     */
    public function generarPedidoPdf(
        Order $order,
        array $carrito,
        array $productos,
        string $clienteNombre,
        string $direccionEnvio
    ): string {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false); // sin recursos externos por seguridad

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->construirHtml($order, $carrito, $productos, $clienteNombre, $direccionEnvio));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // output(null) devuelve el PDF como string sin enviarlo al navegador
        return $dompdf->output();
    }

    // ─── HTML interno del PDF ──────────────────────────────────────────────────

    private function construirHtml(
        Order $order,
        array $carrito,
        array $productos,
        string $clienteNombre,
        string $direccionEnvio
    ): string {
        $filas             = $this->generarFilas($carrito, $productos);
        $total             = number_format($order->total, 2, ',', '.');
        $fecha             = date('d/m/Y H:i');
        $clienteNombreEsc  = htmlspecialchars($clienteNombre,  ENT_QUOTES, 'UTF-8');
        $direccionEnvioEsc = htmlspecialchars($direccionEnvio, ENT_QUOTES, 'UTF-8');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }

                body {
                    font-family: 'DejaVu Sans', Arial, sans-serif;
                    font-size: 13px;
                    color: #222;
                    background: #fff;
                    padding: 40px;
                }

                /* ── Cabecera ── */
                .header {
                    background: #1a1a1a;
                    color: #fff;
                    padding: 20px 28px;
                    border-radius: 6px 6px 0 0;
                    margin-bottom: 0;
                }
                .header h1 { font-size: 20px; letter-spacing: 1px; }
                .header p  { font-size: 12px; color: #bbb; margin-top: 4px; }

                /* ── Cuerpo ── */
                .body { padding: 28px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 6px 6px; }

                .body p { line-height: 1.6; margin-bottom: 12px; }

                /* ── Info box ── */
                .info-box {
                    background: #f9f9f9;
                    border-left: 4px solid #ff4757;
                    padding: 12px 16px;
                    margin: 16px 0 24px;
                    border-radius: 0 4px 4px 0;
                    line-height: 1.8;
                }

                /* ── Tabla de productos ── */
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                thead th {
                    background: #1a1a1a;
                    color: #fff;
                    padding: 9px 12px;
                    text-align: left;
                    font-size: 12px;
                }
                tbody td {
                    padding: 9px 12px;
                    border-bottom: 1px solid #eee;
                    font-size: 12px;
                }
                tbody tr:last-child td { border-bottom: none; }
                tfoot td {
                    padding: 10px 12px;
                    font-weight: bold;
                    background: #f0f0f0;
                    font-size: 13px;
                }

                /* ── Pie ── */
                .footer {
                    margin-top: 24px;
                    text-align: center;
                    font-size: 11px;
                    color: #999;
                    border-top: 1px solid #eee;
                    padding-top: 14px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Clothing Store</h1>
                <p>Resumen de pedido &ndash; documento generado automaticamente</p>
            </div>

            <div class="body">
                <p>Hola, <strong>{$clienteNombreEsc}</strong>.</p>
                <p>Tu pedido ha sido confirmado correctamente. Aqui tienes el resumen de tu compra:</p>

                <div class="info-box">
                    <strong>N&ordm; de pedido:</strong> #{$order->id}<br>
                    <strong>Fecha:</strong> {$fecha}<br>
                    <strong>Direccion de envio:</strong> {$direccionEnvioEsc}
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
                        <tr>
                            <td colspan="3">TOTAL</td>
                            <td>{$total} &euro;</td>
                        </tr>
                    </tfoot>
                </table>

                <p>Gracias por tu compra. Recibiras una notificacion cuando tu pedido sea enviado.</p>
            </div>

            <div class="footer">
                &copy; Clothing Store &middot; Este documento es un comprobante de tu compra.
            </div>
        </body>
        </html>
        HTML;
    }

    private function generarFilas(array $carrito, array $productos): string
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
                <td>{$precio} &euro;</td>
                <td>{$cantidad}</td>
                <td>{$subtotalF} &euro;</td>
            </tr>
            HTML;
        }
        return $html;
    }
}