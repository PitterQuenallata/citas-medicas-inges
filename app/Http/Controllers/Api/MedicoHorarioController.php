<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medico;
use Illuminate\Http\JsonResponse;

/**
 * Endpoint para que el módulo de Citas (Pitter) consulte
 * los horarios de disponibilidad de un médico.
 *
 * GET /api/medicos/{medico}/horarios
 */
class MedicoHorarioController extends Controller
{
    public function __invoke(Medico $medico): JsonResponse
    {
        $horarios = $medico->horariosActivos()
                           ->get()
                           ->map(fn($h) => [
                               'id_horario'            => $h->id_horario,
                               'dia_semana'            => $h->dia_semana,
                               'nombre_dia'            => $h->nombre_dia,
                               'hora_inicio'           => $h->hora_inicio,
                               'hora_fin'              => $h->hora_fin,
                               'duracion_cita_minutos' => $h->duracion_cita_minutos,
                               'activo'                => $h->activo,
                           ]);

        return response()->json([
            'medico' => [
                'id_medico'      => $medico->id_medico,
                'nombre_completo'=> $medico->nombre_completo,
                'codigo_medico'  => $medico->codigo_medico,
                'estado'         => $medico->estado,
            ],
            'horarios' => $horarios,
            // Bloqueos vigentes (útil para que Citas valide disponibilidad real)
            'bloqueos' => $medico->bloqueos()
                                 ->where('fecha', '>=', now()->toDateString())
                                 ->orderBy('fecha')
                                 ->get(['id_bloqueo', 'fecha', 'hora_inicio', 'hora_fin', 'motivo']),
        ], 200);
    }
}
