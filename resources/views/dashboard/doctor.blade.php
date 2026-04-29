@extends('layouts.app')
@section('title', $puedeSeleccionar ? 'Dashboard Medico' : 'Mi Dashboard')

@section('content')
{{-- Filtro de médico (admin/recepcionista) --}}
@include('dashboard._filtro-medico')

{{-- Bienvenida --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
            {{ $puedeSeleccionar ? 'Dashboard de' : 'Bienvenido,' }} Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
        </h2>
        <p class="mt-1 text-sm text-slate-400 dark:text-navy-300">
            {{ now()->translatedFormat('l, d \\d\\e F \\d\\e Y') }}
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('dashboard.analytics', $puedeSeleccionar ? ['medico_id' => $medico->id_medico] : []) }}" class="btn h-9 border border-slate-300 px-4 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200 dark:hover:bg-navy-600">
            <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Estadísticas
        </a>
        <a href="{{ route('dashboard.agenda', $puedeSeleccionar ? ['medico_id' => $medico->id_medico] : []) }}" class="btn h-9 border border-slate-300 px-4 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200 dark:hover:bg-navy-600">
            <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Agenda
        </a>
    </div>
</div>

{{-- Tarjetas de estadísticas --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
    <div class="card p-4">
        <div class="flex items-center gap-3">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                <svg class="size-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $totalCitasHoy }}</p>
                <p class="text-xs text-slate-400 dark:text-navy-300">Citas hoy</p>
            </div>
        </div>
    </div>
    <div class="card p-4">
        <div class="flex items-center gap-3">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-warning/10">
                <svg class="size-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $pendientesHoy }}</p>
                <p class="text-xs text-slate-400 dark:text-navy-300">Pendientes</p>
            </div>
        </div>
    </div>
    <div class="card p-4">
        <div class="flex items-center gap-3">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-success/10">
                <svg class="size-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $atendidosHoy }}</p>
                <p class="text-xs text-slate-400 dark:text-navy-300">Atendidos hoy</p>
            </div>
        </div>
    </div>
    <div class="card p-4">
        <div class="flex items-center gap-3">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-info/10">
                <svg class="size-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $totalPacientes }}</p>
                <p class="text-xs text-slate-400 dark:text-navy-300">Mis pacientes</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 mt-4 lg:grid-cols-3">
    {{-- Próxima cita destacada --}}
    <div class="card p-4 sm:p-5 lg:col-span-1">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Próxima Cita</h3>
        @if($proximaCita)
        <div class="rounded-lg border border-primary/20 bg-primary/5 p-4 dark:border-accent/20 dark:bg-accent/5">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex size-10 items-center justify-center rounded-full bg-primary/10 dark:bg-accent/15">
                    <svg class="size-5 text-primary dark:text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-700 dark:text-navy-100">
                        {{ $proximaCita->paciente?->nombres }} {{ $proximaCita->paciente?->apellidos }}
                    </p>
                    <p class="text-xs text-slate-400 dark:text-navy-300">
                        {{ $proximaCita->fecha_cita->format('d/m/Y') }} a las {{ substr($proximaCita->hora_inicio, 0, 5) }}
                    </p>
                </div>
            </div>
            @if($proximaCita->motivo_consulta)
            <p class="text-sm text-slate-500 dark:text-navy-200">
                <span class="font-medium">Motivo:</span> {{ Str::limit($proximaCita->motivo_consulta, 80) }}
            </p>
            @endif
            <div class="mt-3">
                <span class="badge rounded-full text-xs
                    @if($proximaCita->estado_cita === 'pendiente') bg-warning/10 text-warning
                    @else bg-info/10 text-info @endif">
                    {{ ucfirst($proximaCita->estado_cita) }}
                </span>
            </div>
        </div>
        @else
        <div class="flex flex-col items-center py-6 text-center">
            <svg class="size-12 text-slate-200 dark:text-navy-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm text-slate-400 dark:text-navy-300">No hay citas próximas</p>
        </div>
        @endif

        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-navy-600">
            <div class="flex justify-between text-sm">
                <span class="text-slate-400 dark:text-navy-300">Citas esta semana</span>
                <span class="font-semibold text-slate-700 dark:text-navy-100">{{ $citasSemana }}</span>
            </div>
        </div>
    </div>

    {{-- Citas del día --}}
    <div class="card px-4 pb-4 sm:px-5 lg:col-span-2">
        <div class="flex items-center justify-between py-4">
            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Citas de Hoy</h3>
            <span class="badge rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600 dark:bg-navy-600 dark:text-navy-200">
                {{ $totalCitasHoy }} citas
            </span>
        </div>
        <div class="min-w-full overflow-x-auto">
            <table class="is-hoverable w-full text-left">
                <thead>
                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Hora</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Paciente</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Motivo</th>
                        <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citasHoy as $cita)
                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                        <td class="whitespace-nowrap px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-navy-100">
                            {{ substr($cita->hora_inicio, 0, 5) }}
                            @if($cita->hora_fin)
                                - {{ substr($cita->hora_fin, 0, 5) }}
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600 dark:text-navy-200">
                            {{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}
                        </td>
                        <td class="px-3 py-2.5 text-sm text-slate-500 dark:text-navy-300 max-w-[200px] truncate">
                            {{ $cita->motivo_consulta ?? '—' }}
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
                    <tr><td colspan="4" class="px-3 py-8 text-center text-slate-400 dark:text-navy-300">No hay citas para hoy.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
