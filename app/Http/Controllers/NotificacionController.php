<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Notificacion;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function __construct(protected WhatsAppService $whatsapp) {}

    public function index(Request $request)
    {
        $query = Notificacion::with(['cita', 'paciente'])
            ->orderBy('fecha_envio', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado_envio', $request->estado);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo_notificacion', $request->tipo);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_envio', $request->fecha);
        }

        $notificaciones = $query->paginate(20)->withQueryString();

        $conteos = [
            'enviadas'   => Notificacion::where('estado_envio', 'enviado')->count(),
            'fallidas'   => Notificacion::where('estado_envio', 'fallido')->count(),
            'pendientes' => Notificacion::where('estado_envio', 'pendiente')->count(),
        ];

        $citasHoy = Cita::where('fecha_cita', today())
            ->whereIn('estado_cita', ['pendiente', 'confirmada'])
            ->whereDoesntHave('notificaciones', function ($q) {
                $q->where('tipo_notificacion', 'recordatorio')
                  ->where('estado_envio', 'enviado');
            })
            ->count();

        return view('notificaciones.index', compact('notificaciones', 'conteos', 'citasHoy'));
    }

    public function enviar(Cita $cita)
    {
        if (!$cita->paciente?->telefono) {
            return back()->with('error', 'El paciente no tiene teléfono registrado.');
        }

        try {
            $notificacion = $this->whatsapp->notificarCita($cita, 'recordatorio');

            if ($notificacion->estado_envio === 'enviado') {
                return back()->with('success', 'Notificación WhatsApp enviada exitosamente.');
            }

            return back()->with('error', 'No se pudo enviar la notificación. Se registró como fallida.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al enviar notificación: ' . $e->getMessage());
        }
    }

    public function enviarHoy()
    {
        try {
            $enviados = $this->whatsapp->notificarCitasDelDia();
            return back()->with('success', "Se enviaron {$enviados} recordatorio(s) para las citas de hoy.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al enviar recordatorios: ' . $e->getMessage());
        }
    }
}
