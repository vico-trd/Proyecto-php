<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class PayPalService
{
    private string $clientId;
    private string $secret;
    private string $baseUrl;
    private Client $client;

    public function __construct()
    {
        $this->clientId = $_ENV['PAYPAL_CLIENT_ID'] ?? '';
        $this->secret   = $_ENV['PAYPAL_SECRET']    ?? '';
        $mode           = $_ENV['PAYPAL_MODE']       ?? 'sandbox';

        $this->baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 15,
        ]);
    }

    /**
     * Obtiene un access token de la API de PayPal.
     */
    private function getAccessToken(): string
    {
        $response = $this->client->post('/v1/oauth2/token', [
            'auth'        => [$this->clientId, $this->secret],
            'form_params' => ['grant_type' => 'client_credentials'],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (empty($data['access_token'])) {
            throw new \RuntimeException('PayPal: no se pudo obtener el access token.');
        }

        return $data['access_token'];
    }

    /**
     * Crea una orden PayPal y devuelve el ID y la URL de aprobación.
     *
     * @return array{id: string, approvalUrl: string}
     */
    public function crearOrden(float $total, string $returnUrl, string $cancelUrl): array
    {
        $token = $this->getAccessToken();

        $response = $this->client->post('/v2/checkout/orders', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'intent'         => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value'         => number_format($total, 2, '.', ''),
                    ],
                    'description' => 'Pedido CLOTHING STORE',
                ]],
                'application_context' => [
                    'brand_name'          => 'CLOTHING STORE',
                    'locale'              => 'es-ES',
                    'landing_page'        => 'LOGIN',
                    'user_action'         => 'PAY_NOW',
                    'return_url'          => $returnUrl,
                    'cancel_url'          => $cancelUrl,
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $approvalUrl = '';
        foreach ($data['links'] ?? [] as $link) {
            if ($link['rel'] === 'approve') {
                $approvalUrl = $link['href'];
                break;
            }
        }

        if ($approvalUrl === '') {
            throw new \RuntimeException('PayPal: no se obtuvo la URL de aprobación.');
        }

        return [
            'id'          => $data['id'],
            'approvalUrl' => $approvalUrl,
        ];
    }

    /**
     * Captura el pago de una orden PayPal aprobada.
     *
     * @return array Respuesta completa de la captura
     */
    public function capturarOrden(string $paypalOrderId): array
    {
        $token = $this->getAccessToken();

        $response = $this->client->post("/v2/checkout/orders/{$paypalOrderId}/capture", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
