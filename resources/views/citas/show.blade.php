@extends('layouts.app')
@section('title', 'Detalle Cita')

@section('content')
<div class="flex items-center justify-between pb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('citas.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
            <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-lg font-medium text-slate-700 dark:text-navy-100">Cita {{ $cita->codigo_cita }}</h2>
        <span class="badge rounded-full text-xs
            @if($cita->estado_cita === 'pendiente') bg-warning/10 text-warning
            @elseif($cita->estado_cita === 'confirmada') bg-info/10 text-info
            @elseif($cita->estado_cita === 'atendida') bg-success/10 text-success
            @elseif($cita->estado_cita === 'cancelada') bg-error/10 text-error
            @elseif($cita->estado_cita === 'reprogramada') bg-slate-100 text-slate-500 dark:bg-navy-500 dark:text-navy-200
            @elseif($cita->estado_cita === 'no_asistio') bg-warning/10 text-warning
            @else bg-slate-100 text-slate-600 @endif">
            {{ $cita->estado_label }}
        </span>
    </div>

    {{-- Acciones según estado --}}
    <div class="flex items-center gap-2">
        @if($cita->estado_cita === 'pendiente')
            <form method="POST" action="{{ route('citas.confirmar', $cita->id_cita) }}" class="swal-confirmar">
                @csrf @method('PATCH')
                <button type="submit" class="btn h-8 rounded-full bg-info px-4 text-xs font-medium text-white hover:bg-info/90">Confirmar</button>
            </form>
            <a href="{{ route('citas.edit', $cita->id_cita) }}" class="btn h-8 rounded-full border border-primary px-4 text-xs font-medium text-primary hover:bg-primary/10">Editar</a>
            <a href="{{ route('citas.reprogramar', $cita->id_cita) }}" class="btn h-8 rounded-full border border-slate-300 px-4 text-xs font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200">Reprogramar</a>
            <button type="button" class="btn h-8 rounded-full bg-error px-4 text-xs font-medium text-white hover:bg-error/90 btn-cancelar" data-id="{{ $cita->id_cita }}">Cancelar</button>
        @elseif($cita->estado_cita === 'confirmada')
            <form method="POST" action="{{ route('citas.atender', $cita->id_cita) }}" class="swal-atender">
                @csrf @method('PATCH')
                <button type="submit" class="btn h-8 rounded-full bg-success px-4 text-xs font-medium text-white hover:bg-success/90">Marcar Atendida</button>
            </form>
            <a href="{{ route('citas.reprogramar', $cita->id_cita) }}" class="btn h-8 rounded-full border border-slate-300 px-4 text-xs font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200">Reprogramar</a>
            <button type="button" class="btn h-8 rounded-full bg-error px-4 text-xs font-medium text-white hover:bg-error/90 btn-cancelar" data-id="{{ $cita->id_cita }}">Cancelar</button>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    {{-- Info principal --}}
    <div class="card px-4 py-4 sm:px-5">
        <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Datos de la cita</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Codigo</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $cita->codigo_cita }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Fecha</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $cita->fecha_cita->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Horario</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ substr($cita->hora_inicio,0,5) }} – {{ substr($cita->hora_fin,0,5) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Registrado por</span>
                <span class="text-sm text-slate-700 dark:text-navy-100">{{ $cita->usuarioRegistra?->nombre }} {{ $cita->usuarioRegistra?->apellido }}</span>
            </div>
            @if($cita->motivo_consulta)
            <div>
                <span class="text-sm text-slate-500 dark:text-navy-300">Motivo</span>
                <p class="mt-1 text-sm text-slate-700 dark:text-navy-100">{{ $cita->motivo_consulta }}</p>
            </div>
            @endif
            @if($cita->observaciones)
            <div>
                <span class="text-sm text-slate-500 dark:text-navy-300">Observaciones</span>
                <p class="mt-1 text-sm text-slate-700 dark:text-navy-100">{{ $cita->observaciones }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Paciente y Médico --}}
    <div class="space-y-4">
        <div class="card px-4 py-4 sm:px-5">
            <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Paciente</h3>
            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}</p>
            @if($cita->paciente?->ci)
                <p class="text-xs text-slate-400 mt-1">CI: {{ $cita->paciente->ci }}</p>
            @endif
            @if($cita->paciente?->telefono)
                <p class="text-xs text-slate-400">Tel: {{ $cita->paciente->telefono }}</p>
            @endif
        </div>

        <div class="card px-4 py-4 sm:px-5">
            <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Medico</h3>
            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">Dr(a). {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}</p>
            @if($cita->medico?->especialidades->count())
                <p class="text-xs text-slate-400 mt-1">{{ $cita->medico->especialidades->pluck('nombre_especialidad')->join(', ') }}</p>
            @endif
        </div>
    </div>
</div>

{{-- Info cancelación --}}
@if($cita->estado_cita === 'cancelada')
<div class="card mt-4 border border-error/20 bg-error/5 px-4 py-4 sm:px-5">
    <h3 class="text-sm font-semibold text-error mb-2">Cita cancelada</h3>
    @if($cita->fecha_cancelacion)
        <p class="text-xs text-slate-500">Fecha: {{ $cita->fecha_cancelacion->format('d/m/Y H:i') }}</p>
    @endif
    @if($cita->motivo_cancelacion)
        <p class="text-sm text-slate-700 dark:text-navy-100 mt-1">Motivo: {{ $cita->motivo_cancelacion }}</p>
    @endif
</div>
@endif

{{-- Info reprogramación --}}
@if($cita->estado_cita === 'reprogramada' && $cita->reprogramaciones->count())
<div class="card mt-4 border border-info/20 bg-info/5 px-4 py-4 sm:px-5">
    <h3 class="text-sm font-semibold text-info mb-2">Cita reprogramada</h3>
    @foreach($cita->reprogramaciones as $nueva)
        <a href="{{ route('citas.show', $nueva->id_cita) }}" class="text-sm text-info hover:underline">
            Ver nueva cita: {{ $nueva->codigo_cita }} ({{ $nueva->fecha_cita->format('d/m/Y') }} {{ substr($nueva->hora_inicio,0,5) }})
        </a>
    @endforeach
</div>
@endif

@if($cita->citaOriginal)
<div class="card mt-4 border border-slate-200 bg-slate-50 px-4 py-4 sm:px-5 dark:border-navy-500 dark:bg-navy-600">
    <h3 class="text-sm font-semibold text-slate-500 dark:text-navy-200 mb-2">Reprogramada desde</h3>
    <a href="{{ route('citas.show', $cita->citaOriginal->id_cita) }}" class="text-sm text-primary hover:underline dark:text-accent">
        {{ $cita->citaOriginal->codigo_cita }} ({{ $cita->citaOriginal->fecha_cita->format('d/m/Y') }})
    </a>
</div>
@endif

{{-- Form oculto para cancelar --}}
<form method="POST" id="form-cancelar" style="display:none">
    @csrf @method('PATCH')
    <input type="hidden" name="motivo_cancelacion" id="motivo_cancelacion_input">
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    @if(session('success'))
        Swal.fire({ title: 'Listo', text: @json(session('success')), icon: 'success', confirmButtonColor: '#4f46e5' });
    @endif
    @if(session('error'))
        Swal.fire({ title: 'Error', text: @json(session('error')), icon: 'error', confirmButtonColor: '#4f46e5' });
    @endif

    document.querySelectorAll('.swal-confirmar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Confirmar esta cita?',
                text: 'El estado cambiara a confirmada',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0ea5e9',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, confirmar',
                cancelButtonText: 'Volver'
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    });

    document.querySelectorAll('.swal-atender').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Marcar como atendida?',
                text: 'Confirma que el paciente fue atendido',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, atendida',
                cancelButtonText: 'Volver'
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    });

    document.querySelectorAll('.btn-cancelar').forEach(btn => {
        btn.addEventListener('click', function() {
            const citaId = this.dataset.id;
            Swal.fire({
                title: '¿Cancelar esta cita?',
                input: 'textarea',
                inputLabel: 'Motivo de cancelacion',
                inputPlaceholder: 'Escriba el motivo...',
                inputAttributes: { required: true },
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, cancelar cita',
                cancelButtonText: 'Volver',
                inputValidator: (value) => { if (!value) return 'Debe indicar un motivo'; }
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-cancelar');
                    form.action = `/citas/${citaId}/cancelar`;
                    document.getElementById('motivo_cancelacion_input').value = result.value;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
