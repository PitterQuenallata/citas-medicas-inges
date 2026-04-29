@extends('layouts.app')
@section('title', 'Reporte de Actividad de Médicos')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('reportes.index') }}" class="btn size-8 rounded-full border border-slate-300 p-0 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
            <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Actividad de Médicos</h2>
            <p class="text-xs text-slate-400 dark:text-navy-300">Citas y tasa de atención por médico</p>
        </div>
    </div>
    <a href="{{ route('reportes.pdf', 'medicos') }}?{{ http_build_query(request()->all()) }}"
       class="btn bg-info px-4 text-sm font-medium text-white hover:bg-info-focus flex items-center gap-2">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Exportar PDF
    </a>
</div>

<div class="card px-4 pb-4 sm:px-5 mb-4">
    <h3 class="text-sm font-medium text-slate-700 py-4">Filtros</h3>
    <form method="GET" action="{{ route('reportes.medicos') }}" class="flex flex-wrap gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] }}" class="form-input rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] }}" class="form-input rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn bg-primary px-4 text-xs font-medium text-white hover:bg-primary-focus">Filtrar</button>
            <a href="{{ route('reportes.medicos') }}" class="btn border border-slate-300 px-4 text-xs hover:bg-slate-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <h3 class="text-sm font-medium text-slate-700 py-4">
        {{ $medicos->count() }} médicos · {{ $filtros['fecha_desde'] }} al {{ $filtros['fecha_hasta'] }}
    </h3>
    <div class="overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Médico</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Especialidad(es)</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-center">Total</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-center">Atendidas</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-center">Canceladas</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-center">No Asistió</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-center">Tasa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medicos as $m)
                @php $tasa = $m->total_citas > 0 ? round(($m->citas_atendidas / $m->total_citas) * 100, 1) : 0; @endphp
                <tr class="border-y border-transparent border-b-slate-200">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <p class="text-sm font-medium text-slate-700">Dr. {{ $m->apellidos }}, {{ $m->nombres }}</p>
                        <p class="text-xs text-slate-400">{{ $m->codigo_medico }}</p>
                    </td>
                    <td class="px-3 py-3 sm:px-5 text-xs text-slate-500">{{ $m->especialidades->pluck('nombre_especialidad')->join(', ') ?: '—' }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center font-bold text-slate-700">{{ $m->total_citas }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center">
                        <span class="badge rounded-full bg-success/10 text-success text-xs px-2 py-0.5">{{ $m->citas_atendidas }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center">
                        <span class="badge rounded-full bg-error/10 text-error text-xs px-2 py-0.5">{{ $m->citas_canceladas }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center">
                        <span class="badge rounded-full bg-error/10 text-error text-xs px-2 py-0.5">{{ $m->citas_no_asistio }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center font-semibold {{ $tasa >= 70 ? 'text-success' : ($tasa >= 40 ? 'text-warning' : 'text-error') }}">
                        {{ $tasa }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
