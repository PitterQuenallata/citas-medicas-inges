@extends('layouts.app')
@section('title', 'Detalle Médico')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('medicos.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Médicos</span>
</div>

{{-- Header con info principal --}}
<div class="card p-4 sm:p-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="flex size-16 shrink-0 items-center justify-center rounded-full bg-primary/10 dark:bg-accent/15">
                <svg class="size-8 text-primary dark:text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                    Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
                </h2>
                <p class="text-sm text-slate-400 dark:text-navy-300">
                    {{ $medico->codigo_medico }} &middot; {{ $medico->matricula_profesional }}
                </p>
                <div class="mt-1">
                    @if($medico->estado === 'activo')
                        <span class="badge rounded-full bg-success/10 text-success">Activo</span>
                    @else
                        <span class="badge rounded-full bg-error/10 text-error">Inactivo</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('medicos.edit', $medico->id_medico) }}"
                class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
                Editar
            </a>
            @if($medico->estado === 'activo')
            <form method="POST" action="{{ route('medicos.desactivar', $medico->id_medico) }}" onsubmit="return confirm('¿Desactivar este médico?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn h-9 border border-error px-4 text-sm font-medium text-error hover:bg-error/10">
                    Desactivar
                </button>
            </form>
            @else
            <form method="POST" action="{{ route('medicos.activar', $medico->id_medico) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn h-9 border border-success px-4 text-sm font-medium text-success hover:bg-success/10">
                    Activar
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- Estadísticas rápidas --}}
<div class="grid grid-cols-2 gap-4 mt-4 sm:grid-cols-4">
    <div class="card flex items-center gap-3 p-4">
        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10">
            <svg class="size-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">{{ $totalCitas }}</p>
            <p class="text-xs text-slate-400 dark:text-navy-300">Total citas</p>
        </div>
    </div>
    <div class="card flex items-center gap-3 p-4">
        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-warning/10">
            <svg class="size-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">{{ $citasHoy }}</p>
            <p class="text-xs text-slate-400 dark:text-navy-300">Citas hoy</p>
        </div>
    </div>
    <div class="card flex items-center gap-3 p-4">
        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-info/10">
            <svg class="size-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">{{ $citasPendientes }}</p>
            <p class="text-xs text-slate-400 dark:text-navy-300">Pendientes</p>
        </div>
    </div>
    <div class="card flex items-center gap-3 p-4">
        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-success/10">
            <svg class="size-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">{{ $citasAtendidas }}</p>
            <p class="text-xs text-slate-400 dark:text-navy-300">Atendidas</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 mt-4 lg:grid-cols-2">
    {{-- Información de contacto --}}
    <div class="card p-4 sm:p-5">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Información de Contacto</h3>
        <div class="space-y-3">
            <div class="flex justify-between border-b border-slate-100 pb-2 dark:border-navy-600">
                <span class="text-sm text-slate-400 dark:text-navy-300">CI</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $medico->ci ?? '—' }}</span>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2 dark:border-navy-600">
                <span class="text-sm text-slate-400 dark:text-navy-300">Teléfono</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $medico->telefono ?? '—' }}</span>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2 dark:border-navy-600">
                <span class="text-sm text-slate-400 dark:text-navy-300">Email</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $medico->email ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-400 dark:text-navy-300">Usuario vinculado</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">
                    {{ $medico->usuario?->nombre }} {{ $medico->usuario?->apellido ?? '—' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Especialidades --}}
    <div class="card p-4 sm:p-5">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Especialidades</h3>
        @if($medico->especialidades->count())
            <div class="flex flex-wrap gap-2">
                @foreach($medico->especialidades as $esp)
                    <span class="badge rounded-full bg-primary/10 px-3 py-1 text-sm text-primary dark:bg-accent/15 dark:text-accent-light">
                        {{ $esp->nombre_especialidad }}
                    </span>
                @endforeach
            </div>
        @else
            <p class="text-sm text-slate-400 dark:text-navy-300">Sin especialidades asignadas.</p>
        @endif
    </div>
</div>

{{-- Horarios --}}
<div class="card mt-4 p-4 sm:p-5">
    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Horarios de Atención</h3>
    @if($medico->horariosActivos->count())
        <div class="min-w-full overflow-x-auto">
            <table class="is-hoverable w-full text-left">
                <thead>
                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Día</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Hora Inicio</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Hora Fin</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Duración Cita</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medico->horariosActivos as $horario)
                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                        <td class="whitespace-nowrap px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-navy-100">
                            {{ $diasSemana[$horario->dia_semana] ?? 'Desconocido' }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600 dark:text-navy-200">{{ substr($horario->hora_inicio, 0, 5) }}</td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600 dark:text-navy-200">{{ substr($horario->hora_fin, 0, 5) }}</td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600 dark:text-navy-200">{{ $horario->duracion_cita_minutos }} min</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-sm text-slate-400 dark:text-navy-300 text-center py-4">Sin horarios activos configurados.</p>
    @endif
</div>

{{-- Últimas citas --}}
<div class="card mt-4 px-4 pb-4 sm:px-5">
    <div class="flex items-center py-4">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Últimas Citas</h3>
    </div>
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Fecha</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Hora</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medico->citas->sortByDesc('fecha_cita')->take(10) as $cita)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600">
                        {{ $cita->fecha_cita ? \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') : '—' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600">
                        {{ $cita->hora_inicio ? substr($cita->hora_inicio, 0, 5) : '—' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-700 dark:text-navy-100">
                        {{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-2.5">
                        <span class="badge rounded-full text-xs
                            @if($cita->estado_cita === 'pendiente') bg-warning/10 text-warning
                            @elseif($cita->estado_cita === 'confirmada') bg-info/10 text-info
                            @elseif($cita->estado_cita === 'atendida') bg-success/10 text-success
                            @else bg-error/10 text-error @endif">
                            {{ ucfirst($cita->estado_cita) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-3 py-6 text-center text-slate-400">Sin citas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
