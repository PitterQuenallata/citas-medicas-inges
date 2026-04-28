@extends('layouts.app')
@section('title', 'Resumen Mensual')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('reportes.index') }}" class="btn size-8 rounded-full border border-slate-300 p-0 hover:bg-slate-100">
            <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Resumen Mensual</h2>
            <p class="text-xs text-slate-400">{{ $meses[$mes] }} {{ $anio }} — Reporte ejecutivo</p>
        </div>
    </div>
    <a href="{{ route('reportes.pdf', 'resumen-mensual') }}?{{ http_build_query(request()->all()) }}"
       class="btn bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus flex items-center gap-2">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Exportar PDF
    </a>
</div>

{{-- Selector de mes --}}
<div class="card px-4 pb-4 sm:px-5 mb-4">
    <h3 class="text-sm font-medium text-slate-700 py-4">Seleccionar período</h3>
    <form method="GET" action="{{ route('reportes.resumen-mensual') }}" class="flex flex-wrap gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Mes</label>
            <select name="mes" class="form-select rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
                @foreach($meses as $num => $nombre)
                    <option value="{{ $num }}" @selected($mes == $num)>{{ $nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Año</label>
            <select name="anio" class="form-select rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" @selected($anio == $y)>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="btn bg-primary px-4 text-xs font-medium text-white hover:bg-primary-focus">Ver resumen</button>
        </div>
    </form>
</div>

{{-- KPIs principales --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6 mb-4">
    <div class="card p-4 text-center col-span-1">
        <p class="text-2xl font-bold text-primary">{{ $totalCitas }}</p>
        <p class="text-xs text-slate-400 mt-1">Total Citas</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-success">{{ $citasAtendidas }}</p>
        <p class="text-xs text-slate-400 mt-1">Atendidas</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-error">{{ $citasCanceladas }}</p>
        <p class="text-xs text-slate-400 mt-1">Canceladas/NA</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-info">{{ $pacientesNuevos }}</p>
        <p class="text-xs text-slate-400 mt-1">Pacientes nuevos</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-warning">Bs. {{ number_format($ingresosMes, 0) }}</p>
        <p class="text-xs text-slate-400 mt-1">Ingresos</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold {{ $tasaAsistencia >= 70 ? 'text-success' : ($tasaAsistencia >= 40 ? 'text-warning' : 'text-error') }}">{{ $tasaAsistencia }}%</p>
        <p class="text-xs text-slate-400 mt-1">Tasa asistencia</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    {{-- Distribución de estados --}}
    <div class="card px-4 pb-4 sm:px-5">
        <h3 class="text-sm font-medium text-slate-700 py-4">Distribución de estados</h3>
        @if($citasPorEstado->isEmpty())
            <p class="py-4 text-center text-sm text-slate-400">Sin datos este mes.</p>
        @else
        <div class="space-y-3">
            @php $totalBarras = $citasPorEstado->sum(); @endphp
            @foreach([
                'atendida'     => ['color' => 'bg-success', 'label' => 'Atendidas'],
                'confirmada'   => ['color' => 'bg-info', 'label' => 'Confirmadas'],
                'pendiente'    => ['color' => 'bg-warning', 'label' => 'Pendientes'],
                'cancelada'    => ['color' => 'bg-error', 'label' => 'Canceladas'],
                'no_asistio'   => ['color' => 'bg-error/60', 'label' => 'No Asistió'],
                'reprogramada' => ['color' => 'bg-secondary', 'label' => 'Reprogramadas'],
            ] as $estado => $cfg)
            @if(isset($citasPorEstado[$estado]) && $citasPorEstado[$estado] > 0)
            @php $pct = $totalBarras > 0 ? round(($citasPorEstado[$estado]/$totalBarras)*100) : 0; @endphp
            <div>
                <div class="flex justify-between text-xs text-slate-600 mb-1">
                    <span>{{ $cfg['label'] }}</span>
                    <span class="font-semibold">{{ $citasPorEstado[$estado] }} ({{ $pct }}%)</span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="{{ $cfg['color'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @endif
    </div>

    {{-- Top 5 médicos --}}
    <div class="card px-4 pb-4 sm:px-5">
        <h3 class="text-sm font-medium text-slate-700 py-4">Top 5 médicos con más citas</h3>
        @if($topMedicos->isEmpty())
            <p class="py-4 text-center text-sm text-slate-400">Sin datos este mes.</p>
        @else
        <div class="space-y-3">
            @php $maxCitas = $topMedicos->first()->total_citas; @endphp
            @foreach($topMedicos as $i => $tm)
            @php $pct = $maxCitas > 0 ? round(($tm->total_citas / $maxCitas) * 100) : 0; @endphp
            <div>
                <div class="flex justify-between text-xs text-slate-600 mb-1">
                    <span>{{ $i+1 }}. Dr. {{ $tm->medico?->apellidos }}</span>
                    <span class="font-semibold">{{ $tm->total_citas }} citas</span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="bg-primary h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Ingresos por método --}}
    @if($ingresosPorMetodo->count())
    <div class="card px-4 pb-4 sm:px-5">
        <h3 class="text-sm font-medium text-slate-700 py-4">Ingresos por método de pago</h3>
        <div class="space-y-3">
            @foreach($ingresosPorMetodo as $metodo => $monto)
            <div class="flex items-center justify-between">
                <span class="badge rounded-full bg-primary/10 text-primary text-xs px-3 py-1">{{ ucfirst($metodo) }}</span>
                <span class="font-bold text-slate-700">Bs. {{ number_format($monto, 2) }}</span>
            </div>
            @endforeach
            <div class="border-t border-slate-200 pt-2 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600">TOTAL</span>
                <span class="font-bold text-success">Bs. {{ number_format($ingresosPorMetodo->sum(), 2) }}</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Médico top --}}
    @if($medicoTop && $medicoTop->medico)
    <div class="card px-4 pb-4 sm:px-5">
        <h3 class="text-sm font-medium text-slate-700 py-4">Médico con más citas en el mes</h3>
        <div class="flex items-center gap-4 py-4">
            <div class="flex size-14 items-center justify-center rounded-full bg-primary/10">
                <svg class="size-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-slate-700">Dr. {{ $medicoTop->medico->apellidos }}, {{ $medicoTop->medico->nombres }}</p>
                <p class="text-xs text-slate-400">{{ $medicoTop->medico->codigo_medico }}</p>
                <p class="mt-1 text-lg font-bold text-primary">{{ $medicoTop->total }} citas en {{ $meses[$mes] }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
