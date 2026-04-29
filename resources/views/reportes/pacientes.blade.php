@extends('layouts.app')
@section('title', 'Reporte de Pacientes')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('reportes.index') }}" class="btn size-8 rounded-full border border-slate-300 p-0 hover:bg-slate-100">
            <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Reporte de Pacientes</h2>
            <p class="text-xs text-slate-400">Distribución y actividad de pacientes</p>
        </div>
    </div>
    <a href="{{ route('reportes.pdf', 'pacientes') }}?{{ http_build_query(request()->all()) }}"
       class="btn bg-success px-4 text-sm font-medium text-white hover:bg-success-focus flex items-center gap-2">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Exportar PDF
    </a>
</div>

{{-- Filtros --}}
<div class="card px-4 pb-4 sm:px-5 mb-4">
    <h3 class="text-sm font-medium text-slate-700 py-4">Filtros</h3>
    <form method="GET" action="{{ route('reportes.pacientes') }}" class="flex flex-wrap gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Estado</label>
            <select name="estado" class="form-select rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
                <option value="">Todos</option>
                <option value="activo" @selected(request('estado')=='activo')>Activo</option>
                <option value="inactivo" @selected(request('estado')=='inactivo')>Inactivo</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Sexo</label>
            <select name="sexo" class="form-select rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
                <option value="">Todos</option>
                <option value="masculino" @selected(request('sexo')=='masculino')>Masculino</option>
                <option value="femenino" @selected(request('sexo')=='femenino')>Femenino</option>
                <option value="otro" @selected(request('sexo')=='otro')>Otro</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn bg-primary px-4 text-xs font-medium text-white hover:bg-primary-focus">Filtrar</button>
            <a href="{{ route('reportes.pacientes') }}" class="btn border border-slate-300 px-4 text-xs hover:bg-slate-100">Limpiar</a>
        </div>
    </form>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-4">
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-slate-700">{{ $pacientes->count() }}</p>
        <p class="text-xs text-slate-400 mt-1">Total pacientes</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-success">{{ $distribucionEstado['activo'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">Activos</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-info">{{ $distribucionSexo['masculino'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">Masculino</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-secondary">{{ $distribucionSexo['femenino'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">Femenino</p>
    </div>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <h3 class="text-sm font-medium text-slate-700 py-4">Lista de pacientes</h3>
    @if($pacientes->isEmpty())
        <p class="py-8 text-center text-sm text-slate-400">No hay pacientes.</p>
    @else
    <div class="overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">CI</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Sexo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-center">Total Citas</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-center">Citas este mes</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pacientes as $p)
                <tr class="border-y border-transparent border-b-slate-200">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <p class="text-sm font-medium text-slate-700">{{ $p->apellidos }}, {{ $p->nombres }}</p>
                        <p class="text-xs text-slate-400">{{ $p->codigo_paciente }}</p>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-500">{{ $p->ci ?? '—' }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-500">{{ ucfirst($p->sexo ?? '—') }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center font-bold text-slate-700">{{ $p->total_citas }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center text-xs text-slate-500">{{ $p->citas_mes }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <span class="badge rounded-full {{ $p->estado === 'activo' ? 'bg-success/10 text-success' : 'bg-slate-100 text-slate-500' }} text-xs px-2.5 py-1">
                            {{ ucfirst($p->estado) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
