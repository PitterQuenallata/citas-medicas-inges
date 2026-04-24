@extends('layouts.app')
@section('title', 'Agenda Médica')

@section('content')
<div class="flex flex-wrap items-center gap-3 pb-4">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="medico_id" class="form-select h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            <option value="">Todos los médicos</option>
            @foreach($medicos as $m)
                <option value="{{ $m->id_medico }}" {{ $medicoId == $m->id_medico ? 'selected' : '' }}>
                    Dr. {{ $m->nombres }} {{ $m->apellidos }}
                </option>
            @endforeach
        </select>
        <input type="date" name="fecha" value="{{ $fecha }}"
            class="form-input h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
        <button type="submit" class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">Ver Agenda</button>
    </form>
    <span class="ml-auto text-sm text-slate-500 dark:text-navy-300">
        Fecha: <strong>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</strong>
    </span>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="flex items-center gap-2 py-4">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
            Citas del día
        </h3>
        <span class="badge rounded-full bg-primary/10 text-primary">{{ $citas->count() }} cita(s)</span>
    </div>
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Hora</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Médico</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Especialidad</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Motivo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($citas as $cita)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 font-medium text-slate-700 dark:text-navy-100">
                        {{ $cita->hora_inicio ? substr($cita->hora_inicio,0,5) : '—' }}
                        @if($cita->hora_fin) – {{ substr($cita->hora_fin,0,5) }} @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">
                        {{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">
                        Dr. {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}
                    </td>
                    <td class="px-3 py-3 sm:px-5 text-sm text-slate-500 dark:text-navy-300">
                        {{ $cita->medico?->especialidades->first()?->nombre_especialidad ?? '—' }}
                    </td>
                    <td class="px-3 py-3 sm:px-5 max-w-xs text-sm text-slate-500 truncate">{{ $cita->motivo_consulta ?? '—' }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <span class="badge rounded-full text-xs
                            @if($cita->estado_cita === 'pendiente') bg-warning/10 text-warning
                            @elseif($cita->estado_cita === 'confirmada') bg-info/10 text-info
                            @elseif($cita->estado_cita === 'atendida') bg-success/10 text-success
                            @elseif($cita->estado_cita === 'cancelada') bg-error/10 text-error
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ ucfirst($cita->estado_cita) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">
                    No hay citas para esta fecha{{ $medicoId ? ' y médico seleccionado' : '' }}.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
