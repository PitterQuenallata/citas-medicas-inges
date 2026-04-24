@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
    use App\Models\Paciente;
    use App\Models\Medico;
    use App\Models\Cita;
    $totalPacientes = Paciente::count();
    $totalMedicos   = Medico::where('estado','activo')->count();
    $totalCitas     = Cita::count();
    $citasHoy       = Cita::whereDate('fecha_cita', today())->count();
    $citasPendientes= Cita::where('estado_cita','pendiente')->count();
    $citasCanceladas= Cita::where('estado_cita','cancelada')->count();
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div class="card flex items-center gap-4 p-4 sm:p-5">
        <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-info/10 dark:bg-info/15">
            <svg class="size-7 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 21H7a4 4 0 01-4-4v-1a5 5 0 015-5h8a5 5 0 015 5v1a4 4 0 01-4 4zM12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
        </div>
        <div>
            <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $totalPacientes }}</p>
            <p class="text-sm text-slate-400 dark:text-navy-300">Pacientes registrados</p>
        </div>
    </div>

    <div class="card flex items-center gap-4 p-4 sm:p-5">
        <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-success/10 dark:bg-success/15">
            <svg class="size-7 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $totalMedicos }}</p>
            <p class="text-sm text-slate-400 dark:text-navy-300">Médicos activos</p>
        </div>
    </div>

    <div class="card flex items-center gap-4 p-4 sm:p-5">
        <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-primary/10 dark:bg-primary/15">
            <svg class="size-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $totalCitas }}</p>
            <p class="text-sm text-slate-400 dark:text-navy-300">Total citas</p>
        </div>
    </div>

    <div class="card flex items-center gap-4 p-4 sm:p-5">
        <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-warning/10 dark:bg-warning/15">
            <svg class="size-7 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $citasHoy }}</p>
            <p class="text-sm text-slate-400 dark:text-navy-300">Citas hoy</p>
        </div>
    </div>

    <div class="card flex items-center gap-4 p-4 sm:p-5">
        <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-secondary/10 dark:bg-secondary/15">
            <svg class="size-7 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $citasPendientes }}</p>
            <p class="text-sm text-slate-400 dark:text-navy-300">Citas pendientes</p>
        </div>
    </div>

    <div class="card flex items-center gap-4 p-4 sm:p-5">
        <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-error/10 dark:bg-error/15">
            <svg class="size-7 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $citasCanceladas }}</p>
            <p class="text-sm text-slate-400 dark:text-navy-300">Citas canceladas</p>
        </div>
    </div>
</div>

@php $ultimasCitas = Cita::with(['paciente','medico'])->latest('fecha_cita')->take(5)->get(); @endphp
<div class="card mt-4 px-4 pb-4 sm:px-5">
    <div class="flex items-center py-4">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Últimas citas registradas</h3>
        <a href="{{ route('citas.index') }}" class="ml-auto text-xs text-primary hover:underline">Ver todas</a>
    </div>
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Fecha</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Médico</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimasCitas as $cita)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600">{{ $cita->fecha_cita ? \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') : '—' }}</td>
                    <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-700">{{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}</td>
                    <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600">Dr. {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}</td>
                    <td class="whitespace-nowrap px-3 py-2.5">
                        <span class="badge rounded-full text-xs @if($cita->estado_cita==='pendiente') bg-warning/10 text-warning @elseif($cita->estado_cita==='confirmada') bg-info/10 text-info @elseif($cita->estado_cita==='atendida') bg-success/10 text-success @else bg-error/10 text-error @endif">
                            {{ ucfirst($cita->estado_cita) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-3 py-6 text-center text-slate-400">Sin citas registradas aún.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
