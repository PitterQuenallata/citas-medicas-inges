<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->input('tipo', 'citas');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $estado = $request->input('estado');
        $idMedico = $request->input('id_medico');
        $idEspecialidad = $request->input('id_especialidad');

        $citas = null;
        $totales = [];
        $medicos = collect();
        $especialidades = collect();
        $pacientes = null;
        $pacientesTotales = [];
        $topPacientes = collect();
        $reporteMedicos = collect();

        if ($tipo === 'citas') {
            $query = Cita::query()->with(['paciente', 'medico', 'medico.especialidades']);

            if ($desde) {
                $query->whereDate('fecha_cita', '>=', $desde);
            }
            if ($hasta) {
                $query->whereDate('fecha_cita', '<=', $hasta);
            }
            if ($estado) {
                $query->where('estado_cita', $estado);
            }
            if ($idMedico) {
                $query->where('id_medico', $idMedico);
            }
            if ($idEspecialidad) {
                $query->whereHas('medico.especialidades', function ($q) use ($idEspecialidad) {
                    $q->where('especialidades.id_especialidad', $idEspecialidad);
                });
            }

            $citas = (clone $query)
                ->orderByDesc('fecha_cita')
                ->orderByDesc('hora_inicio')
                ->paginate(10)
                ->withQueryString();

            $totales = (clone $query)
                ->selectRaw('estado_cita, COUNT(*) as total')
                ->groupBy('estado_cita')
                ->pluck('total', 'estado_cita')
                ->toArray();

            $medicos = Medico::query()->orderBy('nombres')->orderBy('apellidos')->get();
            $especialidades = Especialidad::query()->orderBy('nombre')->get();
        }

        if ($tipo === 'pacientes') {
            $queryPac = Paciente::query();

            if ($desde) {
                $queryPac->whereDate('created_at', '>=', $desde);
            }
            if ($hasta) {
                $queryPac->whereDate('created_at', '<=', $hasta);
            }

            $pacientes = (clone $queryPac)
                ->orderByDesc('id_paciente')
                ->paginate(10)
                ->withQueryString();

            $pacientesTotales = (clone $queryPac)
                ->selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado')
                ->toArray();

            $topPacientes = Cita::query()
                ->selectRaw('id_paciente, COUNT(*) as total')
                ->when($desde, fn ($q) => $q->whereDate('fecha_cita', '>=', $desde))
                ->when($hasta, fn ($q) => $q->whereDate('fecha_cita', '<=', $hasta))
                ->groupBy('id_paciente')
                ->orderByDesc('total')
                ->with('paciente')
                ->limit(10)
                ->get();
        }

        if ($tipo === 'medicos') {
            $reporteMedicos = Cita::query()
                ->selectRaw('id_medico, COUNT(*) as total')
                ->when($desde, fn ($q) => $q->whereDate('fecha_cita', '>=', $desde))
                ->when($hasta, fn ($q) => $q->whereDate('fecha_cita', '<=', $hasta))
                ->when($estado, fn ($q) => $q->where('estado_cita', $estado))
                ->groupBy('id_medico')
                ->orderByDesc('total')
                ->with('medico')
                ->get();
        }

        return view('reportes.index', compact(
            'tipo',
            'citas',
            'totales',
            'medicos',
            'especialidades',
            'pacientes',
            'pacientesTotales',
            'topPacientes',
            'reporteMedicos',
            'desde',
            'hasta',
            'estado',
            'idMedico',
            'idEspecialidad'
        ));
    }
}
