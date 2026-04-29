@extends('layouts.app')
@section('title', 'Citas')

@section('content')
<div class="flex items-center justify-between py-2 pb-4">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="estado" class="form-select h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            <option value="">Todos los estados</option>
            @foreach(['pendiente','confirmada','atendida','cancelada','reprogramada','no_asistio'] as $est)
                <option value="{{ $est }}" {{ request('estado') === $est ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$est)) }}</option>
            @endforeach
        </select>
        <select name="medico" class="form-select h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            <option value="">Todos los medicos</option>
            @foreach($medicos as $m)
                <option value="{{ $m->id_medico }}" {{ request('medico') == $m->id_medico ? 'selected' : '' }}>
                    Dr. {{ $m->nombres }} {{ $m->apellidos }}
                </option>
            @endforeach
        </select>
        <select name="especialidad" class="form-select h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            <option value="">Todas las especialidades</option>
            @foreach($especialidades as $e)
                <option value="{{ $e->id_especialidad }}" {{ request('especialidad') == $e->id_especialidad ? 'selected' : '' }}>
                    {{ $e->nombre_especialidad }}
                </option>
            @endforeach
        </select>
        <input type="date" name="fecha" value="{{ request('fecha') }}"
            class="form-input h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar paciente..."
            class="form-input h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
        <button type="submit" class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">Filtrar</button>
    </form>
    <a href="{{ route('citas.create') }}" class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
        + Nueva Cita
    </a>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Nro</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Codigo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Fecha / Hora</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Medico</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Motivo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Pago</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($citas as $cita)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 font-medium text-slate-700 dark:text-navy-100">{{ $citas->firstItem() + $loop->index }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs font-mono text-slate-500 dark:text-navy-300">{{ $cita->codigo_cita }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $cita->fecha_cita ? $cita->fecha_cita->format('d/m/Y') : '—' }}</p>
                        <p class="text-xs text-slate-400">{{ $cita->hora_inicio ? substr($cita->hora_inicio,0,5) : '' }} – {{ $cita->hora_fin ? substr($cita->hora_fin,0,5) : '' }}</p>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-sm text-slate-600 dark:text-navy-200">
                        {{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-sm text-slate-600 dark:text-navy-200">
                        Dr. {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}
                    </td>
                    <td class="px-3 py-3 sm:px-5 max-w-xs text-sm text-slate-600 dark:text-navy-200 truncate">{{ $cita->motivo_consulta ?? '—' }}</td>
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
                        @if($cita->pago)
                            <span class="badge rounded-full text-xs {{ $cita->pago->badge_class }}">
                                {{ $cita->pago->estado_label }}
                            </span>
                        @else
                            <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <div class="flex gap-1">
                            <a href="{{ route('citas.show', $cita->id_cita) }}" class="btn size-8 rounded-full p-0 text-slate-500 hover:bg-slate-100" title="Ver detalle">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if(!in_array($cita->estado_cita, ['cancelada','atendida','reprogramada']))
                            <a href="{{ route('citas.edit', $cita->id_cita) }}" class="btn size-8 rounded-full p-0 text-primary hover:bg-primary/10" title="Editar">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <button type="button" class="btn size-8 rounded-full p-0 text-error hover:bg-error/10 btn-cancelar-tabla" data-id="{{ $cita->id_cita }}" title="Cancelar">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-5 py-8 text-center text-slate-400">No se encontraron citas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $citas->links() }}
    </div>
</div>

{{-- Form oculto para cancelar desde tabla --}}
<form method="POST" id="form-cancelar-tabla" style="display:none">
    @csrf @method('PATCH')
    <input type="hidden" name="motivo_cancelacion" id="motivo_cancel_tabla">
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

    document.querySelectorAll('.btn-cancelar-tabla').forEach(btn => {
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
                    const form = document.getElementById('form-cancelar-tabla');
                    form.action = `/citas/${citaId}/cancelar`;
                    document.getElementById('motivo_cancel_tabla').value = result.value;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
