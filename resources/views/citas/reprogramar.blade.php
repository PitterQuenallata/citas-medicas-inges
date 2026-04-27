@extends('layouts.app')
@section('title', 'Reprogramar Cita')

@section('content')
<div class="flex items-center justify-between pb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('citas.show', $cita->id_cita) }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
            <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-lg font-medium text-slate-700 dark:text-navy-100">Reprogramar Cita {{ $cita->codigo_cita }}</h2>
    </div>
</div>

{{-- Info cita original --}}
<div class="card mb-4 border border-slate-200 bg-slate-50 px-4 py-4 sm:px-5 dark:border-navy-500 dark:bg-navy-600">
    <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Cita original</h3>
    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 text-sm">
        <div>
            <span class="text-slate-400 dark:text-navy-300">Paciente</span>
            <p class="font-medium text-slate-700 dark:text-navy-100">{{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}</p>
        </div>
        <div>
            <span class="text-slate-400 dark:text-navy-300">Medico</span>
            <p class="font-medium text-slate-700 dark:text-navy-100">Dr(a). {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}</p>
        </div>
        <div>
            <span class="text-slate-400 dark:text-navy-300">Fecha</span>
            <p class="font-medium text-slate-700 dark:text-navy-100">{{ $cita->fecha_cita->format('d/m/Y') }}</p>
        </div>
        <div>
            <span class="text-slate-400 dark:text-navy-300">Horario</span>
            <p class="font-medium text-slate-700 dark:text-navy-100">{{ substr($cita->hora_inicio,0,5) }} – {{ substr($cita->hora_fin,0,5) }}</p>
        </div>
    </div>
</div>

@if($errors->has('disponibilidad'))
    <div class="mb-4 rounded-lg border border-error/30 bg-error/10 px-4 py-3 text-sm text-error">
        <strong>Conflicto de disponibilidad:</strong>
        <ul class="mt-1 list-disc pl-4">
            @foreach($errors->get('disponibilidad') as $err)
                @foreach((array)$err as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            @endforeach
        </ul>
    </div>
@endif

{{-- Form nueva fecha --}}
<div class="card px-4 py-5 sm:px-5">
    <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-4">Nueva fecha y horario</h3>
    <form method="POST" action="{{ route('citas.storeReprogramar', $cita->id_cita) }}" id="form-reprogramar">
        @csrf @method('PATCH')

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Nueva fecha <span class="text-error">*</span></span>
                <input type="date" name="fecha_cita" id="fecha_repro" required
                    value="{{ old('fecha_cita') }}" min="{{ date('Y-m-d') }}"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400" />
                @error('fecha_cita')
                    <span class="text-xs text-error">{{ $message }}</span>
                @enderror
            </label>

            <div class="sm:col-span-2">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Horario disponible <span class="text-error">*</span></span>
                <input type="hidden" name="hora_inicio" id="hora_inicio_repro" value="{{ old('hora_inicio') }}">
                <input type="hidden" name="hora_fin"    id="hora_fin_repro"    value="{{ old('hora_fin') }}">
                <div id="slots-repro-container" class="mt-2">
                    <div id="slots-repro-grid" class="flex flex-wrap gap-2"></div>
                    <div id="slots-repro-msg" class="mt-2 text-xs text-slate-400 dark:text-navy-300"></div>
                </div>
                @error('hora_inicio')
                    <span class="text-xs text-error mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('citas.show', $cita->id_cita) }}" class="btn border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500">
                Cancelar
            </a>
            <button type="submit" class="btn bg-primary font-medium text-white hover:bg-primary-focus dark:bg-accent dark:hover:bg-accent-focus">
                Reprogramar Cita
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    const medicoId = {{ $cita->id_medico }};
    const inpFecha = document.getElementById('fecha_repro');
    const inpHi    = document.getElementById('hora_inicio_repro');
    const inpHf    = document.getElementById('hora_fin_repro');
    const grid     = document.getElementById('slots-repro-grid');
    const msg      = document.getElementById('slots-repro-msg');

    inpFecha.addEventListener('change', async () => {
        grid.innerHTML = '';
        msg.textContent = '';
        inpHi.value = '';
        inpHf.value = '';

        if (!inpFecha.value) return;

        grid.innerHTML = '<span class="text-xs text-slate-400">Cargando horarios...</span>';

        try {
            const res   = await fetch(`/api/medicos/${medicoId}/slots?fecha=${inpFecha.value}`);
            const slots = await res.json();

            if (!slots.length) {
                grid.innerHTML = '';
                msg.textContent = 'El medico no tiene horario configurado para este dia.';
                return;
            }

            grid.innerHTML = '';
            slots.forEach(s => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = `${s.hora_inicio} – ${s.hora_fin}`;

                if (!s.disponible) {
                    btn.className = 'btn rounded-full border border-slate-200 px-3 py-1.5 text-xs text-slate-400 line-through cursor-not-allowed dark:border-navy-500 dark:text-navy-400';
                    btn.disabled = true;
                } else {
                    btn.className = 'btn rounded-full border border-primary px-3 py-1.5 text-xs text-primary hover:bg-primary hover:text-white dark:border-accent dark:text-accent transition-colors slot-repro';
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('.slot-repro').forEach(b => {
                            b.className = 'btn rounded-full border border-primary px-3 py-1.5 text-xs text-primary hover:bg-primary hover:text-white dark:border-accent dark:text-accent transition-colors slot-repro';
                        });
                        btn.className = 'btn rounded-full bg-primary px-3 py-1.5 text-xs text-white dark:bg-accent slot-repro';
                        inpHi.value = s.hora_inicio;
                        inpHf.value = s.hora_fin;
                        msg.textContent = '';
                    });
                }
                grid.appendChild(btn);
            });

            if (!slots.some(s => s.disponible)) {
                msg.textContent = 'No hay horarios disponibles para este dia.';
            }
        } catch (e) {
            grid.innerHTML = '';
            msg.textContent = 'Error al cargar horarios.';
        }
    });

    document.getElementById('form-reprogramar').addEventListener('submit', function(e) {
        if (!inpHi.value) {
            e.preventDefault();
            Swal.fire({ title: 'Seleccione un horario', text: 'Debe elegir un slot disponible', icon: 'warning', confirmButtonColor: '#4f46e5' });
        }
    });
})();
</script>
@endpush
