<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VeriPagosService
{
    protected string $secretKey;
    protected string $usuario;
    protected string $password;
    protected string $urlGenerarQR;
    protected string $urlVerificarQR;

    public function __construct()
    {
        $this->secretKey     = config('services.veripagos.secret_key', '');
        $this->usuario       = config('services.veripagos.usuario', '');
        $this->password      = config('services.veripagos.password', '');
        $this->urlGenerarQR  = config('services.veripagos.url_generar_qr', 'https://veripagos.com/api/bcp/generar-qr');
        $this->urlVerificarQR = config('services.veripagos.url_verificar_qr', 'https://veripagos.com/api/bcp/verificar-estado-qr');
    }

    /**
     * Genera un QR de cobro en VeriPagos.
     *
     * @return array{success: bool, qr_url?: string, movimiento_id?: string, error?: string}
     */
    public function generarQR(float $monto, string $detalle = 'Pago'): array
    {
        try {
            $response = Http::withBasicAuth($this->usuario, $this->password)
                ->timeout(15)
                ->post($this->urlGenerarQR, [
                    'secret_key' => $this->secretKey,
                    'monto'      => $monto,
                    'vigencia'   => '0/00:15',
                    'uso_unico'  => true,
                    'detalle'    => $detalle,
                ]);

            $result = $response->json();

            if ($result && ($result['Codigo'] ?? -1) === 0) {
                $data = $result['Data'] ?? [];
                return [
                    'success'       => true,
                    'qr_url'        => $data['qr_url'] ?? $data['qr'] ?? null,
                    'movimiento_id' => $data['movimiento_id'] ?? null,
                ];
            }

            Log::warning('VeriPagos generarQR failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'error'   => $result['Mensaje'] ?? 'Error al generar el QR.',
            ];
        } catch (\Exception $e) {
            Log::error('VeriPagos generarQR exception', ['msg' => $e->getMessage()]);
            return [
                'success' => false,
                'error'   => 'Error en el servidor: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verifica el estado de un movimiento/QR.
     *
     * @return array{success: bool, estado?: string, data?: array, error?: string}
     */
    public function verificarEstado(string $movimientoId): array
    {
        try {
            $response = Http::withBasicAuth($this->usuario, $this->password)
                ->timeout(10)
                ->post($this->urlVerificarQR, [
                    'secret_key'    => $this->secretKey,
                    'movimiento_id' => $movimientoId,
                ]);

            $result = $response->json();

            if ($result && ($result['Codigo'] ?? -1) === 0) {
                $data = $result['Data'] ?? [];
                return [
                    'success' => true,
                    'data'    => $data,
                    'estado'  => $data['estado'] ?? $data['Estado'] ?? 'pendiente',
                ];
            }

            return [
                'success' => false,
                'error'   => $result['Mensaje'] ?? 'Error al verificar el estado del QR.',
            ];
        } catch (\Exception $e) {
            Log::error('VeriPagos verificarEstado exception', ['msg' => $e->getMessage()]);
            return [
                'success' => false,
                'error'   => 'Error en el servidor: ' . $e->getMessage(),
            ];
        }
    }
}
