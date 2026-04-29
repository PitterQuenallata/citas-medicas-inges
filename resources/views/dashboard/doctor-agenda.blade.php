@extends('layouts.app')
@section('title', 'Mi Agenda')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
            Mi Agenda - Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
        </h2>
        <p class="mt-1 text-sm text-slate-400 dark:text-navy-300">
            Semana del {{ now()->startOfWeek()->format('d/m') }} al {{ now()->endOfWeek()->format('d/m/Y') }}
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn h-9 border border-slate-300 px-4 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200 dark:hover:bg-navy-600">
            <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('dashboard.analytics') }}" class="btn h-9 border border-slate-300 px-4 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200 dark:hover:bg-navy-600">
            Estadísticas
        </a>
    </div>
</div>

{{-- Agenda semanal --}}
<div class="card p-4 sm:p-5 mb-4">
    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Agenda Semanal</h3>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5 xl:grid-cols-7">
        @foreach($diasSemana as $numDia => $nombreDia)
        @if($numDia <= 7)
        @php
            $fechaDia = now()->startOfWeek()->addDays($numDia - 1);
            $esHoy = $fechaDia->isToday();
            $citasDelDia = $citasSemana->get($numDia, collect());
        @endphp
        <div class="rounded-lg border p-3 {{ $esHoy ? 'border-primary/40 bg-primary/5 dark:border-accent/40 dark:bg-accent/5' : 'border-slate-200 dark:border-navy-500' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase {{ $esHoy ? 'text-primary dark:text-accent' : 'text-slate-500 dark:text-navy-300' }}">
                    {{ $nombreDia }}
                </span>
                <span class="text-xs {{ $esHoy ? 'text-primary dark:text-accent' : 'text-slate-400 dark:text-navy-400' }}">
                    {{ $fechaDia->format('d/m') }}
                </span>
            </div>
            @if($citasDelDia->count())
                <div class="space-y-1.5">
                    @foreach($citasDelDia as $cita)
                    <div class="rounded bg-white p-2 text-xs shadow-sm dark:bg-navy-600
                        @if($cita->estado_cita === 'atendida') border-l-2 border-success
                        @elseif($cita->estado_cita === 'confirmada') border-l-2 border-info
                        @else border-l-2 border-warning @endif">
                        <p class="font-medium text-slate-700 dark:text-navy-100">{{ substr($cita->hora_inicio, 0, 5) }}</p>
                        <p class="text-slate-500 dark:text-navy-300 truncate">{{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-300 dark:text-navy-500 text-center py-2">Sin citas</p>
            @endif
        </div>
        @endif
        @endforeach
    </div>
</div>

{{-- Mis horarios de atención --}}
<div class="card p-4 sm:p-5 mb-4">
    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Mis Horarios de Atención</h3>
    @if($medico->horariosActivos->count())
    <div class="flex flex-wrap gap-3">
        @foreach($medico->horariosActivos->sortBy('dia_semana') as $horario)
        <div class="rounded-lg border border-slate-200 px-4 py-2 dark:border-navy-500">
            <p class="text-xs font-semibold text-primary dark:text-accent">{{ $diasSemana[$horario->dia_semana] ?? '' }}</p>
            <p class="text-sm text-slate-600 dark:text-navy-200">{{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}</p>
            <p class="text-xs text-slate-400 dark:text-navy-400">{{ $horario->duracion_cita_minutos }} min/cita</p>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-sm text-slate-400 dark:text-navy-300">No hay horarios configurados.</p>
    @endif
</div>

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    {{-- Próximas citas --}}
    <div class="card px-4 pb-4 sm:px-5">
        <div class="flex items-center py-4">
            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Próximas Citas</h3>
            <span class="ml-auto badge rounded-full bg-primary/10 px-2.5 py-0.5 text-xs text-primary dark:bg-accent/15 dark:text-accent-light">
                {{ $proximasCitas->count() }}
            </span>
        </div>
        <div class="space-y-3">
            @forelse($proximasCitas as $cita)
            <div class="flex items-center gap-3 rounded-lg border border-slate-100 p-3 dark:border-navy-600">
                <div class="flex flex-col items-center justify-center rounded-lg bg-slate-100 px-2.5 py-1.5 dark:bg-navy-600">
                    <span class="text-xs font-semibold text-primary dark:text-accent">{{ $cita->fecha_cita->format('d') }}</span>
                    <span class="text-[10px] uppercase text-slate-400 dark:text-navy-300">{{ $cita->fecha_cita->translatedFormat('M') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-700 dark:text-navy-100 truncate">
                        {{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}
                    </p>
                    <p class="text-xs text-slate-400 dark:text-navy-300">
                        {{ substr($cita->hora_inicio, 0, 5) }} &middot; {{ Str::limit($cita->motivo_consulta, 30) ?? 'Sin motivo' }}
                    </p>
                </div>
                <span class="badge rounded-full text-[10px]
                    @if($cita->estado_cita === 'pendiente') bg-warning/10 text-warning
                    @else bg-info/10 text-info @endif">
                    {{ ucfirst($cita->estado_cita) }}
                </span>
            </div>
            @empty
            <p class="text-sm text-slate-400 dark:text-navy-300 text-center py-4">No hay citas próximas.</p>
            @endforelse
        </div>
    </div>

    {{-- Historial reciente --}}
    <div class="card px-4 pb-4 sm:px-5">
        <div class="flex items-center py-4">
            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Historial Reciente</h3>
        </div>
        <div class="min-w-full overflow-x-auto">
            <table class="is-hoverable w-full text-left">
                <thead>
                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Fecha</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Paciente</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historialReciente as $cita)
                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                        <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600">{{ $cita->fecha_cita->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-700 dark:text-navy-100">
                            {{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-500">{{ substr($cita->hora_inicio, 0, 5) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-3 py-6 text-center text-slate-400">Sin historial reciente.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
