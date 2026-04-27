@extends('layouts.app')
@section('title', 'Reprogramar Cita')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('citas.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Citas</span>
</div>

<div class="card p-4 sm:p-5">
    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-2">Reprogramar Cita</h3>
    <p class="text-sm text-slate-500 dark:text-navy-300 mb-6">
        {{ $cita->codigo_cita }} — Dr. {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}
    </p>

    @if($errors->has('disponibilidad'))
        <div class="mb-4 rounded-lg border border-error/30 bg-error/5 p-4">
            <p class="text-sm font-medium text-error">Conflicto de disponibilidad:</p>
            <ul class="mt-2 list-disc pl-5 text-sm text-slate-700">
                @foreach($errors->get('disponibilidad') as $err)
                    @foreach((array)$err as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500 mb-6">
        <p class="text-xs uppercase text-slate-400">Cita actual</p>
        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
            {{ $cita->fecha_cita?->format('d/m/Y') }}
            {{ $cita->hora_inicio ? substr($cita->hora_inicio, 0, 5) : '' }} - {{ $cita->hora_fin ? substr($cita->hora_fin, 0, 5) : '' }}
        </p>
    </div>

    <form method="POST" action="{{ route('citas.storeReprogramar', $cita->id_cita) }}">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Nueva fecha <span class="text-error">*</span></span>
                <input type="date" name="fecha_cita" id="fecha_cita" value="{{ old('fecha_cita') }}" min="{{ date('Y-m-d') }}" required
                    class="form-input mt-1.5 h-9 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('fecha_cita') border-error @enderror" />
                @error('fecha_cita')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
        </div>

        <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio') }}" required>
        <input type="hidden" name="hora_fin" id="hora_fin" value="{{ old('hora_fin') }}" required>

        <div class="mt-4">
            <p class="text-sm font-medium text-slate-600 dark:text-navy-100">Horario disponible <span class="text-error">*</span></p>
            <div id="slot-selected-label" class="mt-2"></div>
            <div id="slots-grid" class="mt-2 flex flex-wrap gap-2"></div>
            <p id="slots-msg" class="mt-2 text-sm text-slate-500"></p>
            @error('hora_inicio')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn bg-primary px-5 text-sm font-medium text-white hover:bg-primary-focus">
                Reprogramar
            </button>
            <a href="{{ route('citas.show', $cita->id_cita) }}" class="btn border border-slate-300 px-5 text-sm font-medium hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                Cancelar
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const medicoId  = {{ (int) $cita->id_medico }};
    const inpFecha  = document.getElementById('fecha_cita');
    const inpHi     = document.getElementById('hora_inicio');
    const inpHf     = document.getElementById('hora_fin');
    const slotsGrid = document.getElementById('slots-grid');
    const slotsMsg  = document.getElementById('slots-msg');

    inpFecha.addEventListener('change', () => {
        resetSlots();
        if (inpFecha.value) cargarSlots();
    });

    async function cargarSlots() {
        slotsGrid.innerHTML = '<span class="text-muted small">Cargando horarios…</span>';
        slotsMsg.textContent = '';

        try {
            const res   = await fetch(`/api/medicos/${medicoId}/slots?` + new URLSearchParams({ fecha: inpFecha.value }));
            const slots = await res.json();

            if (!slots.length) {
                slotsGrid.innerHTML  = '';
                slotsMsg.textContent = 'El médico no tiene horario configurado para este día.';
                return;
            }

            renderSlots(slots);
        } catch (e) {
            slotsGrid.innerHTML = '';
            slotsMsg.textContent = 'Error al cargar los horarios. Intente de nuevo.';
        }
    }

    function renderSlots(slots) {
        slotsGrid.innerHTML = '';
        const preHi = inpHi.value;

        slots.forEach(s => {
            const btn = document.createElement('button');
            btn.type  = 'button';
            btn.textContent = `${s.hora_inicio} – ${s.hora_fin}`;
            btn.className   = 'h-9 px-3 text-xs font-medium rounded-lg border slot-btn';

            if (!s.disponible) {
                btn.disabled = true;
                btn.classList.add('bg-error/10', 'text-error', 'border-error/30', 'line-through', 'cursor-not-allowed', 'opacity-70', 'slot-ocupado');
            } else {
                btn.classList.add('bg-success/10', 'text-success', 'border-success/30', 'hover:bg-success/20', 'slot-libre');
                btn.addEventListener('click', () => seleccionarSlot(btn, s.hora_inicio, s.hora_fin));

                if (preHi && preHi === s.hora_inicio) {
                    seleccionarSlot(btn, s.hora_inicio, s.hora_fin);
                }
            }

            slotsGrid.appendChild(btn);
        });

        if (!slots.some(s => s.disponible)) {
            slotsMsg.textContent = 'No hay horarios disponibles para este día.';
        }
    }

    function seleccionarSlot(btn, hi, hf) {
        document.querySelectorAll('.slot-btn.slot-seleccionado').forEach(b => b.classList.remove('slot-seleccionado'));
        btn.classList.add('slot-seleccionado');
        document.querySelectorAll('.slot-btn.slot-seleccionado').forEach(b => b.classList.add('bg-primary', 'text-white', 'border-primary'));
        inpHi.value = hi;
        inpHf.value = hf;
        slotsMsg.textContent = '';

        const label = document.getElementById('slot-selected-label');
        if (label) {
            label.innerHTML = `<span class="badge rounded-full bg-info/10 text-info dark:bg-info/15">✓ ${hi} – ${hf}</span>`;
        }
    }

    function resetSlots() {
        slotsGrid.innerHTML = '';
        slotsMsg.textContent = '';
        inpHi.value = '';
        inpHf.value = '';

        const label = document.getElementById('slot-selected-label');
        if (label) label.innerHTML = '';
    }
})();
</script>
@endpush
@endsection
