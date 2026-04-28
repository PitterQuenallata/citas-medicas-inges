@extends('layouts.app')
@section('title', 'Reporte de Citas Canceladas y No Asistidas')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('reportes.index') }}" class="btn size-8 rounded-full border border-slate-300 p-0 hover:bg-slate-100">
            <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Canceladas y No Asistidas</h2>
            <p class="text-xs text-slate-400">Análisis de inasistencias y cancelaciones</p>
        </div>
    </div>
    <a href="{{ route('reportes.pdf', 'canceladas') }}?{{ http_build_query(request()->all()) }}"
       class="btn bg-error px-4 text-sm font-medium text-white hover:bg-error-focus flex items-center gap-2">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Exportar PDF
    </a>
</div>

<div class="card px-4 pb-4 sm:px-5 mb-4">
    <h3 class="text-sm font-medium text-slate-700 py-4">Filtros</h3>
    <form method="GET" action="{{ route('reportes.canceladas') }}" class="flex flex-wrap gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] }}" class="form-input rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] }}" class="form-input rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Tipo</label>
            <select name="estado_cita" class="form-select rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
                <option value="">Todos</option>
                <option value="cancelada" @selected(request('estado_cita')=='cancelada')>Cancelada</option>
                <option value="no_asistio" @selected(request('estado_cita')=='no_asistio')>No Asistió</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Médico</label>
            <select name="id_medico" class="form-select rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
                <option value="">Todos</option>
                @foreach($medicos as $m)
                    <option value="{{ $m->id_medico }}" @selected(request('id_medico')==$m->id_medico)>{{ $m->apellidos }}, {{ $m->nombres }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn bg-primary px-4 text-xs font-medium text-white hover:bg-primary-focus">Filtrar</button>
            <a href="{{ route('reportes.canceladas') }}" class="btn border border-slate-300 px-4 text-xs hover:bg-slate-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-4">
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-error">{{ $citas->count() }}</p>
        <p class="text-xs text-slate-400 mt-1">Total</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-error">{{ $porEstado['cancelada'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">Canceladas</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-warning">{{ $porEstado['no_asistio'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">No Asistió</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-slate-600">{{ $porMedico->count() }}</p>
        <p class="text-xs text-slate-400 mt-1">Médicos afectados</p>
    </div>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <h3 class="text-sm font-medium text-slate-700 py-4">{{ $citas->count() }} citas · {{ $filtros['fecha_desde'] }} al {{ $filtros['fecha_hasta'] }}</h3>
    @if($citas->isEmpty())
        <p class="py-8 text-center text-sm text-slate-400">No hay citas canceladas en el período seleccionado.</p>
    @else
    <div class="overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Código</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Fecha Cita</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Médico</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Estado</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($citas as $cita)
                <tr class="border-y border-transparent border-b-slate-200">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs font-mono text-slate-500">{{ $cita->codigo_cita }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-600">{{ $cita->fecha_cita->format('d/m/Y') }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-600">{{ $cita->paciente?->apellidos }}, {{ $cita->paciente?->nombres }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-600">Dr. {{ $cita->medico?->apellidos }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <span class="badge rounded-full {{ $cita->estado_cita === 'cancelada' ? 'bg-error/10 text-error' : 'bg-warning/10 text-warning' }} text-xs px-2.5 py-1">
                            {{ $cita->estado_cita === 'no_asistio' ? 'No Asistió' : 'Cancelada' }}
                        </span>
                    </td>
                    <td class="px-3 py-3 sm:px-5 text-xs text-slate-400 max-w-xs truncate">{{ $cita->motivo_cancelacion ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
