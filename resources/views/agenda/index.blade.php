@extends('layouts.app')
@section('title', 'Agenda Médica')

@section('content')
<div class="flex flex-wrap items-center gap-3 pb-4">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="medico_id" class="form-select h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            <option value="">Todos los medicos</option>
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
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Citas del dia</h3>
        <span class="badge rounded-full bg-primary/10 text-primary">{{ $citas->count() }} cita(s)</span>
    </div>
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Hora</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Medico</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Especialidad</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Motivo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($citas as $cita)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 font-medium text-slate-700 dark:text-navy-100">
                        {{ $cita->hora_inicio ? substr($cita->hora_inicio,0,5) : '—' }}
                        @if($cita->hora_fin) – {{ substr($cita->hora_fin,0,5) }} @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-sm text-slate-600 dark:text-navy-200">
                        {{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-sm text-slate-600 dark:text-navy-200">
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
                            @elseif($cita->estado_cita === 'reprogramada') bg-slate-100 text-slate-500 dark:bg-navy-500 dark:text-navy-200
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ $cita->estado_label }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <div class="flex gap-1">
                            <a href="{{ route('citas.show', $cita->id_cita) }}" class="btn size-8 rounded-full p-0 text-slate-500 hover:bg-slate-100" title="Ver detalle">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if($cita->estado_cita === 'pendiente')
                            <form method="POST" action="{{ route('citas.confirmar', $cita->id_cita) }}" class="swal-confirmar-agenda">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn size-8 rounded-full p-0 text-info hover:bg-info/10" title="Confirmar">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                            @endif
                            @if($cita->estado_cita === 'confirmada')
                            <form method="POST" action="{{ route('citas.atender', $cita->id_cita) }}" class="swal-atender-agenda">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn size-8 rounded-full p-0 text-success hover:bg-success/10" title="Marcar atendida">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </button>
                            </form>
                            @endif
                            @if(!in_array($cita->estado_cita, ['cancelada','atendida','reprogramada']))
                            <button type="button" class="btn size-8 rounded-full p-0 text-error hover:bg-error/10 btn-cancelar-agenda" data-id="{{ $cita->id_cita }}" title="Cancelar">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">
                    No hay citas para esta fecha{{ $medicoId ? ' y medico seleccionado' : '' }}.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Form oculto cancelar agenda --}}
<form method="POST" id="form-cancelar-agenda" style="display:none">
    @csrf @method('PATCH')
    <input type="hidden" name="motivo_cancelacion" id="motivo_cancel_agenda">
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

    document.querySelectorAll('.swal-confirmar-agenda').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Confirmar cita?',
                icon: 'question', showCancelButton: true,
                confirmButtonColor: '#0ea5e9', cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, confirmar', cancelButtonText: 'Volver'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });

    document.querySelectorAll('.swal-atender-agenda').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Marcar como atendida?',
                icon: 'question', showCancelButton: true,
                confirmButtonColor: '#22c55e', cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, atendida', cancelButtonText: 'Volver'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });

    document.querySelectorAll('.btn-cancelar-agenda').forEach(btn => {
        btn.addEventListener('click', function() {
            const citaId = this.dataset.id;
            Swal.fire({
                title: '¿Cancelar esta cita?',
                input: 'textarea', inputLabel: 'Motivo de cancelacion',
                inputPlaceholder: 'Escriba el motivo...', inputAttributes: { required: true },
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#e11d48', cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, cancelar', cancelButtonText: 'Volver',
                inputValidator: (v) => { if (!v) return 'Debe indicar un motivo'; }
            }).then(r => {
                if (r.isConfirmed) {
                    const form = document.getElementById('form-cancelar-agenda');
                    form.action = `/citas/${citaId}/cancelar`;
                    document.getElementById('motivo_cancel_agenda').value = r.value;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
