@extends('layouts.app')
@section('title', 'Reporte de Notificaciones')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('reportes.index') }}" class="btn size-8 rounded-full border border-slate-300 p-0 hover:bg-slate-100">
            <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Reporte de Notificaciones</h2>
            <p class="text-xs text-slate-400">Historial de envíos por canal y estado</p>
        </div>
    </div>
    <a href="{{ route('reportes.pdf', 'notificaciones') }}?{{ http_build_query(request()->all()) }}"
       class="btn bg-error px-4 text-sm font-medium text-white hover:bg-error-focus flex items-center gap-2">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Exportar PDF
    </a>
</div>

<div class="card px-4 pb-4 sm:px-5 mb-4">
    <h3 class="text-sm font-medium text-slate-700 py-4">Filtros</h3>
    <form method="GET" action="{{ route('reportes.notificaciones') }}" class="flex flex-wrap gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] }}" class="form-input rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] }}" class="form-input rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Canal</label>
            <select name="canal" class="form-select rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
                <option value="">Todos</option>
                @foreach(['whatsapp','email','sms','sistema'] as $c)
                    <option value="{{ $c }}" @selected(request('canal')==$c)>{{ ucfirst($c) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Estado</label>
            <select name="estado_envio" class="form-select rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
                <option value="">Todos</option>
                <option value="enviado" @selected(request('estado_envio')=='enviado')>Enviado</option>
                <option value="pendiente" @selected(request('estado_envio')=='pendiente')>Pendiente</option>
                <option value="fallido" @selected(request('estado_envio')=='fallido')>Fallido</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn bg-primary px-4 text-xs font-medium text-white hover:bg-primary-focus">Filtrar</button>
            <a href="{{ route('reportes.notificaciones') }}" class="btn border border-slate-300 px-4 text-xs hover:bg-slate-100">Limpiar</a>
        </div>
    </form>
</div>

{{-- Resumen --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-4">
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-slate-700">{{ $notificaciones->count() }}</p>
        <p class="text-xs text-slate-400 mt-1">Total</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-success">{{ $porEstado['enviado'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">Enviadas</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-warning">{{ $porEstado['pendiente'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">Pendientes</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-error">{{ $porEstado['fallido'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">Fallidas</p>
    </div>
</div>

@if($porCanal->count())
<div class="flex gap-3 mb-4 flex-wrap">
    @foreach($porCanal as $canal => $cant)
    <div class="card px-4 py-3 flex items-center gap-2">
        <span class="badge rounded-full bg-primary/10 text-primary text-xs px-2.5 py-1">{{ ucfirst($canal) }}</span>
        <span class="font-bold text-slate-700">{{ $cant }}</span>
    </div>
    @endforeach
</div>
@endif

<div class="card px-4 pb-4 sm:px-5">
    <h3 class="text-sm font-medium text-slate-700 py-4">{{ $notificaciones->count() }} notificaciones</h3>
    @if($notificaciones->isEmpty())
        <p class="py-8 text-center text-sm text-slate-400">No hay notificaciones en el período.</p>
    @else
    <div class="overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Fecha Envío</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Tipo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Canal</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notificaciones as $n)
                @php
                    $estadoColor = match($n->estado_envio) {
                        'enviado'  => 'bg-success/10 text-success',
                        'fallido'  => 'bg-error/10 text-error',
                        default    => 'bg-warning/10 text-warning',
                    };
                @endphp
                <tr class="border-y border-transparent border-b-slate-200">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-500">{{ $n->fecha_envio->format('d/m/Y H:i') }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-600">{{ $n->cita?->paciente?->apellidos }}, {{ $n->cita?->paciente?->nombres }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-500">{{ ucfirst($n->tipo_notificacion) }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <span class="badge rounded-full bg-info/10 text-info text-xs px-2 py-0.5">{{ ucfirst($n->canal) }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <span class="badge rounded-full {{ $estadoColor }} text-xs px-2.5 py-1">{{ ucfirst($n->estado_envio) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
