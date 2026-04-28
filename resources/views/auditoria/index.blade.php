@extends('layouts.app')
@section('title', 'Registro de Auditoría')

@section('content')
<div class="flex justify-end mb-4">
    <a href="{{ route('auditoria.pdf') }}?{{ http_build_query(request()->all()) }}"
       class="btn bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus flex items-center gap-2">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Exportar PDF
    </a>
</div>

{{-- Resumen rápido --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-4">
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-slate-700">{{ number_format($totalRegistros) }}</p>
        <p class="text-xs text-slate-400 mt-1">Total registros</p>
    </div>
    @foreach(['crear' => 'success', 'editar' => 'info', 'eliminar' => 'error'] as $accion => $color)
    <div class="card p-4 text-center">
        <p class="text-2xl font-bold text-{{ $color }}">{{ $accionesPorTipo[$accion] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ ucfirst($accion) }}</p>
    </div>
    @endforeach
</div>

{{-- Filtros --}}
<div class="card mb-4">
    {{-- Header de la card --}}
    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-navy-500 sm:px-5">
        <div class="flex items-center gap-2">
            <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">Filtrar registros</h3>
        </div>
        @if(request()->hasAny(['fecha_desde','fecha_hasta','id_usuario','accion','tabla']))
            <span class="badge rounded-full bg-primary/10 px-2.5 py-1 text-xs text-primary">
                Filtros activos
            </span>
        @endif
    </div>

    {{-- Campos --}}
    <form method="GET" action="{{ route('auditoria.index') }}" class="p-4 sm:p-5">
        <div class="flex flex-wrap gap-3">

            {{-- Desde --}}
            <div class="flex flex-col gap-1 min-w-[140px] flex-1">
                <label class="text-xs font-medium text-slate-600 dark:text-navy-100">
                    <span class="flex items-center gap-1">
                        <svg class="size-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Desde
                    </span>
                </label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                    class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            </div>

            {{-- Hasta --}}
            <div class="flex flex-col gap-1 min-w-[140px] flex-1">
                <label class="text-xs font-medium text-slate-600 dark:text-navy-100">
                    <span class="flex items-center gap-1">
                        <svg class="size-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Hasta
                    </span>
                </label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                    class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            </div>

            {{-- Usuario --}}
            <div class="flex flex-col gap-1 min-w-[160px] flex-1">
                <label class="text-xs font-medium text-slate-600 dark:text-navy-100">
                    <span class="flex items-center gap-1">
                        <svg class="size-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Usuario
                    </span>
                </label>
                <select name="id_usuario"
                    class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" @selected(request('id_usuario') == $u->id)>
                            {{ $u->nombre }} {{ $u->apellido }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Acción --}}
            <div class="flex flex-col gap-1 min-w-[130px] flex-1">
                <label class="text-xs font-medium text-slate-600 dark:text-navy-100">
                    <span class="flex items-center gap-1">
                        <svg class="size-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Acción
                    </span>
                </label>
                <select name="accion"
                    class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                    <option value="">Todas las acciones</option>
                    @foreach($accionesUnicas as $a)
                        <option value="{{ $a }}" @selected(request('accion') == $a)>{{ ucfirst($a) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tabla --}}
            <div class="flex flex-col gap-1 min-w-[150px] flex-1">
                <label class="text-xs font-medium text-slate-600 dark:text-navy-100">
                    <span class="flex items-center gap-1">
                        <svg class="size-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18"/></svg>
                        Tabla
                    </span>
                </label>
                <select name="tabla"
                    class="form-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                    <option value="">Todas las tablas</option>
                    @foreach($tablasUnicas as $t)
                        <option value="{{ $t }}" @selected(request('tabla') == $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Botones alineados al fondo de la fila --}}
            <div class="flex items-end gap-2 flex-shrink-0">
                <button type="submit"
                    class="btn bg-primary px-4 py-2 text-xs font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 flex items-center gap-1.5">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Buscar
                </button>
                <a href="{{ route('auditoria.index') }}"
                   class="btn border border-slate-300 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200 dark:hover:bg-navy-600 flex items-center gap-1.5">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Limpiar
                </a>
            </div>

        </div>
    </form>
</div>


{{-- Tabla de auditoría con modal Alpine.js --}}
<div class="card px-4 pb-4 sm:px-5" x-data="{ modalAbierto: false, registro: null }">

    <div class="flex items-center justify-between py-4">
        <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100">
            {{ number_format($registros->total()) }} registros totales
        </h3>
        <span class="text-xs text-slate-400">Página {{ $registros->currentPage() }} de {{ $registros->lastPage() }}</span>
    </div>

    @if($registros->isEmpty())
        <p class="py-8 text-center text-sm text-slate-400">No hay registros de auditoría con los filtros seleccionados.</p>
    @else
    <div class="overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">Fecha / Hora</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">Usuario</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">Acción</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">Tabla</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs text-center">Registro ID</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs">IP</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5 text-xs text-center">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $r)
                @php
                    $accionColor = match($r->accion) {
                        'crear'    => 'bg-success/10 text-success',
                        'editar'   => 'bg-info/10 text-info',
                        'eliminar' => 'bg-error/10 text-error',
                        default    => 'bg-slate-100 text-slate-600',
                    };
                    $datosAntes  = $r->datos_anteriores ? json_decode($r->datos_anteriores, true) : null;
                    $datosDespues = $r->datos_nuevos ? json_decode($r->datos_nuevos, true) : null;
                @endphp
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <p class="text-xs font-medium text-slate-700 dark:text-navy-100">{{ $r->created_at->format('d/m/Y') }}</p>
                        <p class="text-xs text-slate-400">{{ $r->created_at->format('H:i:s') }}</p>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        @if($r->usuario)
                            <p class="text-xs font-medium text-slate-700 dark:text-navy-100">{{ $r->usuario->nombre }} {{ $r->usuario->apellido }}</p>
                            <p class="text-xs text-slate-400">{{ $r->usuario->email }}</p>
                        @else
                            <span class="text-xs text-slate-400">Sistema</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <span class="badge rounded-full {{ $accionColor }} text-xs px-2.5 py-1">{{ ucfirst($r->accion) }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs font-mono text-slate-500 dark:text-navy-300">{{ $r->tabla }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center text-xs text-slate-500">{{ $r->registro_id ?? '—' }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs text-slate-400 font-mono">{{ $r->ip ?? '—' }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-center">
                        @if($datosAntes || $datosDespues)
                        <button type="button"
                            @click="registro = {{ json_encode(['accion' => $r->accion, 'tabla' => $r->tabla, 'id' => $r->registro_id, 'fecha' => $r->created_at->format('d/m/Y H:i'), 'usuario' => $r->usuario ? $r->usuario->nombre.' '.$r->usuario->apellido : 'Sistema', 'antes' => $datosAntes, 'despues' => $datosDespues]) }}; modalAbierto = true"
                            class="btn size-7 rounded-full border border-slate-300 p-0 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                            <svg class="size-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        @else
                            <span class="text-xs text-slate-300">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $registros->links() }}
    </div>

    {{-- Modal detalle — patrón Line One con x-teleport --}}
    <template x-teleport="#x-teleport-target">
        <div
            class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
            x-show="modalAbierto"
            role="dialog"
            @keydown.window.escape="modalAbierto = false"
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300"
                @click="modalAbierto = false"
                x-show="modalAbierto"
                x-transition:enter="ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            {{-- Panel --}}
            <div
                class="relative flex w-full max-w-2xl flex-col overflow-hidden rounded-lg bg-white transition-opacity duration-300 dark:bg-navy-700"
                x-show="modalAbierto"
                x-transition:enter="ease-out"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-navy-500">
                    <div>
                        <h3 class="font-semibold text-slate-700 dark:text-navy-100">Detalle de auditoría</h3>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="registro ? registro.tabla + ' · ' + registro.fecha : ''"></p>
                    </div>
                    <button @click="modalAbierto = false" class="btn size-8 rounded-full border border-slate-300 p-0 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-500">
                        <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Info básica --}}
                <div class="px-5 py-3 bg-slate-50 dark:bg-navy-800 flex gap-4 flex-wrap border-b border-slate-200 dark:border-navy-500">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400">Acción:</span>
                        <span class="badge rounded-full text-xs px-2 py-0.5"
                            :class="{
                                'bg-success/10 text-success': registro && registro.accion === 'crear',
                                'bg-info/10 text-info':       registro && registro.accion === 'editar',
                                'bg-error/10 text-error':     registro && registro.accion === 'eliminar',
                                'bg-slate-100 text-slate-600': registro && !['crear','editar','eliminar'].includes(registro.accion),
                            }"
                            x-text="registro ? registro.accion : ''"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400">Registro ID:</span>
                        <span class="text-xs font-mono font-semibold text-slate-700 dark:text-navy-100" x-text="registro ? registro.id || '—' : ''"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400">Usuario:</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-navy-100" x-text="registro ? registro.usuario : ''"></span>
                    </div>
                </div>

                {{-- Diff antes / después --}}
                <div class="overflow-y-auto max-h-[60vh] p-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div x-show="registro && registro.antes">
                            <h4 class="text-xs font-semibold uppercase text-slate-500 mb-2 flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-error inline-block"></span>
                                Antes
                            </h4>
                            <pre class="text-xs bg-red-50 border border-red-100 rounded-lg p-3 overflow-x-auto text-red-800 whitespace-pre-wrap dark:bg-navy-800 dark:text-red-300 dark:border-red-900/30"
                                 x-text="registro && registro.antes ? JSON.stringify(registro.antes, null, 2) : ''"></pre>
                        </div>
                        <div x-show="registro && registro.despues">
                            <h4 class="text-xs font-semibold uppercase text-slate-500 mb-2 flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-success inline-block"></span>
                                Después
                            </h4>
                            <pre class="text-xs bg-green-50 border border-green-100 rounded-lg p-3 overflow-x-auto text-green-800 whitespace-pre-wrap dark:bg-navy-800 dark:text-green-300 dark:border-green-900/30"
                                 x-text="registro && registro.despues ? JSON.stringify(registro.despues, null, 2) : ''"></pre>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-5 py-3 border-t border-slate-200 dark:border-navy-500 flex justify-end">
                    <button @click="modalAbierto = false" class="btn border border-slate-300 px-5 text-sm hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Cerrar</button>
                </div>
            </div>
        </div>
    </template>

    @endif
</div>
@endsection
