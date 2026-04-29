<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\HorarioMedico;
use App\Models\Medico;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->esMedico() && !$user->esSuperAdmin()) {
            return $this->dashboardDoctor($user);
        }

        return view('dashboard');
    }

    public function analytics()
    {
        $user = auth()->user();

        if (!$user->esMedico() && !$user->esSuperAdmin()) {
            return redirect()->route('dashboard');
        }

        $medico = $user->medicoProfile();
        if (!$medico) {
            return redirect()->route('dashboard');
        }

        $mesesLabels = [];
        $mesesData = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $mesesLabels[] = $fecha->translatedFormat('M Y');
            $mesesData[] = Cita::where('id_medico', $medico->id_medico)
                ->whereYear('fecha_cita', $fecha->year)
                ->whereMonth('fecha_cita', $fecha->month)
                ->count();
        }

        $estadosCitas = Cita::where('id_medico', $medico->id_medico)
            ->selectRaw("estado_cita, count(*) as total")
            ->groupBy('estado_cita')
            ->pluck('total', 'estado_cita')
            ->toArray();

        $topPacientes = Cita::where('id_medico', $medico->id_medico)
            ->selectRaw('id_paciente, count(*) as total_citas')
            ->groupBy('id_paciente')
            ->orderByDesc('total_citas')
            ->limit(10)
            ->with('paciente')
            ->get();

        $totalAtendidas = $estadosCitas['atendida'] ?? 0;
        $totalCitas = array_sum($estadosCitas);
        $tasaAtencion = $totalCitas > 0 ? round(($totalAtendidas / $totalCitas) * 100, 1) : 0;
        $totalCanceladas = $estadosCitas['cancelada'] ?? 0;
        $tasaCancelacion = $totalCitas > 0 ? round(($totalCanceladas / $totalCitas) * 100, 1) : 0;

        return view('dashboard.doctor-analytics', compact(
            'medico', 'mesesLabels', 'mesesData', 'estadosCitas',
            'topPacientes', 'tasaAtencion', 'tasaCancelacion', 'totalCitas'
        ));
    }

    public function agenda()
    {
        $user = auth()->user();

        if (!$user->esMedico() && !$user->esSuperAdmin()) {
            return redirect()->route('dashboard');
        }

        $medico = $user->medicoProfile();
        if (!$medico) {
            return redirect()->route('dashboard');
        }

        $medico->load('horariosActivos');
        $diasSemana = HorarioMedico::DIAS;

        $proximasCitas = Cita::where('id_medico', $medico->id_medico)
            ->where(function ($q) {
                $q->where('fecha_cita', '>', today())
                  ->orWhere(function ($q2) {
                      $q2->whereDate('fecha_cita', today())
                         ->where('hora_inicio', '>=', now()->format('H:i:s'));
                  });
            })
            ->whereIn('estado_cita', ['pendiente', 'confirmada'])
            ->orderBy('fecha_cita')
            ->orderBy('hora_inicio')
            ->with('paciente')
            ->limit(20)
            ->get();

        $historialReciente = Cita::where('id_medico', $medico->id_medico)
            ->where('estado_cita', 'atendida')
            ->orderByDesc('fecha_cita')
            ->orderByDesc('hora_inicio')
            ->with('paciente')
            ->limit(15)
            ->get();

        $inicioSemana = now()->startOfWeek();
        $finSemana = now()->endOfWeek();
        $citasSemana = Cita::where('id_medico', $medico->id_medico)
            ->whereBetween('fecha_cita', [$inicioSemana, $finSemana])
            ->whereIn('estado_cita', ['pendiente', 'confirmada', 'atendida'])
            ->orderBy('fecha_cita')
            ->orderBy('hora_inicio')
            ->with('paciente')
            ->get()
            ->groupBy(fn ($c) => Carbon::parse($c->fecha_cita)->dayOfWeekIso);

        return view('dashboard.doctor-agenda', compact(
            'medico', 'diasSemana', 'proximasCitas', 'historialReciente', 'citasSemana'
        ));
    }

    private function dashboardDoctor($user)
    {
        $medico = $user->medicoProfile();
        if (!$medico) {
            return view('dashboard');
        }

        $hoy = today();

        $citasHoy = Cita::where('id_medico', $medico->id_medico)
            ->whereDate('fecha_cita', $hoy)
            ->whereIn('estado_cita', ['pendiente', 'confirmada', 'atendida'])
            ->orderBy('hora_inicio')
            ->with('paciente')
            ->get();

        $atendidosHoy = $citasHoy->where('estado_cita', 'atendida')->count();
        $pendientesHoy = $citasHoy->whereIn('estado_cita', ['pendiente', 'confirmada'])->count();
        $totalCitasHoy = $citasHoy->count();

        $proximaCita = Cita::where('id_medico', $medico->id_medico)
            ->whereDate('fecha_cita', '>=', $hoy)
            ->whereIn('estado_cita', ['pendiente', 'confirmada'])
            ->orderBy('fecha_cita')
            ->orderBy('hora_inicio')
            ->with('paciente')
            ->first();

        $totalPacientes = Cita::where('id_medico', $medico->id_medico)
            ->distinct('id_paciente')
            ->count('id_paciente');

        $citasSemana = Cita::where('id_medico', $medico->id_medico)
            ->whereBetween('fecha_cita', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('estado_cita', ['pendiente', 'confirmada'])
            ->count();

        return view('dashboard.doctor', compact(
            'medico', 'citasHoy', 'atendidosHoy', 'pendientesHoy',
            'totalCitasHoy', 'proximaCita', 'totalPacientes', 'citasSemana'
        ));
    }
}
