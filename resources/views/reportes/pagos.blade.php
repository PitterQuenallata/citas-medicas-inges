@extends('layouts.app')
@section('title', 'Reporte de Ingresos y Pagos')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('reportes.index') }}" class="btn size-8 rounded-full border border-slate-300 p-0 hover:bg-slate-100">
            <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Ingresos y Pagos</h2>
            <p class="text-xs text-slate-400">Recaudación por período y método de pago</p>
        </div>
    </div>
    <a href="{{ route('reportes.pdf', 'pagos') }}?{{ http_build_query(request()->all()) }}"
       class="btn bg-warning px-4 text-sm font-medium text-white hover:bg-warning-focus flex items-center gap-2">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Exportar PDF
    </a>
</div>

<div class="card px-4 pb-4 sm:px-5 mb-4">
    <h3 class="text-sm font-medium text-slate-700 py-4">Filtros</h3>
    <form method="GET" action="{{ route('reportes.pagos') }}" class="flex flex-wrap gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] }}" class="form-input rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] }}" class="form-input rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Método de pago</label>
            <select name="metodo_pago" class="form-select rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
                <option value="">Todos</option>
                <option value="efectivo" @selected(request('metodo_pago')=='efectivo')>Efectivo</option>
                <option value="qr" @selected(request('metodo_pago')=='qr')>QR</option>
                <option value="transferencia" @selected(request('metodo_pago')=='transferencia')>Transferencia</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Estado</label>
            <select name="estado_pago" class="form-select rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none">
                <option value="">Todos</option>
                <option value="pagado" @selected(request('estado_pago')=='pagado')>Pagado</option>
                <option value="pendiente" @selected(request('estado_pago')=='pendiente')>Pendiente</option>
                <option value="anulado" @selected(request('estado_pago')=='anulado')>Anulado</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn bg-primary px-4 text-xs font-medium text-white hover:bg-primary-focus">Filtrar</button>
            <a href="{{ route('reportes.pagos') }}" class="btn border border-slate-300 px-4 text-xs hover:bg-slate-100">Limpiar</a>
        </div>
    </form>
</div>

{{-- Resumen financiero --}}
<div class="grid grid-cols-1 gap-3 sm:grid-cols-3 mb-4">
    <div class="card p-4 border-l-4 border-success">
        <p class="text-xs text-slate-400 mb-1">Total cobrado</p>
        <p class="text-2xl font-bold text-success">Bs. {{ number_format($totalPagado, 2) }}</p>
    </div>
    <div class="card p-4 border-l-4 border-warning">
        <p class="text-xs text-slate-400 mb-1">Pendiente de cobro</p>
        <p class="text-2xl font-bold text-warning">Bs. {{ number_format($totalPendiente, 2) }}</p>
    </div>
    <div class="card p-4 border-l-4 border-error">
        <p class="text-xs text-slate-400 mb-1">Anulado</p>
        <p class="text-2xl font-bold text-error">Bs. {{ number_format($totalAnulado, 2) }}</p>
    </div>
</div>

{{-- Por método de pago --}}
@if($porMetodo->count())
<div class="grid grid-cols-3 gap-3 mb-4">
    @foreach($porMetodo as $metodo => $monto)
    <div class="card p-3 text-center">
        <p class="text-xs text-slate-400 mb-1">{{ ucfirst($metodo) }}</p>
        <p class="font-bold text-slate-700">Bs. {{ number_format($monto, 2) }}</p>
    </div>
    @endforeach
</div>
@endif

<div class="card px-4 pb-4 sm:px-5">
    <h3 class="text-sm font-medium text-slate-700 py-4">{{ $pagos->count() }} registros de pago</h3>
    @if($pagos->isEmpty())
        <p class="py-8 text-center text-sm text-slate-400">No hay pagos en el período seleccionado.</p>
    @else
    <div class="overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Código</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Fecha</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Método</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs text-right">Monto</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 lg:px-5 text-xs">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $pago)
                @php
                    $estadoColor = match($pago->estado_pago) {
                        'pagado'   => 'bg-success/10 text-success',
                        'anulado'  => 'bg-error/10 text-error',
                        default    => 'bg-warning/10 text-warning',
                    };
                @endphp
                <tr class="border-y border-transparent border-b-slate-200">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs font-mono text-slate-500">{{ $pago->codigo_pago }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-600">{{ $pago->fecha_pago?->format('d/m/Y H:i') ?? $pago->created_at->format('d/m/Y') }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-600">{{ $pago->cita?->paciente?->apellidos }}, {{ $pago->cita?->paciente?->nombres }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-500">{{ ucfirst($pago->metodo_pago) }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-right font-semibold text-slate-700">Bs. {{ number_format($pago->monto, 2) }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <span class="badge rounded-full {{ $estadoColor }} text-xs px-2.5 py-1">{{ ucfirst($pago->estado_pago) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-200">
                    <td colspan="4" class="px-3 py-3 sm:px-5 text-xs font-bold text-slate-700 text-right">TOTAL COBRADO:</td>
                    <td class="px-3 py-3 sm:px-5 text-right font-bold text-success">Bs. {{ number_format($totalPagado, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>
@endsection
