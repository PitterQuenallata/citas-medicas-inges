@extends('layouts.app')
@section('title', $especialidad->nombre_especialidad)

@section('content')
{{-- NUEVO: vista de detalle de especialidad --}}
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('especialidades.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Especialidades</span>
</div>

{{-- Cabecera --}}
<div class="card p-4 sm:p-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex size-14 shrink-0 items-center justify-center rounded-full bg-primary/10 dark:bg-accent/15">
                <svg class="size-7 text-primary dark:text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                    {{ $especialidad->nombre_especialidad }}
                </h2>
                @if($especialidad->descripcion)
                    <p class="mt-0.5 text-sm text-slate-400 dark:text-navy-300">{{ $especialidad->descripcion }}</p>
                @endif
                <div class="mt-1">
                    @if($especialidad->estado === 'activo')
                        <span class="badge rounded-full bg-success/10 text-success dark:bg-success/15">Activo</span>
                    @else
                        <span class="badge rounded-full bg-error/10 text-error dark:bg-error/15">Inactivo</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('especialidades.edit', $especialidad->id_especialidad) }}"
                class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
                Editar
            </a>
        </div>
    </div>
</div>

{{-- Métricas --}}
<div class="grid grid-cols-2 gap-4 mt-4 sm:grid-cols-3">
    <div class="card flex items-center gap-3 p-4">
        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10">
            <svg class="size-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">{{ $especialidad->medicos->count() }}</p>
            <p class="text-xs text-slate-400 dark:text-navy-300">Médicos</p>
        </div>
    </div>
    <div class="card flex items-center gap-3 p-4">
        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-success/10">
            <svg class="size-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">Bs. {{ number_format($especialidad->costo_consulta, 2) }}</p>
            <p class="text-xs text-slate-400 dark:text-navy-300">Costo consulta</p>
        </div>
    </div>
    <div class="card flex items-center gap-3 p-4">
        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-info/10">
            <svg class="size-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
        </div>
        <div>
            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                {{ $especialidad->medicos->where('estado', 'activo')->count() }}
            </p>
            <p class="text-xs text-slate-400 dark:text-navy-300">Médicos activos</p>
        </div>
    </div>
</div>

{{-- Lista de médicos asociados --}}
<div class="card mt-4 px-4 pb-4 sm:px-5">
    <div class="flex items-center justify-between py-4">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
            Médicos asociados
        </h3>
    </div>

    @if($especialidad->medicos->count())
        <div class="min-w-full overflow-x-auto">
            <table class="is-hoverable w-full text-left">
                <thead>
                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Código</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Nombre</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Matrícula</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Estado</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($especialidad->medicos as $medico)
                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                        <td class="whitespace-nowrap px-3 py-3 text-xs font-mono text-slate-500 dark:text-navy-300">
                            {{ $medico->codigo_medico }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-3 font-medium text-slate-700 dark:text-navy-100">
                            Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-3 text-slate-600 dark:text-navy-200">
                            {{ $medico->matricula_profesional }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-3">
                            @if($medico->estado === 'activo')
                                <span class="badge rounded-full bg-success/10 text-success">Activo</span>
                            @else
                                <span class="badge rounded-full bg-error/10 text-error">Inactivo</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-3">
                            <a href="{{ route('medicos.show', $medico->id_medico) }}"
                                class="btn size-8 rounded-full p-0 text-info hover:bg-info/10" title="Ver médico">
                                <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="py-6 text-center text-sm text-slate-400 dark:text-navy-300">
            No hay médicos asociados a esta especialidad.
        </p>
    @endif
</div>
@endsection
