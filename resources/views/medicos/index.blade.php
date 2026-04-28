@extends('layouts.app')
@section('title', 'Médicos')

@section('content')

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
    class="alert mb-4 flex items-center justify-between rounded-lg border border-success/30 bg-success/10 px-4 py-3 text-success sm:px-5">
    <p>{{ session('success') }}</p>
    <button @click="show = false" class="btn size-7 rounded-full p-0 hover:bg-success/20">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif

<div class="flex items-center justify-between py-2 pb-4">
    <form method="GET" class="flex gap-2">
        <input type="text" name="busqueda" value="{{ request('busqueda') }}"
            placeholder="Buscar por nombre, código..."
            class="form-input h-9 w-72 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 placeholder-slate-400 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
        <select name="estado" class="form-select h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            <option value="">Todos</option>
            <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
        <button type="submit" class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">Buscar</button>
        @if(request('busqueda') || request('estado'))
            <a href="{{ route('medicos.index') }}" class="btn h-9 px-4 text-sm border border-slate-300 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Limpiar</a>
        @endif
    </form>
    <a href="{{ route('medicos.create') }}" class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
        + Nuevo Médico
    </a>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Código</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Nombre</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Especialidad</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Teléfono</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicos as $medico)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs font-mono text-slate-500 dark:text-navy-300">{{ $medico->codigo_medico }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 font-medium text-slate-700 dark:text-navy-100">
                        Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
                    </td>
                    <td class="px-3 py-3 sm:px-5 text-sm text-slate-600 dark:text-navy-200">
                        {{ $medico->especialidades->pluck('nombre_especialidad')->join(', ') ?: '—' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $medico->telefono ?? '—' }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        @if($medico->estado === 'activo')
                            <span class="badge rounded-full bg-success/10 text-success">Activo</span>
                        @else
                            <span class="badge rounded-full bg-error/10 text-error">Inactivo</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <div class="flex space-x-1">
                            <a href="{{ route('medicos.show', $medico->id_medico) }}"
                                class="btn size-8 rounded-full p-0 text-info hover:bg-info/10" title="Ver detalle">
                                <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('medicos.edit', $medico->id_medico) }}"
                                class="btn size-8 rounded-full p-0 text-primary hover:bg-primary/10" title="Editar">
                                <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($medico->estado === 'activo')
                            <form method="POST" action="{{ route('medicos.desactivar', $medico->id_medico) }}" class="inline" onsubmit="return confirm('¿Desactivar este médico?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn size-8 rounded-full p-0 text-error hover:bg-error/10" title="Desactivar">
                                    <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('medicos.activar', $medico->id_medico) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn size-8 rounded-full p-0 text-success hover:bg-success/10" title="Activar">
                                    <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-slate-400 dark:text-navy-300">No se encontraron médicos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($medicos->hasPages())
    <div class="mt-4">
        {{ $medicos->links() }}
    </div>
    @endif
</div>
@endsection
