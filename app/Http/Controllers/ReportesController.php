<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Especialidad;
use App\Models\Notificacion;
use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    // -------------------------------------------------------------------------
    // INDEX — Panel de reportes con tarjetas
    // -------------------------------------------------------------------------
    public function index()
    {
        $stats = [
            'total_citas_mes'     => Cita::whereMonth('fecha_cita', now()->month)->whereYear('fecha_cita', now()->year)->count(),
            'total_pacientes'     => Paciente::where('estado', 'activo')->count(),
            'total_medicos'       => Medico::where('estado', 'activo')->count(),
            'ingresos_mes'        => Pago::where('estado_pago', 'pagado')->whereMonth('fecha_pago', now()->month)->whereYear('fecha_pago', now()->year)->sum('monto'),
        ];

        return view('reportes.index', compact('stats'));
    }

    // -------------------------------------------------------------------------
    // R1 — Citas por Período
    // -------------------------------------------------------------------------
    public function citas(Request $request)
    {
        $filtros = $this->filtrosBase($request);

        $query = Cita::with(['paciente', 'medico.especialidades'])
            ->orderBy('fecha_cita', 'desc');

        $this->aplicarFiltrosFecha($query, $filtros, 'fecha_cita');

        if ($request->filled('estado')) {
            $query->where('estado_cita', $request->estado);
        }
        if ($request->filled('id_medico')) {
            $query->where('id_medico', $request->id_medico);
        }

        $citas = $query->get();

        $totalesPorEstado = $citas->groupBy('estado_cita')->map->count();

        $medicos = Medico::where('estado', 'activo')->orderBy('apellidos')->get();

        return view('reportes.citas', compact('citas', 'totalesPorEstado', 'medicos', 'filtros'));
    }

    // -------------------------------------------------------------------------
    // R2 — Actividad de Médicos
    // -------------------------------------------------------------------------
    public function medicos(Request $request)
    {
        $filtros = $this->filtrosBase($request);

        $query = Medico::with('especialidades')
            ->withCount([
                'citas as total_citas' => fn($q) => $this->aplicarFiltrosFecha($q, $filtros, 'fecha_cita'),
                'citas as citas_atendidas' => fn($q) => $this->aplicarFiltrosFecha($q->where('estado_cita', 'atendida'), $filtros, 'fecha_cita'),
                'citas as citas_canceladas' => fn($q) => $this->aplicarFiltrosFecha($q->where('estado_cita', 'cancelada'), $filtros, 'fecha_cita'),
                'citas as citas_no_asistio' => fn($q) => $this->aplicarFiltrosFecha($q->where('estado_cita', 'no_asistio'), $filtros, 'fecha_cita'),
            ])
            ->where('estado', 'activo')
            ->orderByDesc('total_citas');

        $medicos = $query->get();

        return view('reportes.medicos', compact('medicos', 'filtros'));
    }

    // -------------------------------------------------------------------------
    // R3 — Pacientes
    // -------------------------------------------------------------------------
    public function pacientes(Request $request)
    {
        $filtros = $this->filtrosBase($request);

        $query = Paciente::withCount([
                'citas as total_citas',
                'citas as citas_mes' => fn($q) => $q->whereMonth('fecha_cita', now()->month),
            ])
            ->with('citas');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        $pacientes = $query->orderBy('apellidos')->get();

        $distribucionSexo = $pacientes->groupBy('sexo')->map->count();
        $distribucionEstado = $pacientes->groupBy('estado')->map->count();

        return view('reportes.pacientes', compact('pacientes', 'distribucionSexo', 'distribucionEstado', 'filtros'));
    }

    // -------------------------------------------------------------------------
    // R4 — Ingresos / Pagos
    // -------------------------------------------------------------------------
    public function pagos(Request $request)
    {
        $filtros = $this->filtrosBase($request);

        $query = Pago::with(['cita.paciente', 'cita.medico', 'usuarioRegistra'])
            ->orderBy('created_at', 'desc');

        if ($filtros['fecha_desde']) {
            $query->whereDate('created_at', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $query->whereDate('created_at', '<=', $filtros['fecha_hasta']);
        }
        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }
        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }

        $pagos = $query->get();

        $totalPagado    = $pagos->where('estado_pago', 'pagado')->sum('monto');
        $totalPendiente = $pagos->where('estado_pago', 'pendiente')->sum('monto');
        $totalAnulado   = $pagos->where('estado_pago', 'anulado')->sum('monto');
        $porMetodo      = $pagos->where('estado_pago', 'pagado')->groupBy('metodo_pago')->map->sum('monto');

        return view('reportes.pagos', compact('pagos', 'totalPagado', 'totalPendiente', 'totalAnulado', 'porMetodo', 'filtros'));
    }

    // -------------------------------------------------------------------------
    // R5 — Atención por Especialidad
    // -------------------------------------------------------------------------
    public function especialidades(Request $request)
    {
        $filtros = $this->filtrosBase($request);

        $especialidades = Especialidad::with(['medicos' => fn($q) => $q->where('estado', 'activo')])
            ->withCount([
                'medicos as total_medicos' => fn($q) => $q->where('estado', 'activo'),
            ])
            ->where('estado', 'activo')
            ->get();

        // Citas por especialidad en el período
        foreach ($especialidades as $esp) {
            $q = Cita::whereHas('medico.especialidades', fn($q2) => $q2->where('especialidades.id_especialidad', $esp->id_especialidad));
            $this->aplicarFiltrosFecha($q, $filtros, 'fecha_cita');
            $esp->total_citas  = $q->count();
            $esp->citas_atendidas = (clone $q)->where('estado_cita', 'atendida')->count();

            $pagosQ = Pago::where('estado_pago', 'pagado')
                ->whereHas('cita.medico.especialidades', fn($q2) => $q2->where('especialidades.id_especialidad', $esp->id_especialidad));
            if ($filtros['fecha_desde']) $pagosQ->whereDate('fecha_pago', '>=', $filtros['fecha_desde']);
            if ($filtros['fecha_hasta']) $pagosQ->whereDate('fecha_pago', '<=', $filtros['fecha_hasta']);
            $esp->ingresos = $pagosQ->sum('monto');
        }

        return view('reportes.especialidades', compact('especialidades', 'filtros'));
    }

    // -------------------------------------------------------------------------
    // R6 — Notificaciones
    // -------------------------------------------------------------------------
    public function notificaciones(Request $request)
    {
        $filtros = $this->filtrosBase($request);

        $query = Notificacion::with(['cita.paciente'])
            ->orderBy('fecha_envio', 'desc');

        if ($filtros['fecha_desde']) {
            $query->whereDate('fecha_envio', '>=', $filtros['fecha_desde']);
        }
        if ($filtros['fecha_hasta']) {
            $query->whereDate('fecha_envio', '<=', $filtros['fecha_hasta']);
        }
        if ($request->filled('canal')) {
            $query->where('canal', $request->canal);
        }
        if ($request->filled('estado_envio')) {
            $query->where('estado_envio', $request->estado_envio);
        }

        $notificaciones = $query->get();

        $porCanal  = $notificaciones->groupBy('canal')->map->count();
        $porEstado = $notificaciones->groupBy('estado_envio')->map->count();

        return view('reportes.notificaciones', compact('notificaciones', 'porCanal', 'porEstado', 'filtros'));
    }

    // -------------------------------------------------------------------------
    // R7 — Citas Canceladas / No Asistidas
    // -------------------------------------------------------------------------
    public function canceladas(Request $request)
    {
        $filtros = $this->filtrosBase($request);

        $query = Cita::with(['paciente', 'medico'])
            ->whereIn('estado_cita', ['cancelada', 'no_asistio'])
            ->orderBy('fecha_cita', 'desc');

        $this->aplicarFiltrosFecha($query, $filtros, 'fecha_cita');

        if ($request->filled('id_medico')) {
            $query->where('id_medico', $request->id_medico);
        }
        if ($request->filled('estado_cita')) {
            $query->where('estado_cita', $request->estado_cita);
        }

        $citas = $query->get();

        $porMedico  = $citas->groupBy(fn($c) => optional($c->medico)->apellidos . ', ' . optional($c->medico)->nombres)->map->count()->sortDesc();
        $porEstado  = $citas->groupBy('estado_cita')->map->count();
        $porMes     = $citas->groupBy(fn($c) => $c->fecha_cita->format('Y-m'))->map->count()->sortKeys();

        $medicos = Medico::where('estado', 'activo')->orderBy('apellidos')->get();

        return view('reportes.canceladas', compact('citas', 'porMedico', 'porEstado', 'porMes', 'medicos', 'filtros'));
    }

    // -------------------------------------------------------------------------
    // R8 — Resumen Mensual
    // -------------------------------------------------------------------------
    public function resumenMensual(Request $request)
    {
        $mes  = $request->input('mes',  now()->month);
        $anio = $request->input('anio', now()->year);

        $totalCitas       = Cita::whereMonth('fecha_cita', $mes)->whereYear('fecha_cita', $anio)->count();
        $citasAtendidas   = Cita::whereMonth('fecha_cita', $mes)->whereYear('fecha_cita', $anio)->where('estado_cita', 'atendida')->count();
        $citasCanceladas  = Cita::whereMonth('fecha_cita', $mes)->whereYear('fecha_cita', $anio)->whereIn('estado_cita', ['cancelada', 'no_asistio'])->count();
        $pacientesNuevos  = Paciente::whereMonth('created_at', $mes)->whereYear('created_at', $anio)->count();
        $ingresosMes      = Pago::where('estado_pago', 'pagado')->whereMonth('fecha_pago', $mes)->whereYear('fecha_pago', $anio)->sum('monto');
        $tasaAsistencia   = $totalCitas > 0 ? round(($citasAtendidas / $totalCitas) * 100, 1) : 0;

        // Médico con más citas
        $medicoTop = Cita::select('id_medico', DB::raw('count(*) as total'))
            ->whereMonth('fecha_cita', $mes)->whereYear('fecha_cita', $anio)
            ->groupBy('id_medico')
            ->orderByDesc('total')
            ->with('medico')
            ->first();

        // Distribución de estados
        $citasPorEstado = Cita::select('estado_cita', DB::raw('count(*) as total'))
            ->whereMonth('fecha_cita', $mes)->whereYear('fecha_cita', $anio)
            ->groupBy('estado_cita')
            ->pluck('total', 'estado_cita');

        // Ingresos por método
        $ingresosPorMetodo = Pago::select('metodo_pago', DB::raw('sum(monto) as total'))
            ->where('estado_pago', 'pagado')
            ->whereMonth('fecha_pago', $mes)->whereYear('fecha_pago', $anio)
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        // Top 5 médicos
        $topMedicos = Cita::select('id_medico', DB::raw('count(*) as total_citas'))
            ->whereMonth('fecha_cita', $mes)->whereYear('fecha_cita', $anio)
            ->groupBy('id_medico')
            ->orderByDesc('total_citas')
            ->with('medico')
            ->limit(5)
            ->get();

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return view('reportes.resumen-mensual', compact(
            'mes', 'anio', 'meses',
            'totalCitas', 'citasAtendidas', 'citasCanceladas',
            'pacientesNuevos', 'ingresosMes', 'tasaAsistencia',
            'medicoTop', 'citasPorEstado', 'ingresosPorMetodo', 'topMedicos'
        ));
    }

    // -------------------------------------------------------------------------
    // EXPORTAR PDF — recolecta datos y genera PDF
    // -------------------------------------------------------------------------
    public function exportarPdf(Request $request, string $tipo)
    {
        $allowed = ['citas', 'medicos', 'pacientes', 'pagos', 'especialidades', 'notificaciones', 'canceladas', 'resumen-mensual'];
        if (!in_array($tipo, $allowed)) {
            abort(404);
        }

        $generadoEn = now()->format('d/m/Y H:i');
        $filtros    = $request->only(['fecha_desde', 'fecha_hasta', 'mes', 'anio', 'estado', 'id_medico', 'metodo_pago', 'estado_pago', 'canal', 'estado_envio', 'estado_cita', 'sexo', 'estado']);

        // Recolectar datos según el tipo
        $data = match($tipo) {
            'citas'           => $this->dataCitas($request),
            'medicos'         => $this->dataMedicos($request),
            'pacientes'       => $this->dataPacientes($request),
            'pagos'           => $this->dataPagos($request),
            'especialidades'  => $this->dataEspecialidades($request),
            'notificaciones'  => $this->dataNotificaciones($request),
            'canceladas'      => $this->dataCanceladas($request),
            'resumen-mensual' => $this->dataResumenMensual($request),
        };

        $view = view("pdf.reporte-{$tipo}", array_merge($data, compact('generadoEn', 'filtros')));
        $pdf  = Pdf::loadHTML($view->render())->setPaper('a4', 'landscape');

        return $pdf->download("reporte-{$tipo}-" . now()->format('Ymd-His') . '.pdf');
    }

    // -------------------------------------------------------------------------
    // DATA HELPERS para exportarPdf (comparten lógica con los métodos de vista)
    // -------------------------------------------------------------------------
    private function dataCitas(Request $request): array
    {
        $filtros = $this->filtrosBase($request);
        $query = \App\Models\Cita::with(['paciente', 'medico'])->orderBy('fecha_cita', 'desc');
        $this->aplicarFiltrosFecha($query, $filtros, 'fecha_cita');
        if ($request->filled('estado')) $query->where('estado_cita', $request->estado);
        if ($request->filled('id_medico')) $query->where('id_medico', $request->id_medico);
        $citas = $query->get();
        return ['citas' => $citas, 'totalesPorEstado' => $citas->groupBy('estado_cita')->map->count(), 'filtros' => $filtros];
    }

    private function dataMedicos(Request $request): array
    {
        $filtros = $this->filtrosBase($request);
        $medicos = Medico::with('especialidades')
            ->withCount([
                'citas as total_citas'       => fn($q) => $this->aplicarFiltrosFecha($q, $filtros, 'fecha_cita'),
                'citas as citas_atendidas'   => fn($q) => $this->aplicarFiltrosFecha($q->where('estado_cita','atendida'), $filtros, 'fecha_cita'),
                'citas as citas_canceladas'  => fn($q) => $this->aplicarFiltrosFecha($q->where('estado_cita','cancelada'), $filtros, 'fecha_cita'),
                'citas as citas_no_asistio'  => fn($q) => $this->aplicarFiltrosFecha($q->where('estado_cita','no_asistio'), $filtros, 'fecha_cita'),
            ])
            ->where('estado','activo')->orderByDesc('total_citas')->get();
        return compact('medicos', 'filtros');
    }

    private function dataPacientes(Request $request): array
    {
        $filtros = $this->filtrosBase($request);
        $query = Paciente::withCount(['citas as total_citas', 'citas as citas_mes' => fn($q) => $q->whereMonth('fecha_cita', now()->month)]);
        if ($request->filled('estado')) $query->where('estado', $request->estado);
        if ($request->filled('sexo')) $query->where('sexo', $request->sexo);
        $pacientes = $query->orderBy('apellidos')->get();
        return ['pacientes' => $pacientes, 'distribucionSexo' => $pacientes->groupBy('sexo')->map->count(), 'distribucionEstado' => $pacientes->groupBy('estado')->map->count(), 'filtros' => $filtros];
    }

    private function dataPagos(Request $request): array
    {
        $filtros = $this->filtrosBase($request);
        $query = Pago::with(['cita.paciente','cita.medico','usuarioRegistra'])->orderBy('created_at','desc');
        if ($filtros['fecha_desde']) $query->whereDate('created_at','>=',$filtros['fecha_desde']);
        if ($filtros['fecha_hasta']) $query->whereDate('created_at','<=',$filtros['fecha_hasta']);
        if ($request->filled('metodo_pago')) $query->where('metodo_pago',$request->metodo_pago);
        if ($request->filled('estado_pago')) $query->where('estado_pago',$request->estado_pago);
        $pagos = $query->get();
        return ['pagos' => $pagos, 'totalPagado' => $pagos->where('estado_pago','pagado')->sum('monto'), 'totalPendiente' => $pagos->where('estado_pago','pendiente')->sum('monto'), 'totalAnulado' => $pagos->where('estado_pago','anulado')->sum('monto'), 'porMetodo' => $pagos->where('estado_pago','pagado')->groupBy('metodo_pago')->map->sum('monto'), 'filtros' => $filtros];
    }

    private function dataEspecialidades(Request $request): array
    {
        $filtros = $this->filtrosBase($request);
        $especialidades = Especialidad::with(['medicos' => fn($q) => $q->where('estado','activo')])->withCount(['medicos as total_medicos' => fn($q) => $q->where('estado','activo')])->where('estado','activo')->get();
        foreach ($especialidades as $esp) {
            $q = Cita::whereHas('medico.especialidades', fn($q2) => $q2->where('especialidades.id_especialidad',$esp->id_especialidad));
            $this->aplicarFiltrosFecha($q, $filtros, 'fecha_cita');
            $esp->total_citas = $q->count();
            $esp->citas_atendidas = (clone $q)->where('estado_cita','atendida')->count();
            $pagosQ = Pago::where('estado_pago','pagado')->whereHas('cita.medico.especialidades', fn($q2) => $q2->where('especialidades.id_especialidad',$esp->id_especialidad));
            if ($filtros['fecha_desde']) $pagosQ->whereDate('fecha_pago','>=',$filtros['fecha_desde']);
            if ($filtros['fecha_hasta']) $pagosQ->whereDate('fecha_pago','<=',$filtros['fecha_hasta']);
            $esp->ingresos = $pagosQ->sum('monto');
        }
        return compact('especialidades', 'filtros');
    }

    private function dataNotificaciones(Request $request): array
    {
        $filtros = $this->filtrosBase($request);
        $query = Notificacion::with(['cita.paciente'])->orderBy('fecha_envio','desc');
        if ($filtros['fecha_desde']) $query->whereDate('fecha_envio','>=',$filtros['fecha_desde']);
        if ($filtros['fecha_hasta']) $query->whereDate('fecha_envio','<=',$filtros['fecha_hasta']);
        if ($request->filled('canal')) $query->where('canal',$request->canal);
        if ($request->filled('estado_envio')) $query->where('estado_envio',$request->estado_envio);
        $notificaciones = $query->get();
        return ['notificaciones' => $notificaciones, 'porCanal' => $notificaciones->groupBy('canal')->map->count(), 'porEstado' => $notificaciones->groupBy('estado_envio')->map->count(), 'filtros' => $filtros];
    }

    private function dataCanceladas(Request $request): array
    {
        $filtros = $this->filtrosBase($request);
        $query = Cita::with(['paciente','medico'])->whereIn('estado_cita',['cancelada','no_asistio'])->orderBy('fecha_cita','desc');
        $this->aplicarFiltrosFecha($query, $filtros, 'fecha_cita');
        if ($request->filled('id_medico')) $query->where('id_medico',$request->id_medico);
        if ($request->filled('estado_cita')) $query->where('estado_cita',$request->estado_cita);
        $citas = $query->get();
        return ['citas' => $citas, 'porMedico' => $citas->groupBy(fn($c) => optional($c->medico)->apellidos)->map->count()->sortDesc(), 'porEstado' => $citas->groupBy('estado_cita')->map->count(), 'porMes' => $citas->groupBy(fn($c) => $c->fecha_cita->format('Y-m'))->map->count()->sortKeys(), 'filtros' => $filtros];
    }

    private function dataResumenMensual(Request $request): array
    {
        $mes  = $request->input('mes',  now()->month);
        $anio = $request->input('anio', now()->year);
        $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
        $totalCitas      = Cita::whereMonth('fecha_cita',$mes)->whereYear('fecha_cita',$anio)->count();
        $citasAtendidas  = Cita::whereMonth('fecha_cita',$mes)->whereYear('fecha_cita',$anio)->where('estado_cita','atendida')->count();
        $citasCanceladas = Cita::whereMonth('fecha_cita',$mes)->whereYear('fecha_cita',$anio)->whereIn('estado_cita',['cancelada','no_asistio'])->count();
        $pacientesNuevos = Paciente::whereMonth('created_at',$mes)->whereYear('created_at',$anio)->count();
        $ingresosMes     = Pago::where('estado_pago','pagado')->whereMonth('fecha_pago',$mes)->whereYear('fecha_pago',$anio)->sum('monto');
        $tasaAsistencia  = $totalCitas > 0 ? round(($citasAtendidas/$totalCitas)*100,1) : 0;
        $medicoTop       = Cita::select('id_medico', DB::raw('count(*) as total'))->whereMonth('fecha_cita',$mes)->whereYear('fecha_cita',$anio)->groupBy('id_medico')->orderByDesc('total')->with('medico')->first();
        $citasPorEstado  = Cita::select('estado_cita', DB::raw('count(*) as total'))->whereMonth('fecha_cita',$mes)->whereYear('fecha_cita',$anio)->groupBy('estado_cita')->pluck('total','estado_cita');
        $ingresosPorMetodo = Pago::select('metodo_pago', DB::raw('sum(monto) as total'))->where('estado_pago','pagado')->whereMonth('fecha_pago',$mes)->whereYear('fecha_pago',$anio)->groupBy('metodo_pago')->pluck('total','metodo_pago');
        $topMedicos      = Cita::select('id_medico', DB::raw('count(*) as total_citas'))->whereMonth('fecha_cita',$mes)->whereYear('fecha_cita',$anio)->groupBy('id_medico')->orderByDesc('total_citas')->with('medico')->limit(5)->get();
        return compact('mes','anio','meses','totalCitas','citasAtendidas','citasCanceladas','pacientesNuevos','ingresosMes','tasaAsistencia','medicoTop','citasPorEstado','ingresosPorMetodo','topMedicos');
    }

    // -------------------------------------------------------------------------
    // HELPERS privados
    // -------------------------------------------------------------------------
    private function filtrosBase(Request $request): array
    {
        return [
            'fecha_desde' => $request->input('fecha_desde', now()->startOfMonth()->toDateString()),
            'fecha_hasta' => $request->input('fecha_hasta', now()->toDateString()),
        ];
    }

    private function aplicarFiltrosFecha($query, array $filtros, string $columna): mixed
    {
        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate($columna, '>=', $filtros['fecha_desde']);
        }
        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate($columna, '<=', $filtros['fecha_hasta']);
        }
        return $query;
    }
}
