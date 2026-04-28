<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $url;
    protected string $apiKey;

    public function __construct()
    {
        $this->url    = config('services.wapix.url');
        $this->apiKey = config('services.wapix.api_key');
    }

    /**
     * Envía un mensaje de texto libre vía Wapix WhatsApp API.
     *
     * @return array{success: bool, message_id: string|null, error: string|null}
     */
    public function enviar(string $telefono, string $mensaje): array
    {
        if (empty($this->apiKey) || empty($this->url)) {
            return ['success' => false, 'message_id' => null, 'error' => 'Wapix API no configurada (falta API key o URL).'];
        }

        $telefono = $this->normalizarTelefono($telefono);

        try {
            $response = Http::withHeaders([
                'X-API-Key'    => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->url, [
                'to'      => $telefono,
                'content' => $mensaje,
            ]);

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success'    => true,
                    'message_id' => $data['id'] ?? $data['message_id'] ?? null,
                    'error'      => null,
                ];
            }

            $error = $data['error'] ?? $data['message'] ?? $response->body();
            Log::warning('WhatsApp Wapix envío fallido', ['telefono' => $telefono, 'response' => $data]);

            return ['success' => false, 'message_id' => null, 'error' => $error];
        } catch (\Throwable $e) {
            Log::error('WhatsApp Wapix excepción', ['message' => $e->getMessage()]);
            return ['success' => false, 'message_id' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Construye el mensaje, envía y registra en BD.
     */
    public function notificarCita(Cita $cita, string $tipo, ?string $motivoCancelacion = null): Notificacion
    {
        $cita->loadMissing(['paciente', 'medico', 'pago']);

        $telefono = $cita->paciente?->telefono;
        $mensaje  = $this->construirMensaje($cita, $tipo, $motivoCancelacion);

        $resultado = ['success' => false, 'error' => 'Paciente sin teléfono registrado.'];

        if ($telefono) {
            $resultado = $this->enviar($telefono, $mensaje);
        }

        return Notificacion::create([
            'id_cita'            => $cita->id_cita,
            'id_paciente'        => $cita->id_paciente,
            'tipo_notificacion'  => $tipo,
            'canal'              => 'whatsapp',
            'mensaje'            => $mensaje,
            'fecha_envio'        => now(),
            'estado_envio'       => $resultado['success'] ? 'enviado' : 'fallido',
        ]);
    }

    /**
     * Envía recordatorio a todas las citas de hoy que aún no tienen recordatorio enviado.
     */
    public function notificarCitasDelDia(): int
    {
        $citas = Cita::with(['paciente', 'medico'])
            ->where('fecha_cita', today())
            ->whereIn('estado_cita', ['pendiente', 'confirmada'])
            ->whereDoesntHave('notificaciones', function ($q) {
                $q->where('tipo_notificacion', 'recordatorio')
                  ->where('estado_envio', 'enviado');
            })
            ->get();

        $enviados = 0;

        foreach ($citas as $cita) {
            $notificacion = $this->notificarCita($cita, 'recordatorio');
            if ($notificacion->estado_envio === 'enviado') {
                $enviados++;
            }
        }

        return $enviados;
    }

    /**
     * Construye el texto del mensaje según el tipo de notificación.
     */
    protected function construirMensaje(Cita $cita, string $tipo, ?string $motivoCancelacion = null): string
    {
        $clinica = config('app.name', 'Clínica');
        $nombre  = $cita->paciente?->nombre_completo ?? 'Paciente';
        $fecha   = $cita->fecha_cita->translatedFormat('l d \d\e F, Y');
        $hora    = substr($cita->hora_inicio, 0, 5);
        $medico  = $cita->medico ? 'Dr(a). ' . $cita->medico->nombre_completo : 'su médico';
        $codigo  = $cita->codigo_cita;
        $pago    = $cita->esta_pagada ? '✅ Pagada' : '⏳ Pendiente de pago';

        return match ($tipo) {
            'reserva' => "*{$clinica}*\n\n"
                . "Hola *{$nombre}* 👋\n\n"
                . "Su cita médica ha sido registrada exitosamente.\n\n"
                . "*Detalles de la cita:*\n"
                . "Código: *{$codigo}*\n"
                . "Fecha: *{$fecha}*\n"
                . "Hora: *{$hora}*\n"
                . "Médico: *{$medico}*\n"
                . "Pago: {$pago}\n\n"
                . "Por favor, llegue 15 minutos antes de su cita.\n\n"
                . "Le esperamos.",

            'confirmacion' => "*{$clinica}*\n\n"
                . "Hola *{$nombre}* 👋\n\n"
                . "Su cita ha sido *confirmada* exitosamente.\n\n"
                . "*Detalles:*\n"
                . "Código: *{$codigo}*\n"
                . "Fecha: *{$fecha}*\n"
                . "Hora: *{$hora}*\n"
                . "Médico: *{$medico}*\n"
                . "Pago: {$pago}\n\n"
                . "Recuerde llegar 15 minutos antes.\n\n"
                . "Le esperamos.",

            'cancelacion' => "*{$clinica}*\n\n"
                . "Hola *{$nombre}* 👋\n\n"
                . "Su cita ha sido *cancelada*.\n\n"
                . "*Detalles:*\n"
                . "Código: *{$codigo}*\n"
                . "Fecha: *{$fecha}*\n"
                . "Hora: *{$hora}*\n"
                . "Médico: *{$medico}*\n"
                . ($motivoCancelacion ? "Motivo: _{$motivoCancelacion}_\n" : '')
                . "\nSi desea reprogramar, comuníquese con nosotros.\n\n"
                . "Atentamente, *{$clinica}*",

            'reprogramacion' => "*{$clinica}*\n\n"
                . "Hola *{$nombre}* �\n\n"
                . "Su cita ha sido *reprogramada* con los siguientes datos:\n\n"
                . "*Nueva cita:*\n"
                . "Nuevo código: *{$codigo}*\n"
                . "Nueva fecha: *{$fecha}*\n"
                . "Nueva hora: *{$hora}*\n"
                . "Médico: *{$medico}*\n"
                . "Pago: {$pago}\n\n"
                . "Por favor, llegue 15 minutos antes.\n\n"
                . "Le esperamos.",

            'recordatorio' => "*{$clinica}*\n\n"
                . "Hola *{$nombre}* 👋\n\n"
                . "Le recordamos que tiene una cita médica *hoy*.\n\n"
                . "*Detalles:*\n"
                . "Código: *{$codigo}*\n"
                . "Fecha: *{$fecha}*\n"
                . "Hora: *{$hora}*\n"
                . "Médico: *{$medico}*\n"
                . "Pago: {$pago}\n\n"
                . "Recuerde llegar 15 minutos antes de su cita.\n\n"
                . "Le esperamos.",

            default => "*{$clinica}*\n\nHola *{$nombre}* 👋, tiene una notificación sobre su cita *{$codigo}*.",
        };
    }

    /**
     * Normaliza el teléfono a formato internacional sin '+'.
     */
    protected function normalizarTelefono(string $telefono): string
    {
        $telefono = preg_replace('/[^0-9]/', '', $telefono);

        // Si es número boliviano sin código de país (empieza con 6 o 7, 8 dígitos)
        if (preg_match('/^[67]\d{7}$/', $telefono)) {
            $telefono = '591' . $telefono;
        }

        return '+' . $telefono;
    }
}
