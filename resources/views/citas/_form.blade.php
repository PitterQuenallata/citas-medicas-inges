{{-- ============================================================
     Partial: _form.blade.php (Line One / TailwindCSS)
     Variables esperadas: $pacientes, $especialidades
     Opcional: $cita (para edición, incluye medico.especialidades cargado)
     ============================================================ --}}

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

@php
    $editMedicoId       = old('id_medico',   isset($cita) ? $cita->id_medico : '');
    $editFecha          = old('fecha_cita',  isset($cita) ? $cita->fecha_cita->format('Y-m-d') : '');
    $editHoraInicio     = old('hora_inicio', isset($cita) ? substr($cita->hora_inicio, 0, 5) : '');
    $editHoraFin        = old('hora_fin',    isset($cita) ? substr($cita->hora_fin,    0, 5) : '');
    $editEspId          = isset($cita) ? ($cita->medico->especialidades->first()->id_especialidad ?? '') : '';
    $excluirCitaId      = isset($cita) ? $cita->id_cita : null;
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

    {{-- Paciente --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Paciente <span class="text-error">*</span></span>
        <select name="id_paciente" id="id_paciente" required
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400">
            <option value="">— Seleccionar paciente —</option>
            @foreach($pacientes as $pac)
                <option value="{{ $pac->id_paciente }}"
                    {{ old('id_paciente', $cita->id_paciente ?? '') == $pac->id_paciente ? 'selected' : '' }}>
                    {{ $pac->apellidos }}, {{ $pac->nombres }}
                    @if($pac->ci) ({{ $pac->ci }}) @endif
                </option>
            @endforeach
        </select>
        @error('id_paciente')
            <span class="text-xs text-error">{{ $message }}</span>
        @enderror
    </label>

    {{-- Especialidad --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Especialidad <span class="text-error">*</span></span>
        <select id="id_especialidad"
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400">
            <option value="">— Seleccionar especialidad —</option>
            @foreach($especialidades as $esp)
                <option value="{{ $esp->id_especialidad }}"
                    {{ $editEspId == $esp->id_especialidad ? 'selected' : '' }}>
                    {{ $esp->nombre_especialidad }}
                </option>
            @endforeach
        </select>
        <span id="esp-msg" class="hidden text-xs text-warning mt-1">Sin medicos activos para esta especialidad.</span>
    </label>

    {{-- Médico (AJAX) --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Medico <span class="text-error">*</span></span>
        <select name="id_medico" id="id_medico" required {{ !$editMedicoId ? 'disabled' : '' }}
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 disabled:opacity-60">
            <option value="">— Primero selecciona una especialidad —</option>
        </select>
        @error('id_medico')
            <span class="text-xs text-error">{{ $message }}</span>
        @enderror
    </label>

    {{-- Fecha --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Fecha de la cita <span class="text-error">*</span></span>
        <input type="date" name="fecha_cita" id="fecha_cita" required
            value="{{ $editFecha }}" min="{{ date('Y-m-d') }}"
            {{ !$editMedicoId ? 'disabled' : '' }}
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 disabled:opacity-60" />
        @error('fecha_cita')
            <span class="text-xs text-error">{{ $message }}</span>
        @enderror
    </label>

    {{-- Slots de horario --}}
    <div class="sm:col-span-2">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Horario disponible <span class="text-error">*</span></span>
        <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ $editHoraInicio }}">
        <input type="hidden" name="hora_fin"    id="hora_fin"    value="{{ $editHoraFin }}">

        <div id="slots-container" class="mt-2">
            <div id="slots-grid" class="flex flex-wrap gap-2"></div>
            <div id="slots-msg" class="mt-2 text-xs text-slate-400 dark:text-navy-300"></div>
        </div>
        @error('hora_inicio')
            <span class="text-xs text-error mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Motivo --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Motivo de consulta</span>
        <textarea name="motivo_consulta" id="motivo_consulta" rows="3"
            placeholder="Describa brevemente el motivo..."
            class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm placeholder:text-slate-400 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400">{{ old('motivo_consulta', $cita->motivo_consulta ?? '') }}</textarea>
        @error('motivo_consulta')
            <span class="text-xs text-error">{{ $message }}</span>
        @enderror
    </label>

    {{-- Observaciones --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Observaciones</span>
        <textarea name="observaciones" id="observaciones" rows="3"
            placeholder="Notas adicionales..."
            class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm placeholder:text-slate-400 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400">{{ old('observaciones', $cita->observaciones ?? '') }}</textarea>
        @error('observaciones')
            <span class="text-xs text-error">{{ $message }}</span>
        @enderror
    </label>

    {{-- Estado (solo edición) --}}
    @if(isset($cita))
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Estado</span>
        <select name="estado_cita" id="estado_cita"
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400">
            @foreach(['pendiente','confirmada','atendida','no_asistio'] as $est)
                <option value="{{ $est }}"
                    {{ old('estado_cita', $cita->estado_cita) === $est ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $est)) }}
                </option>
            @endforeach
        </select>
    </label>
    @endif
</div>

@push('scripts')
<script>
(function () {
    const selEsp    = document.getElementById('id_especialidad');
    const selMedico = document.getElementById('id_medico');
    const inpFecha  = document.getElementById('fecha_cita');
    const inpHi     = document.getElementById('hora_inicio');
    const inpHf     = document.getElementById('hora_fin');
    const slotsGrid = document.getElementById('slots-grid');
    const slotsMsg  = document.getElementById('slots-msg');
    const espMsg    = document.getElementById('esp-msg');
    const excluirId = {{ $excluirCitaId ?? 'null' }};

    selEsp.addEventListener('change', async () => {
        const espId = selEsp.value;
        resetMedico();
        resetSlots();
        if (!espId) return;

        selMedico.innerHTML = '<option value="">Cargando...</option>';
        selMedico.disabled  = true;

        try {
            const res   = await fetch(`/api/especialidades/${espId}/medicos`);
            const lista = await res.json();

            if (!lista.length) {
                selMedico.innerHTML = '<option value="">Sin medicos para esta especialidad</option>';
                espMsg.classList.remove('hidden');
                return;
            }

            espMsg.classList.add('hidden');
            selMedico.innerHTML = '<option value="">— Seleccionar medico —</option>';
            lista.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.nombre;
                selMedico.appendChild(opt);
            });
            selMedico.disabled = false;
            inpFecha.disabled  = false;

            const premedico = '{{ $editMedicoId }}';
            if (premedico) {
                selMedico.value = premedico;
                if (inpFecha.value) cargarSlots();
            }
        } catch (e) {
            selMedico.innerHTML = '<option value="">Error al cargar medicos</option>';
        }
    });

    selMedico.addEventListener('change', () => { resetSlots(); if (selMedico.value && inpFecha.value) cargarSlots(); });
    inpFecha.addEventListener('change',  () => { resetSlots(); if (selMedico.value && inpFecha.value) cargarSlots(); });

    async function cargarSlots() {
        slotsGrid.innerHTML = '<span class="text-xs text-slate-400">Cargando horarios...</span>';
        slotsMsg.textContent = '';

        const params = new URLSearchParams({ fecha: inpFecha.value });
        if (excluirId) params.append('excluir_cita_id', excluirId);

        try {
            const res   = await fetch(`/api/medicos/${selMedico.value}/slots?${params}`);
            const slots = await res.json();

            if (!slots.length) {
                slotsGrid.innerHTML = '';
                slotsMsg.textContent = 'El medico no tiene horario configurado para este dia.';
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
            btn.type = 'button';
            btn.textContent = `${s.hora_inicio} – ${s.hora_fin}`;

            if (!s.disponible) {
                btn.className = 'btn rounded-full border border-slate-200 px-3 py-1.5 text-xs text-slate-400 line-through cursor-not-allowed dark:border-navy-500 dark:text-navy-400';
                btn.disabled = true;
                btn.title = 'Horario ocupado';
            } else {
                btn.className = 'btn rounded-full border border-primary px-3 py-1.5 text-xs text-primary hover:bg-primary hover:text-white dark:border-accent dark:text-accent dark:hover:bg-accent dark:hover:text-white transition-colors slot-libre';
                btn.addEventListener('click', () => seleccionarSlot(btn, s.hora_inicio, s.hora_fin));

                if (preHi && preHi === s.hora_inicio) {
                    seleccionarSlot(btn, s.hora_inicio, s.hora_fin);
                }
            }
            slotsGrid.appendChild(btn);
        });

        if (!slots.some(s => s.disponible)) {
            slotsMsg.textContent = 'No hay horarios disponibles para este dia.';
        }
    }

    function seleccionarSlot(btn, hi, hf) {
        document.querySelectorAll('.slot-libre').forEach(b => {
            b.className = 'btn rounded-full border border-primary px-3 py-1.5 text-xs text-primary hover:bg-primary hover:text-white dark:border-accent dark:text-accent dark:hover:bg-accent dark:hover:text-white transition-colors slot-libre';
        });
        btn.className = 'btn rounded-full bg-primary px-3 py-1.5 text-xs text-white dark:bg-accent slot-libre slot-selected';
        inpHi.value = hi;
        inpHf.value = hf;
        slotsMsg.textContent = '';
    }

    function resetMedico() {
        selMedico.innerHTML = '<option value="">— Primero selecciona una especialidad —</option>';
        selMedico.disabled = true;
        inpFecha.disabled = true;
        espMsg.classList.add('hidden');
    }

    function resetSlots() {
        slotsGrid.innerHTML = '';
        slotsMsg.textContent = '';
        inpHi.value = '';
        inpHf.value = '';
    }

    const preEsp = '{{ $editEspId }}';
    if (preEsp) {
        selEsp.value = preEsp;
        selEsp.dispatchEvent(new Event('change'));
    }
})();
</script>
@endpush
