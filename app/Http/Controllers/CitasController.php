<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Services\DisponibilidadService;
use Illuminate\Http\Request;

class CitasController extends Controller
{
    public function __construct(protected DisponibilidadService $disponibilidad) {}

    // -------------------------------------------------------------------------
    // INDEX — lista paginada con filtros
    // -------------------------------------------------------------------------
    public function index(Request $request)
    {
        $query = Cita::with(['paciente', 'medico'])
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_inicio', 'asc');

        if ($request->filled('estado')) {
            $query->where('estado_cita', $request->estado);
        }
        if ($request->filled('especialidad')) {
            $query->whereHas('medico.especialidades', function ($q) use ($request) {
                $q->where('especialidades.id_especialidad', $request->especialidad);
            });
        }
        if ($request->filled('medico')) {
            $query->where('id_medico', $request->medico);
        }
        if ($request->filled('fecha')) {
            $query->where('fecha_cita', $request->fecha);
        }
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('paciente', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                  ->orWhere('apellidos', 'like', "%{$buscar}%")
                  ->orWhere('ci', 'like', "%{$buscar}%");
            });
        }

        $citas          = $query->paginate(15)->withQueryString();
        $medicos        = Medico::where('estado', 'activo')->orderBy('apellidos')->get();
        $especialidades = Especialidad::where('estado', 'activo')->orderBy('nombre_especialidad')->get();

        return view('citas.index', compact('citas', 'medicos', 'especialidades'));
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------
    public function create()
    {
        $pacientes     = Paciente::where('estado', 'activo')
            ->orderBy('apellidos')->orderBy('nombres')->get();
        $especialidades = Especialidad::where('estado', 'activo')
            ->orderBy('nombre_especialidad')->get();

        return view('citas.create', compact('pacientes', 'especialidades'));
    }

    // -------------------------------------------------------------------------
    // STORE
    // -------------------------------------------------------------------------
    public function store(StoreCitaRequest $request)
    {
        $horaInicio = $request->hora_inicio . ':00';
        $horaFin    = $request->hora_fin    . ':00';

        $errores = $this->disponibilidad->verificar(
            (int) $request->id_medico,
            $request->fecha_cita,
            $horaInicio,
            $horaFin
        );

        if (!empty($errores)) {
            return back()->withInput()->withErrors(['disponibilidad' => $errores]);
        }

        Cita::create([
            'codigo_cita'        => Cita::generarCodigo(),
            'id_paciente'        => $request->id_paciente,
            'id_medico'          => $request->id_medico,
            'id_usuario_registra'=> auth()->id() ?? 1,
            'fecha_cita'         => $request->fecha_cita,
            'hora_inicio'        => $horaInicio,
            'hora_fin'           => $horaFin,
            'motivo_consulta'    => $request->motivo_consulta,
            'observaciones'      => $request->observaciones,
            'estado_cita'        => 'pendiente',
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Cita registrada exitosamente.');
    }

    // -------------------------------------------------------------------------
    // SHOW
    // -------------------------------------------------------------------------
    public function show(Cita $cita)
    {
        $cita->load(['paciente', 'medico.especialidades', 'usuarioRegistra', 'citaOriginal']);
        return view('citas.show', compact('cita'));
    }

    // -------------------------------------------------------------------------
    // EDIT
    // -------------------------------------------------------------------------
    public function edit(Cita $cita)
    {
        if (in_array($cita->estado_cita, ['cancelada', 'atendida', 'reprogramada'])) {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'No se puede editar una cita en estado ' . $cita->estado_label . '.');
        }

        $cita->load('medico.especialidades');
        $pacientes      = Paciente::where('estado', 'activo')
            ->orderBy('apellidos')->orderBy('nombres')->get();
        $especialidades = Especialidad::where('estado', 'activo')
            ->orderBy('nombre_especialidad')->get();

        return view('citas.edit', compact('cita', 'pacientes', 'especialidades'));
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------
    public function update(UpdateCitaRequest $request, Cita $cita)
    {
        if (in_array($cita->estado_cita, ['cancelada', 'atendida', 'reprogramada'])) {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'No se puede editar una cita en estado ' . $cita->estado_label . '.');
        }

        $horaInicio = $request->hora_inicio . ':00';
        $horaFin    = $request->hora_fin    . ':00';

        $errores = $this->disponibilidad->verificar(
            (int) $request->id_medico,
            $request->fecha_cita,
            $horaInicio,
            $horaFin,
            $cita->id_cita
        );

        if (!empty($errores)) {
            return back()->withInput()->withErrors(['disponibilidad' => $errores]);
        }

        $cita->update([
            'id_paciente'     => $request->id_paciente,
            'id_medico'       => $request->id_medico,
            'fecha_cita'      => $request->fecha_cita,
            'hora_inicio'     => $horaInicio,
            'hora_fin'        => $horaFin,
            'motivo_consulta' => $request->motivo_consulta,
            'observaciones'   => $request->observaciones,
            'estado_cita'     => $request->estado_cita ?? $cita->estado_cita,
        ]);

        return redirect()->route('citas.show', $cita)
            ->with('success', 'Cita actualizada exitosamente.');
    }

    // -------------------------------------------------------------------------
    // CANCELAR
    // -------------------------------------------------------------------------
    public function cancelar(Request $request, Cita $cita)
    {
        if (in_array($cita->estado_cita, ['cancelada', 'atendida'])) {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'Esta cita no puede ser cancelada.');
        }

        $request->validate([
            'motivo_cancelacion' => ['required', 'string', 'max:255'],
        ], [
            'motivo_cancelacion.required' => 'Debe indicar el motivo de cancelación.',
        ]);

        $cita->update([
            'estado_cita'       => 'cancelada',
            'fecha_cancelacion' => now(),
            'motivo_cancelacion'=> $request->motivo_cancelacion,
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Cita cancelada correctamente.');
    }

    // -------------------------------------------------------------------------
    // REPROGRAMAR — formulario
    // -------------------------------------------------------------------------
    public function showReprogramar(Cita $cita)
    {
        if (in_array($cita->estado_cita, ['cancelada', 'atendida', 'reprogramada'])) {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'Esta cita no puede ser reprogramada.');
        }

        $cita->load(['paciente', 'medico']);
        return view('citas.reprogramar', compact('cita'));
    }

    // -------------------------------------------------------------------------
    // REPROGRAMAR — guardar
    // -------------------------------------------------------------------------
    public function storeReprogramar(Request $request, Cita $cita)
    {
        if (in_array($cita->estado_cita, ['cancelada', 'atendida', 'reprogramada'])) {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'Esta cita no puede ser reprogramada.');
        }

        $request->validate([
            'fecha_cita'  => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin'    => ['required', 'date_format:H:i', 'after:hora_inicio'],
        ], [
            'fecha_cita.required'          => 'La nueva fecha es obligatoria.',
            'fecha_cita.after_or_equal'    => 'La nueva fecha no puede ser anterior a hoy.',
            'hora_inicio.required'         => 'La hora de inicio es obligatoria.',
            'hora_fin.required'            => 'La hora de fin es obligatoria.',
            'hora_fin.after'               => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        $horaInicio = $request->hora_inicio . ':00';
        $horaFin    = $request->hora_fin    . ':00';

        $errores = $this->disponibilidad->verificar(
            $cita->id_medico,
            $request->fecha_cita,
            $horaInicio,
            $horaFin
        );

        if (!empty($errores)) {
            return back()->withInput()->withErrors(['disponibilidad' => $errores]);
        }

        $nuevaCita = Cita::create([
            'codigo_cita'                => Cita::generarCodigo(),
            'id_paciente'                => $cita->id_paciente,
            'id_medico'                  => $cita->id_medico,
            'id_usuario_registra'        => auth()->id() ?? 1,
            'fecha_cita'                 => $request->fecha_cita,
            'hora_inicio'                => $horaInicio,
            'hora_fin'                   => $horaFin,
            'motivo_consulta'            => $cita->motivo_consulta,
            'estado_cita'                => 'pendiente',
            'id_cita_reprogramada_desde' => $cita->id_cita,
        ]);

        $cita->update(['estado_cita' => 'reprogramada']);

        return redirect()->route('citas.show', $nuevaCita)
            ->with('success', 'Cita reprogramada exitosamente.');
    }

    // -------------------------------------------------------------------------
    // API — médicos filtrados por especialidad (AJAX)
    // -------------------------------------------------------------------------
    public function medicosPorEspecialidad(Especialidad $especialidad)
    {
        $medicos = $especialidad->medicos()
            ->where('medicos.estado', 'activo')
            ->orderBy('medicos.apellidos')
            ->get(['medicos.id_medico', 'medicos.nombres', 'medicos.apellidos']);

        return response()->json($medicos->map(fn($m) => [
            'id'     => $m->id_medico,
            'nombre' => 'Dr(a). ' . $m->apellidos . ', ' . $m->nombres,
        ]));
    }

    // -------------------------------------------------------------------------
    // API — slots disponibles del médico en una fecha (AJAX)
    // -------------------------------------------------------------------------
    public function slots(Request $request, Medico $medico)
    {
        $request->validate(['fecha' => ['required', 'date']]);

        $excluir = $request->input('excluir_cita_id');

        $slots = $this->disponibilidad->generarSlots(
            $medico->id_medico,
            $request->fecha,
            $excluir ? (int) $excluir : null
        );

        return response()->json($slots);
    }

    // -------------------------------------------------------------------------
    // Agenda médica
    // -------------------------------------------------------------------------
    public function agenda(Request $request)
    {
        $medicos  = Medico::where('estado', 'activo')->orderBy('apellidos')->get();
        $fecha    = $request->input('fecha', today()->format('Y-m-d'));
        $medicoId = $request->input('id_medico');

        $citas = collect();
        $horarios = collect();

        if ($medicoId) {
            $citas = Cita::with('paciente')
                ->where('id_medico', $medicoId)
                ->where('fecha_cita', $fecha)
                ->orderBy('hora_inicio')
                ->get();

            $diaSemana = \Carbon\Carbon::parse($fecha)->dayOfWeekIso;
            $horarios  = \App\Models\HorarioMedico::where('id_medico', $medicoId)
                ->where('dia_semana', $diaSemana)
                ->where('activo', true)
                ->get();
        }

        return view('agenda.index', compact('medicos', 'fecha', 'medicoId', 'citas', 'horarios'));
    }

    // -------------------------------------------------------------------------
    // API — disponibilidad del médico (AJAX)
    // -------------------------------------------------------------------------
    public function disponibilidad(Request $request, Medico $medico)
    {
        $request->validate([
            'fecha'       => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin'    => ['required', 'date_format:H:i'],
        ]);

        $horaInicio = $request->hora_inicio . ':00';
        $horaFin    = $request->hora_fin    . ':00';
        $excluir    = $request->input('excluir_cita_id');

        $errores = $this->disponibilidad->verificar(
            $medico->id_medico,
            $request->fecha,
            $horaInicio,
            $horaFin,
            $excluir ? (int) $excluir : null
        );

        return response()->json([
            'disponible' => empty($errores),
            'errores'    => $errores,
        ]);
    }
}
