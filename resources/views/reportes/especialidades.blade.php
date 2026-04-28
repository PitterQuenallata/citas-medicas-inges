@extends('layouts.app')
@section('title', 'Reporte por Especialidad')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('reportes.index') }}" class="btn size-8 rounded-full border border-slate-300 p-0 hover:bg-slate-100">
            <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Atención por Especialidad</h2>
            <p class="text-xs text-slate-400">Citas e ingresos por especialidad médica</p>
        </div>
    </div>
    <a href="{{ route('reportes.pdf', 'especialidades') }}?{{ http_build_query(request()->all()) }}"
       class="btn bg-secondary px-4 text-sm font-medium text-white hover:bg-secondary-focus flex items-center gap-2">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Exportar PDF
    </a>
</div>

<div class="card px-4 pb-4 sm:px-5 mb-4">
    <h3 class="text-sm font-medium text-slate-700 py-4">Filtros</h3>
    <form method="GET" action="{{ route('reportes.especialidades') }}" class="flex flex-wrap gap-3">
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
            <a href="{{ route('reportes.especialidades') }}" class="btn border border-slate-300 px-4 text-xs hover:bg-slate-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <h3 class="text-sm font-medium text-slate-700 py-4">{{ $especialidades->count() }} especialidades</h3>
    <div class="overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Especialidad</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-center">Médicos</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-center">Total Citas</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-center">Atendidas</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-right">Costo Consulta</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-right">Ingresos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($especialidades as $esp)
                <tr class="border-y border-transparent border-b-slate-200">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <p class="text-sm font-medium text-slate-700">{{ $esp->nombre_especialidad }}</p>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center text-sm font-bold text-slate-700">{{ $esp->total_medicos }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center font-bold text-slate-700">{{ $esp->total_citas }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center">
                        <span class="badge rounded-full bg-success/10 text-success text-xs px-2 py-0.5">{{ $esp->citas_atendidas }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-right text-xs text-slate-500">Bs. {{ number_format($esp->costo_consulta, 2) }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-right font-semibold text-success">Bs. {{ number_format($esp->ingresos, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-200">
                    <td colspan="5" class="px-3 py-3 sm:px-5 text-xs font-bold text-slate-700 text-right">TOTAL:</td>
                    <td class="px-3 py-3 sm:px-5 text-right font-bold text-success">Bs. {{ number_format($especialidades->sum('ingresos'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
