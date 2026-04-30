@extends('layouts.app')
@section('title', 'Horarios — Dr. ' . $medico->nombres . ' ' . $medico->apellidos)

@section('content')
{{-- NUEVO: vista de horarios agrupados por día para un médico específico --}}
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('medicos.show', $medico->id_medico) }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver al perfil del médico</span>
</div>

{{-- Cabecera con info del médico --}}
<div class="card mb-4 p-4 sm:p-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-semibold text-slate-700 dark:text-navy-100">
                Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
            </h2>
            <p class="text-sm text-slate-400 dark:text-navy-300">
                {{ $medico->codigo_medico }} &middot; {{ $medico->matricula_profesional }}
            </p>
        </div>
        {{-- Botón para agregar nuevo horario pre-dirigido a este médico --}}
        <a href="{{ route('horarios.create', ['medico_id' => $medico->id_medico]) }}"
            class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
            + Nuevo Horario
        </a>
    </div>
</div>

@if($horariosPorDia->isEmpty())
    <div class="card p-8 text-center text-slate-400 dark:text-navy-300">
        <svg class="mx-auto mb-3 size-10 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm">Este médico no tiene horarios configurados.</p>
        <a href="{{ route('horarios.create', ['medico_id' => $medico->id_medico]) }}"
            class="btn mt-4 h-9 bg-primary px-5 text-sm font-medium text-white hover:bg-primary-focus">
            Agregar primer horario
        </a>
    </div>
@else
    {{-- Bloque por día --}}
    @foreach($diasSemana as $numDia => $nombreDia)
        @if($horariosPorDia->has($numDia))
        <div class="card mb-4 px-4 pb-4 sm:px-5">
            <div class="flex items-center justify-between py-4">
                <h3 class="flex items-center gap-2 text-sm font-semibold uppercase text-slate-700 dark:text-navy-100">
                    <span class="flex size-7 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary">
                        {{ $numDia }}
                    </span>
                    {{ $nombreDia }}
                    <span class="ml-1 text-xs font-normal text-slate-400">
                        ({{ $horariosPorDia[$numDia]->count() }} {{ $horariosPorDia[$numDia]->count() === 1 ? 'bloque' : 'bloques' }})
                    </span>
                </h3>
            </div>
            <div class="min-w-full overflow-x-auto">
                <table class="is-hoverable w-full text-left">
                    <thead>
                        <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                            <th class="whitespace-nowrap px-3 py-2 font-semibold uppercase text-xs text-slate-600 dark:text-navy-200">Inicio</th>
                            <th class="whitespace-nowrap px-3 py-2 font-semibold uppercase text-xs text-slate-600 dark:text-navy-200">Fin</th>
                            <th class="whitespace-nowrap px-3 py-2 font-semibold uppercase text-xs text-slate-600 dark:text-navy-200">Duración cita</th>
                            <th class="whitespace-nowrap px-3 py-2 font-semibold uppercase text-xs text-slate-600 dark:text-navy-200">Estado</th>
                            <th class="whitespace-nowrap px-3 py-2 font-semibold uppercase text-xs text-slate-600 dark:text-navy-200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($horariosPorDia[$numDia]->sortBy('hora_inicio') as $horario)
                        <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                            <td class="whitespace-nowrap px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-navy-100">
                                {{ substr($horario->hora_inicio, 0, 5) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600 dark:text-navy-200">
                                {{ substr($horario->hora_fin, 0, 5) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600 dark:text-navy-200">
                                {{ $horario->duracion_cita_minutos }} min
                            </td>
                            <td class="whitespace-nowrap px-3 py-2.5">
                                @if($horario->activo)
                                    <span class="badge rounded-full bg-success/10 text-success">Activo</span>
                                @else
                                    <span class="badge rounded-full bg-error/10 text-error">Inactivo</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-2.5">
                                <div class="flex gap-2">
                                    <a href="{{ route('horarios.edit', $horario->id_horario) }}"
                                        class="btn size-8 rounded-full p-0 text-primary hover:bg-primary/10" title="Editar">
                                        <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('horarios.destroy', $horario->id_horario) }}"
                                        x-data
                                        @submit.prevent="if(confirm('¿Eliminar el bloque {{ substr($horario->hora_inicio,0,5) }} - {{ substr($horario->hora_fin,0,5) }} del {{ $nombreDia }}?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn size-8 rounded-full p-0 text-error hover:bg-error/10" title="Eliminar">
                                            <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach
@endif
@endsection
