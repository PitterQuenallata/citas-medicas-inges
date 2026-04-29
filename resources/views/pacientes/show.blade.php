@extends('layouts.app')
@section('title', 'Detalle Paciente')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('pacientes.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Pacientes</span>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="card p-4 sm:p-5">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Información Personal</h3>
        <dl class="space-y-3">
            <div class="flex gap-2">
                <dt class="w-32 shrink-0 text-sm font-medium text-slate-500 dark:text-navy-300">Nombre:</dt>
                <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $paciente->nombres }} {{ $paciente->apellidos }}</dd>
            </div>
            <div class="flex gap-2">
                <dt class="w-32 shrink-0 text-sm font-medium text-slate-500 dark:text-navy-300">CI:</dt>
                <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $paciente->ci ?? '—' }}</dd>
            </div>
            <div class="flex gap-2">
                <dt class="w-32 shrink-0 text-sm font-medium text-slate-500 dark:text-navy-300">Teléfono:</dt>
                <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $paciente->telefono ?? '—' }}</dd>
            </div>
            <div class="flex gap-2">
                <dt class="w-32 shrink-0 text-sm font-medium text-slate-500 dark:text-navy-300">Estado:</dt>
                <dd>
                    @if($paciente->estado === 'activo')
                        <span class="badge rounded-full bg-success/10 text-success">Activo</span>
                    @else
                        <span class="badge rounded-full bg-error/10 text-error">Inactivo</span>
                    @endif
                </dd>
            </div>
            <div class="flex gap-2">
                <dt class="w-32 shrink-0 text-sm font-medium text-slate-500 dark:text-navy-300">Registrado:</dt>
                <dd class="text-sm text-slate-700 dark:text-navy-100">{{ $paciente->created_at?->format('d/m/Y') }}</dd>
            </div>
        </dl>
        @if(!auth()->user()->esMedico() || auth()->user()->esSuperAdmin())
        <div class="mt-6 flex gap-2">
            <a href="{{ route('pacientes.edit', $paciente->id_paciente) }}"
                class="btn bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
                Editar
            </a>
        </div>
        @endif
    </div>

    <div class="card p-4 sm:p-5">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Citas Recientes</h3>
        @php $citas = $paciente->citas()->latest('fecha_cita')->take(5)->get(); @endphp
        @if($citas->isEmpty())
            <p class="text-sm text-slate-400 dark:text-navy-300">Sin citas registradas.</p>
        @else
            <ul class="space-y-2">
                @foreach($citas as $cita)
                <li class="flex items-center justify-between rounded-lg border border-slate-150 p-3 dark:border-navy-500">
                    <div>
                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                            {{ $cita->fecha_cita ? \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') : '—' }}
                            {{ $cita->hora_inicio ? ' - ' . substr($cita->hora_inicio, 0, 5) : '' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-navy-300">
                            {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}
                        </p>
                    </div>
                    <span class="badge rounded-full text-xs
                        @if($cita->estado_cita === 'pendiente') bg-warning/10 text-warning
                        @elseif($cita->estado_cita === 'confirmada') bg-info/10 text-info
                        @elseif($cita->estado_cita === 'atendida') bg-success/10 text-success
                        @else bg-error/10 text-error @endif">
                        {{ ucfirst($cita->estado_cita) }}
                    </span>
                </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
