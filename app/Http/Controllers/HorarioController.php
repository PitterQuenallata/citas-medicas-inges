<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHorarioRequest;
use App\Http\Requests\UpdateHorarioRequest;
use App\Models\HorarioMedico;
use App\Models\Medico;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $horarios = HorarioMedico::with('medico')
            ->when($buscar, function ($q) use ($buscar) {
                $q->whereHas('medico', function ($m) use ($buscar) {
                    $m->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('codigo_medico', 'like', "%{$buscar}%");
                });
            })
            // REFACTOR: ordenar por médico, luego día y hora para lectura más natural
            ->orderBy('id_medico')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->paginate(10)
            ->withQueryString();

        return view('horarios.index', compact('horarios'));
    }

    public function create()
    {
        // REFACTOR: solo médicos activos disponibles para asignar horarios
        $medicos = Medico::where('estado', 'activo')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
        $dias = HorarioMedico::DIAS;

        return view('horarios.create', compact('medicos', 'dias'));
    }

    // REFACTOR: validación movida a StoreHorarioRequest
    public function store(StoreHorarioRequest $request)
    {
        $validated = $request->validated();

        // Validar solapamiento de horarios para el mismo médico y día
        if ($this->existeSolapamiento((int) $validated['id_medico'], (int) $validated['dia_semana'], $validated['hora_inicio'], $validated['hora_fin'])) {
            return back()->withInput()->withErrors([
                'hora_inicio' => 'Ya existe un horario que se solapa con este intervalo para el mismo médico y día.',
            ]);
        }

        HorarioMedico::create([
            'id_medico'             => $validated['id_medico'],
            'dia_semana'            => $validated['dia_semana'],
            'hora_inicio'           => $validated['hora_inicio'],
            'hora_fin'              => $validated['hora_fin'],
            'duracion_cita_minutos' => $validated['duracion_cita_minutos'],
            'activo'                => $request->boolean('activo', true),
        ]);

        return redirect()->route('horarios.index')
            ->with('success', 'Horario creado correctamente.');
    }

    public function edit(HorarioMedico $horario)
    {
        // REFACTOR: solo médicos activos (incluye el actual aunque esté inactivo)
        $medicos = Medico::where(function ($q) use ($horario) {
                $q->where('estado', 'activo')
                  ->orWhere('id_medico', $horario->id_medico);
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
        $dias = HorarioMedico::DIAS;

        return view('horarios.edit', compact('horario', 'medicos', 'dias'));
    }

    // REFACTOR: validación movida a UpdateHorarioRequest
    public function update(UpdateHorarioRequest $request, HorarioMedico $horario)
    {
        $validated = $request->validated();

        // Validar solapamiento excluyendo el horario actual
        if ($this->existeSolapamiento((int) $validated['id_medico'], (int) $validated['dia_semana'], $validated['hora_inicio'], $validated['hora_fin'], $horario->id_horario)) {
            return back()->withInput()->withErrors([
                'hora_inicio' => 'Ya existe un horario que se solapa con este intervalo para el mismo médico y día.',
            ]);
        }

        $horario->update([
            'id_medico'             => $validated['id_medico'],
            'dia_semana'            => $validated['dia_semana'],
            'hora_inicio'           => $validated['hora_inicio'],
            'hora_fin'              => $validated['hora_fin'],
            'duracion_cita_minutos' => $validated['duracion_cita_minutos'],
            'activo'                => $request->boolean('activo'),
        ]);

        return redirect()->route('horarios.index')
            ->with('success', 'Horario actualizado correctamente.');
    }

    // NUEVO: eliminar horario
    public function destroy(HorarioMedico $horario)
    {
        $horario->delete();

        return redirect()->route('horarios.index')
            ->with('success', 'Horario eliminado correctamente.');
    }

    /**
     * NUEVO: verifica si existe solapamiento de horarios para un médico/día.
     * Normaliza a 'HH:MM:SS' para comparación correcta contra valores SQLite.
     */
    private function existeSolapamiento(int $idMedico, int $diaSemana, string $horaInicio, string $horaFin, ?int $excluirId = null): bool
    {
        // Normalizar a HH:MM:SS para evitar falsos positivos en comparación de strings
        $inicio = strlen($horaInicio) === 5 ? $horaInicio . ':00' : $horaInicio;
        $fin    = strlen($horaFin)    === 5 ? $horaFin    . ':00' : $horaFin;

        return HorarioMedico::where('id_medico', $idMedico)
            ->where('dia_semana', $diaSemana)
            ->when($excluirId, fn ($q) => $q->where('id_horario', '!=', $excluirId))
            ->where(function ($q) use ($inicio, $fin) {
                // Dos intervalos [a,b) y [c,d) se solapan si a < d && b > c
                $q->where('hora_inicio', '<', $fin)
                  ->where('hora_fin', '>', $inicio);
            })
            ->exists();
    }
}
