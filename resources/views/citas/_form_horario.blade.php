@php
    $editFecha      = old('fecha_cita', $cita->fecha_cita?->format('Y-m-d'));
    $editHoraInicio = old('hora_inicio', $cita->hora_inicio ? substr($cita->hora_inicio, 0, 5) : '');
    $editHoraFin    = old('hora_fin', $cita->hora_fin ? substr($cita->hora_fin, 0, 5) : '');
@endphp

<input type="hidden" name="id_paciente" value="{{ $cita->id_paciente }}">
<input type="hidden" name="id_medico" value="{{ $cita->id_medico }}">
<input type="hidden" name="motivo_consulta" value="{{ old('motivo_consulta', $cita->motivo_consulta) }}">
<input type="hidden" name="observaciones" value="{{ old('observaciones', $cita->observaciones) }}">
<input type="hidden" name="estado_cita" value="{{ old('estado_cita', $cita->estado_cita) }}">

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <p class="text-sm font-medium text-slate-600 dark:text-navy-100">Paciente</p>
        <p class="mt-1 text-sm text-slate-700 dark:text-navy-100">
            {{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}
            @if($cita->paciente?->ci)
                <span class="text-slate-400">(CI: {{ $cita->paciente->ci }})</span>
            @endif
        </p>
    </div>

    <div class="sm:col-span-2">
        <p class="text-sm font-medium text-slate-600 dark:text-navy-100">Médico</p>
        <p class="mt-1 text-sm text-slate-700 dark:text-navy-100">
            Dr. {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}
            @if($cita->medico?->codigo_medico)
                <span class="text-slate-400">({{ $cita->medico->codigo_medico }})</span>
            @endif
        </p>
    </div>

    <label class="block sm:col-span-2">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Fecha de la cita <span class="text-error">*</span></span>
        <input type="date" name="fecha_cita" id="fecha_cita" value="{{ $editFecha }}" min="{{ date('Y-m-d') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('fecha_cita') border-error @enderror">
        @error('fecha_cita')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <div class="sm:col-span-2">
        <p class="text-sm font-medium text-slate-600 dark:text-navy-100">Horario disponible <span class="text-error">*</span></p>

        <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ $editHoraInicio }}" required>
        <input type="hidden" name="hora_fin" id="hora_fin" value="{{ $editHoraFin }}" required>

        <div id="slots-container" class="mt-2">
            <div id="slot-selected-label" class="mb-2"></div>
            <div id="slots-grid" class="flex flex-wrap gap-2"></div>
            <p id="slots-msg" class="mt-2 text-xs text-slate-500"></p>
        </div>

        @error('hora_inicio')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
        @error('hora_fin')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>
</div>

@push('scripts')
<script>
(function () {
    const inpFecha  = document.getElementById('fecha_cita');
    const inpHi     = document.getElementById('hora_inicio');
    const inpHf     = document.getElementById('hora_fin');
    const slotsGrid = document.getElementById('slots-grid');
    const slotsMsg  = document.getElementById('slots-msg');
    const medicoId  = {{ (int) $cita->id_medico }};
    const excluirId = {{ (int) $cita->id_cita }};

    inpFecha.addEventListener('change', () => {
        resetSlots();
        if (inpFecha.value) cargarSlots();
    });

    async function cargarSlots() {
        slotsGrid.innerHTML = '<span class="text-xs text-slate-500">Cargando horarios…</span>';
        slotsMsg.textContent = '';

        const params = new URLSearchParams({ fecha: inpFecha.value, excluir_cita_id: excluirId });

        try {
            const res   = await fetch(`/api/medicos/${medicoId}/slots?${params}`);
            const slots = await res.json();

            if (!slots.length) {
                slotsGrid.innerHTML = '';
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
                btn.classList.add('bg-error/10', 'text-error', 'border-error/30', 'line-through', 'cursor-not-allowed', 'opacity-70');
                btn.title = 'Horario ocupado';
            } else {
                btn.classList.add('bg-success/10', 'text-success', 'border-success/30', 'hover:bg-success/20');
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
        document.querySelectorAll('.slot-btn.slot-seleccionado').forEach(b => {
            b.classList.remove('slot-seleccionado');
            b.classList.remove('bg-primary', 'text-white', 'border-primary');
        });

        btn.classList.add('slot-seleccionado');
        btn.classList.add('bg-primary', 'text-white', 'border-primary');
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

    if (inpFecha.value) {
        cargarSlots();
    }
})();
</script>
@endpush
