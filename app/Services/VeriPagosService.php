<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VeriPagosService
{
    protected string $baseUrl;
    protected string $apiToken;
    protected string $comercioId;

    public function __construct()
    {
        $this->baseUrl    = rtrim(config('services.veripagos.base_url', 'https://api.veripagos.com/v1'), '/');
        $this->apiToken   = config('services.veripagos.api_token', '');
        $this->comercioId = config('services.veripagos.comercio_id', '');
    }

    /**
     * Genera un QR de cobro en VeriPagos.
     *
     * @return array{success: bool, qr_url?: string, movimiento_id?: string, error?: string}
     */
    public function generarQR(float $monto, string $concepto, string $referencia): array
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->timeout(15)
                ->post("{$this->baseUrl}/qr/generar", [
                    'comercio_id' => $this->comercioId,
                    'monto'       => $monto,
                    'moneda'      => 'BOB',
                    'concepto'    => $concepto,
                    'referencia'  => $referencia,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success'        => true,
                    'qr_url'         => $data['qr_image_url'] ?? $data['qr_url'] ?? null,
                    'movimiento_id'  => $data['movimiento_id'] ?? $data['id'] ?? null,
                    'qr_data'        => $data,
                ];
            }

            Log::warning('VeriPagos generarQR failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'error'   => $response->json('message', 'Error al generar QR'),
            ];
        } catch (\Exception $e) {
            Log::error('VeriPagos generarQR exception', ['msg' => $e->getMessage()]);
            return [
                'success' => false,
                'error'   => 'No se pudo conectar con VeriPagos: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verifica el estado de un movimiento/QR.
     *
     * @return array{success: bool, estado?: string, datos_remitente?: array, error?: string}
     */
    public function verificarEstado(string $movimientoId): array
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->timeout(10)
                ->get("{$this->baseUrl}/qr/verificar/{$movimientoId}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success'         => true,
                    'estado'          => $data['estado'] ?? 'pendiente',
                    'datos_remitente' => $data['remitente'] ?? $data['datos_remitente'] ?? null,
                    'data'            => $data,
                ];
            }

            return [
                'success' => false,
                'error'   => $response->json('message', 'Error al verificar estado'),
            ];
        } catch (\Exception $e) {
            Log::error('VeriPagos verificarEstado exception', ['msg' => $e->getMessage()]);
            return [
                'success' => false,
                'error'   => 'No se pudo conectar con VeriPagos.',
            ];
        }
    }
}
