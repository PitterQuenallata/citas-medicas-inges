<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Auditoria;
use App\Services\DisponibilidadService;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CitasController extends Controller
{
    public function __construct(
        protected DisponibilidadService $disponibilidad,
        protected WhatsAppService $whatsapp
    ) {}

    // -------------------------------------------------------------------------
    // INDEX — lista paginada con filtros
    // -------------------------------------------------------------------------
    public function index(Request $request)
    {
        $query = Cita::with(['paciente', 'medico', 'pago'])
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

        $cita = Cita::create([
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

        Auditoria::registrar('crear', 'citas', $cita->id_cita, null, $cita->toArray());

        try {
            $this->whatsapp->notificarCita($cita, 'reserva');
        } catch (\Throwable $e) {
            // No bloquear la acción si falla WhatsApp
        }

        return redirect()->route('citas.show', $cita)
            ->with('success', 'Cita registrada exitosamente.');
    }

    // -------------------------------------------------------------------------
    // SHOW
    // -------------------------------------------------------------------------
    public function show(Cita $cita)
    {
        $cita->load(['paciente', 'medico.especialidades', 'usuarioRegistra', 'citaOriginal', 'reprogramaciones', 'pago']);
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

        $datosAnteriores = $cita->toArray();

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

        Auditoria::registrar('editar', 'citas', $cita->id_cita, $datosAnteriores, $cita->fresh()->toArray());

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

        $datosAnteriores = $cita->toArray();

        $cita->update([
            'estado_cita'       => 'cancelada',
            'fecha_cancelacion' => now(),
            'motivo_cancelacion'=> $request->motivo_cancelacion,
        ]);

        Auditoria::registrar('cancelar', 'citas', $cita->id_cita, $datosAnteriores, $cita->fresh()->toArray());

        try {
            $this->whatsapp->notificarCita($cita, 'cancelacion', $request->motivo_cancelacion);
        } catch (\Throwable $e) {
            // No bloquear la acción si falla WhatsApp
        }

        return redirect()->route('citas.show', $cita)
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

        Auditoria::registrar('reprogramar', 'citas', $cita->id_cita, ['estado_cita' => 'pendiente'], ['estado_cita' => 'reprogramada']);
        Auditoria::registrar('crear', 'citas', $nuevaCita->id_cita, null, $nuevaCita->toArray());

        try {
            $this->whatsapp->notificarCita($nuevaCita, 'reprogramacion');
        } catch (\Throwable $e) {
            // No bloquear la acción si falla WhatsApp
        }

        return redirect()->route('citas.show', $nuevaCita)
            ->with('success', 'Cita reprogramada exitosamente.');
    }

    // -------------------------------------------------------------------------
    // CONFIRMAR
    // -------------------------------------------------------------------------
    public function confirmar(Cita $cita)
    {
        if ($cita->estado_cita !== 'pendiente') {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'Solo se pueden confirmar citas pendientes.');
        }

        $datosAnteriores = $cita->toArray();
        $cita->update(['estado_cita' => 'confirmada']);
        Auditoria::registrar('confirmar', 'citas', $cita->id_cita, $datosAnteriores, $cita->fresh()->toArray());

        try {
            $this->whatsapp->notificarCita($cita, 'confirmacion');
        } catch (\Throwable $e) {
            // No bloquear la acción si falla WhatsApp
        }

        return redirect()->route('citas.show', $cita)
            ->with('success', 'Cita confirmada exitosamente.');
    }

    // -------------------------------------------------------------------------
    // ATENDER
    // -------------------------------------------------------------------------
    public function atender(Cita $cita)
    {
        if ($cita->estado_cita !== 'confirmada') {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'Solo se pueden atender citas confirmadas.');
        }

        if (!$cita->esta_pagada) {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'No se puede atender la cita sin pago confirmado. Registre el pago primero.');
        }

        $datosAnteriores = $cita->toArray();
        $cita->update(['estado_cita' => 'atendida']);
        Auditoria::registrar('atender', 'citas', $cita->id_cita, $datosAnteriores, $cita->fresh()->toArray());

        return redirect()->route('citas.show', $cita)
            ->with('success', 'Cita marcada como atendida.');
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
    // CALENDARIO — vista mensual con FullCalendar
    // -------------------------------------------------------------------------
    public function calendario()
    {
        return view('citas.calendario');
    }

    // -------------------------------------------------------------------------
    // API — eventos para FullCalendar (AJAX)
    // -------------------------------------------------------------------------
    public function calendarEvents(Request $request)
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end'   => ['required', 'date'],
        ]);

        $citas = Cita::with(['paciente', 'medico'])
            ->whereBetween('fecha_cita', [$request->start, $request->end])
            ->orderBy('hora_inicio')
            ->get();

        $colores = [
            'pendiente'    => '#f59e0b',
            'confirmada'   => '#0ea5e9',
            'atendida'     => '#22c55e',
            'cancelada'    => '#ef4444',
            'reprogramada' => '#94a3b8',
            'no_asistio'   => '#f97316',
        ];

        $eventos = $citas->map(function ($cita) use ($colores) {
            $horaInicio = $cita->fecha_cita->format('Y-m-d') . 'T' . $cita->hora_inicio;
            $horaFin    = $cita->fecha_cita->format('Y-m-d') . 'T' . $cita->hora_fin;

            return [
                'id'              => $cita->id_cita,
                'title'           => ($cita->paciente?->nombres ?? '') . ' ' . ($cita->paciente?->apellidos ?? ''),
                'start'           => $horaInicio,
                'end'             => $horaFin,
                'color'           => $colores[$cita->estado_cita] ?? '#94a3b8',
                'extendedProps'   => [
                    'codigo'       => $cita->codigo_cita,
                    'paciente'     => ($cita->paciente?->nombres ?? '') . ' ' . ($cita->paciente?->apellidos ?? ''),
                    'medico'       => 'Dr. ' . ($cita->medico?->nombres ?? '') . ' ' . ($cita->medico?->apellidos ?? ''),
                    'hora_inicio'  => substr($cita->hora_inicio, 0, 5),
                    'hora_fin'     => substr($cita->hora_fin, 0, 5),
                    'motivo'       => $cita->motivo_consulta ?? '—',
                    'estado'       => $cita->estado_cita,
                    'estado_label' => $cita->estado_label,
                    'url_show'     => route('citas.show', $cita->id_cita),
                    'url_edit'     => route('citas.edit', $cita->id_cita),
                ],
            ];
        });

        return response()->json($eventos);
    }

    // -------------------------------------------------------------------------
    // Agenda médica
    // -------------------------------------------------------------------------
    public function agenda(Request $request)
    {
        $medicos  = Medico::where('estado', 'activo')->orderBy('apellidos')->get();
        $fecha    = $request->input('fecha', today()->format('Y-m-d'));
        $medicoId = $request->input('medico_id');

        $query = Cita::with(['paciente', 'medico.especialidades'])
            ->where('fecha_cita', $fecha)
            ->orderBy('hora_inicio');

        if ($medicoId) {
            $query->where('id_medico', $medicoId);
        }

        $citas    = $query->get();
        $horarios = collect();

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

    // -------------------------------------------------------------------------
    // TICKET PDF — genera ticket de cita inline en el navegador
    // -------------------------------------------------------------------------
    public function ticket(Cita $cita)
    {
        $cita->load(['paciente', 'medico.especialidades', 'pago']);

        $pdf = Pdf::loadView('pdf.ticket-cita', compact('cita'))
            ->setPaper([0, 0, 396, 612]);

        return $pdf->stream("ticket-{$cita->codigo_cita}.pdf");
    }
}
