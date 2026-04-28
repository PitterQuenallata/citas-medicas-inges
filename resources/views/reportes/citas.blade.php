@extends('layouts.app')
@section('title', 'Reporte de Citas por Período')

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('reportes.index') }}" class="btn size-8 rounded-full border border-slate-300 p-0 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
            <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Reporte de Citas</h2>
            <p class="text-xs text-slate-400 dark:text-navy-300">Por período de fecha</p>
        </div>
    </div>
    <a href="{{ route('reportes.pdf', 'citas') }}?{{ http_build_query(request()->all()) }}"
       class="btn bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus flex items-center gap-2">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Exportar PDF
    </a>
</div>

{{-- Filtros --}}
<div class="card px-4 pb-4 sm:px-5 mb-4">
    <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100 py-4">Filtros</h3>
    <form method="GET" action="{{ route('reportes.citas') }}" class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-navy-100 mb-1">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] }}"
                class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-navy-100 mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] }}"
                class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-navy-100 mb-1">Estado</label>
            <select name="estado" class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700">
                <option value="">Todos</option>
                @foreach(['pendiente','confirmada','atendida','cancelada','reprogramada','no_asistio'] as $e)
                    <option value="{{ $e }}" @selected(request('estado') == $e)>{{ ucfirst(str_replace('_',' ',$e)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-navy-100 mb-1">Médico</label>
            <select name="id_medico" class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700">
                <option value="">Todos</option>
                @foreach($medicos as $m)
                    <option value="{{ $m->id_medico }}" @selected(request('id_medico') == $m->id_medico)>{{ $m->apellidos }}, {{ $m->nombres }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2 sm:col-span-4 flex gap-2">
            <button type="submit" class="btn bg-primary px-4 text-xs font-medium text-white hover:bg-primary-focus">Filtrar</button>
            <a href="{{ route('reportes.citas') }}" class="btn border border-slate-300 px-4 text-xs hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Limpiar</a>
        </div>
    </form>
</div>

{{-- Resumen por estado --}}
@if($citas->count())
<div class="grid grid-cols-3 gap-3 sm:grid-cols-6 mb-4">
    @foreach(['pendiente' => 'warning', 'confirmada' => 'info', 'atendida' => 'success', 'cancelada' => 'error', 'reprogramada' => 'secondary', 'no_asistio' => 'error'] as $estado => $color)
    <div class="card p-3 text-center">
        <p class="text-xl font-bold text-{{ $color }}">{{ $totalesPorEstado[$estado] ?? 0 }}</p>
        <p class="text-xs text-slate-400 dark:text-navy-300 mt-0.5">{{ ucfirst(str_replace('_',' ',$estado)) }}</p>
    </div>
    @endforeach
</div>
@endif

{{-- Tabla --}}
<div class="card px-4 pb-4 sm:px-5">
    <div class="flex items-center justify-between py-4">
        <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">
            {{ number_format($citas->count()) }} citas encontradas
        </h3>
        <span class="text-xs text-slate-400">{{ $filtros['fecha_desde'] }} al {{ $filtros['fecha_hasta'] }}</span>
    </div>
    @if($citas->isEmpty())
        <p class="py-8 text-center text-sm text-slate-400">No hay citas en el período seleccionado.</p>
    @else
    <div class="overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">Código</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">Fecha</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">Médico</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">Hora</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($citas as $cita)
                @php
                    $badgeColor = match($cita->estado_cita) {
                        'atendida'     => 'bg-success/10 text-success',
                        'confirmada'   => 'bg-info/10 text-info',
                        'cancelada'    => 'bg-error/10 text-error',
                        'no_asistio'   => 'bg-error/10 text-error',
                        'reprogramada' => 'bg-secondary/10 text-secondary',
                        default        => 'bg-warning/10 text-warning',
                    };
                @endphp
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs font-mono text-slate-500">{{ $cita->codigo_cita }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-600 dark:text-navy-100">{{ $cita->fecha_cita->format('d/m/Y') }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-600 dark:text-navy-100">{{ $cita->paciente?->apellidos }}, {{ $cita->paciente?->nombres }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-600 dark:text-navy-100">Dr. {{ $cita->medico?->apellidos }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-500">{{ substr($cita->hora_inicio,0,5) }} – {{ substr($cita->hora_fin,0,5) }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <span class="badge rounded-full {{ $badgeColor }} text-xs px-2.5 py-1">{{ ucfirst(str_replace('_',' ',$cita->estado_cita)) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
