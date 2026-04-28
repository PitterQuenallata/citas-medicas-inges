@extends('layouts.app')
@section('title', 'Detalle Cita')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('citas.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Citas</span>
</div>

<div class="card p-4 sm:p-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">{{ $cita->codigo_cita }}</h3>
            <p class="text-sm text-slate-500 dark:text-navy-300">
                {{ $cita->fecha_cita?->format('d/m/Y') }}
                {{ $cita->hora_inicio ? substr($cita->hora_inicio, 0, 5) : '' }} - {{ $cita->hora_fin ? substr($cita->hora_fin, 0, 5) : '' }}
            </p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">
            <p class="text-xs uppercase text-slate-400">Paciente</p>
            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                {{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}
            </p>
            <p class="text-sm text-slate-500 dark:text-navy-300">CI: {{ $cita->paciente?->ci ?? '—' }}</p>
        </div>

        <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">
            <p class="text-xs uppercase text-slate-400">Médico</p>
            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                Dr. {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}
            </p>
            <p class="text-sm text-slate-500 dark:text-navy-300">Código: {{ $cita->medico?->codigo_medico ?? '—' }}</p>
        </div>

        <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">
            <p class="text-xs uppercase text-slate-400">Estado</p>
            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $cita->estado_label }}</p>
        </div>

        <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">
            <p class="text-xs uppercase text-slate-400">Registrada por</p>
            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                {{ $cita->usuarioRegistra?->nombre }} {{ $cita->usuarioRegistra?->apellido }}
            </p>
            <p class="text-sm text-slate-500 dark:text-navy-300">{{ $cita->created_at?->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">
            <p class="text-xs uppercase text-slate-400">Motivo</p>
            <p class="text-sm text-slate-700 dark:text-navy-100">{{ $cita->motivo_consulta ?? '—' }}</p>
        </div>
        <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">
            <p class="text-xs uppercase text-slate-400">Observaciones</p>
            <p class="text-sm text-slate-700 dark:text-navy-100">{{ $cita->observaciones ?? '—' }}</p>
        </div>
    </div>

    @if($cita->estado_cita === 'cancelada')
        <div class="mt-6 rounded-lg border border-error/30 bg-error/5 p-4">
            <p class="text-xs uppercase text-error">Cancelación</p>
            <p class="text-sm text-slate-700">{{ $cita->motivo_cancelacion ?? '—' }}</p>
            <p class="text-sm text-slate-500">{{ $cita->fecha_cancelacion?->format('d/m/Y H:i') }}</p>
        </div>
    @endif

    @if($cita->citaOriginal)
        <div class="mt-6 rounded-lg border border-slate-200 p-4 dark:border-navy-500">
            <p class="text-xs uppercase text-slate-400">Reprogramada desde</p>
            <a class="text-sm font-medium text-primary" href="{{ route('citas.show', $cita->citaOriginal->id_cita) }}">
                {{ $cita->citaOriginal->codigo_cita }}
            </a>
        </div>
    @endif

    <div class="mt-6 rounded-lg border border-slate-200 p-4 dark:border-navy-500">
        <h4 class="text-sm font-medium text-slate-700 dark:text-navy-100 mb-3">Historial clínico</h4>
        @if($cita->paciente)
            <a href="{{ route('historial.show', $cita->paciente->id_paciente) }}" class="btn bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
                Ver historial
            </a>
        @else
            <p class="text-sm text-slate-500 dark:text-navy-300">No se encontró paciente para esta cita.</p>
        @endif
    </div>
</div>
@endsection
