<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\HorarioMedico;
use App\Models\BloqueoMedico;
use Carbon\Carbon;

class DisponibilidadService
{
    /**
     * Verifica las tres reglas y retorna array de errores (vacío = disponible).
     */
    public function verificar(
        int $medicoId,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        ?int $excluirCitaId = null
    ): array {
        $errores = [];

        $errorHorario = $this->verificarHorarioMedico($medicoId, $fecha, $horaInicio, $horaFin);
        if ($errorHorario) {
            $errores[] = $errorHorario;
        }

        $errorBloqueo = $this->verificarBloqueos($medicoId, $fecha, $horaInicio, $horaFin);
        if ($errorBloqueo) {
            $errores[] = $errorBloqueo;
        }

        $errorConflicto = $this->verificarConflictos($medicoId, $fecha, $horaInicio, $horaFin, $excluirCitaId);
        if ($errorConflicto) {
            $errores[] = $errorConflicto;
        }

        return $errores;
    }

    /**
     * Verifica que la cita caiga dentro del horario semanal del médico.
     */
    public function verificarHorarioMedico(
        int $medicoId,
        string $fecha,
        string $horaInicio,
        string $horaFin
    ): ?string {
        $diaSemana = Carbon::parse($fecha)->dayOfWeekIso; // 1=Lunes … 7=Domingo

        $horarios = HorarioMedico::where('id_medico', $medicoId)
            ->where('dia_semana', $diaSemana)
            ->where('activo', true)
            ->get();

        if ($horarios->isEmpty()) {
            $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            return "El médico no tiene horario configurado para {$dias[$diaSemana]}.";
        }

        $horaInicioCarbon = Carbon::createFromFormat('H:i', substr($horaInicio, 0, 5));
        $horaFinCarbon    = Carbon::createFromFormat('H:i', substr($horaFin, 0, 5));

        foreach ($horarios as $horario) {
            $inicioHorario = Carbon::createFromFormat('H:i:s', $horario->hora_inicio);
            $finHorario    = Carbon::createFromFormat('H:i:s', $horario->hora_fin);

            if ($horaInicioCarbon->greaterThanOrEqualTo($inicioHorario)
                && $horaFinCarbon->lessThanOrEqualTo($finHorario)) {
                return null;
            }
        }

        return "La hora seleccionada está fuera del horario de atención del médico.";
    }

    /**
     * Verifica que el médico no tenga bloqueo en esa fecha/hora.
     */
    public function verificarBloqueos(
        int $medicoId,
        string $fecha,
        string $horaInicio,
        string $horaFin
    ): ?string {
        $bloqueos = BloqueoMedico::where('id_medico', $medicoId)
            ->where('fecha', $fecha)
            ->get();

        foreach ($bloqueos as $bloqueo) {
            // Bloqueo de día completo
            if (is_null($bloqueo->hora_inicio) || is_null($bloqueo->hora_fin)) {
                return "El médico tiene bloqueada su agenda el día {$fecha}" .
                       ($bloqueo->motivo ? " ({$bloqueo->motivo})" : '') . '.';
            }

            // Solapamiento con bloqueo parcial
            if ($horaInicio < $bloqueo->hora_fin && $horaFin > $bloqueo->hora_inicio) {
                return "El médico tiene su agenda bloqueada entre {$bloqueo->hora_inicio} y {$bloqueo->hora_fin}" .
                       ($bloqueo->motivo ? " ({$bloqueo->motivo})" : '') . '.';
            }
        }

        return null;
    }

    /**
     * Verifica que no haya otra cita activa del mismo médico que se solape.
     */
    public function verificarConflictos(
        int $medicoId,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        ?int $excluirCitaId = null
    ): ?string {
        $query = Cita::where('id_medico', $medicoId)
            ->where('fecha_cita', $fecha)
            ->whereIn('estado_cita', ['pendiente', 'confirmada'])
            ->where('hora_inicio', '<', $horaFin)
            ->where('hora_fin', '>', $horaInicio);

        if ($excluirCitaId) {
            $query->where('id_cita', '!=', $excluirCitaId);
        }

        $conflicto = $query->with('paciente')->first();

        if ($conflicto) {
            $nombrePaciente = $conflicto->paciente?->nombre_completo ?? 'otro paciente';
            return "El médico ya tiene una cita con {$nombrePaciente} entre {$conflicto->hora_inicio} y {$conflicto->hora_fin}.";
        }

        return null;
    }

    /**
     * Genera todos los slots del día para un médico según su horario.
     * Cada slot indica si está disponible o ya está ocupado.
     *
     * @return array<int, array{hora_inicio:string, hora_fin:string, disponible:bool}>
     */
    public function generarSlots(int $medicoId, string $fecha, ?int $excluirCitaId = null): array
    {
        $diaSemana = Carbon::parse($fecha)->dayOfWeekIso;

        $horarios = HorarioMedico::where('id_medico', $medicoId)
            ->where('dia_semana', $diaSemana)
            ->where('activo', true)
            ->get();

        if ($horarios->isEmpty()) {
            return [];
        }

        $citasQuery = Cita::where('id_medico', $medicoId)
            ->where('fecha_cita', $fecha)
            ->whereIn('estado_cita', ['pendiente', 'confirmada']);

        if ($excluirCitaId) {
            $citasQuery->where('id_cita', '!=', $excluirCitaId);
        }

        $citas    = $citasQuery->get();
        $bloqueos = BloqueoMedico::where('id_medico', $medicoId)
            ->where('fecha', $fecha)
            ->get();

        $slots = [];

        foreach ($horarios as $horario) {
            $cursor   = Carbon::createFromFormat('H:i:s', $horario->hora_inicio);
            $fin      = Carbon::createFromFormat('H:i:s', $horario->hora_fin);
            $duracion = (int) $horario->duracion_cita_minutos;

            while ($cursor->copy()->addMinutes($duracion)->lessThanOrEqualTo($fin)) {
                $slotIni    = $cursor->format('H:i');
                $slotFinObj = $cursor->copy()->addMinutes($duracion);
                $slotFin    = $slotFinObj->format('H:i');
                $iniStr     = $slotIni . ':00';
                $finStr     = $slotFin . ':00';

                $ocupado = false;

                foreach ($citas as $cita) {
                    if ($iniStr < $cita->hora_fin && $finStr > $cita->hora_inicio) {
                        $ocupado = true;
                        break;
                    }
                }

                if (!$ocupado) {
                    foreach ($bloqueos as $bloqueo) {
                        if (is_null($bloqueo->hora_inicio) || is_null($bloqueo->hora_fin)) {
                            $ocupado = true;
                            break;
                        }
                        if ($iniStr < $bloqueo->hora_fin && $finStr > $bloqueo->hora_inicio) {
                            $ocupado = true;
                            break;
                        }
                    }
                }

                $slots[] = [
                    'hora_inicio' => $slotIni,
                    'hora_fin'    => $slotFin,
                    'disponible'  => !$ocupado,
                ];

                $cursor->addMinutes($duracion);
            }
        }

        return $slots;
    }
}
