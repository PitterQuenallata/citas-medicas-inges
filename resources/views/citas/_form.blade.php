{{-- ============================================================
     Partial: _form.blade.php
     Variables esperadas: $pacientes, $especialidades
     Opcional: $cita (para edición, incluye medico.especialidades cargado)
     ============================================================ --}}

@if($errors->has('disponibilidad'))
    <div class="alert-disponibilidad mb-4 p-3">
        <strong>Conflicto de disponibilidad:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->get('disponibilidad') as $err)
                @foreach((array)$err as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            @endforeach
        </ul>
    </div>
@endif

{{-- Valores de pre-llenado para modo edición --}}
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
                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('id_paciente') border-error @enderror">
            <option value="">— Seleccionar paciente —</option>
            @foreach($pacientes as $pac)
                <option value="{{ $pac->id_paciente }}"
                    {{ old('id_paciente', $cita->id_paciente ?? '') == $pac->id_paciente ? 'selected' : '' }}>
                    {{ $pac->apellidos }}, {{ $pac->nombres }}@if($pac->ci) ({{ $pac->ci }}) @endif
                </option>
            @endforeach
        </select>
        @error('id_paciente')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    {{-- Especialidad --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Especialidad</span>
        <select id="id_especialidad"
                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            <option value="">— Seleccionar especialidad —</option>
            @foreach($especialidades as $esp)
                <option value="{{ $esp->id_especialidad }}"
                    {{ $editEspId == $esp->id_especialidad ? 'selected' : '' }}>
                    {{ $esp->nombre_especialidad }}
                </option>
            @endforeach
        </select>
        <p id="esp-msg" class="mt-1 text-xs text-slate-500" style="display:none;">Sin médicos activos para esta especialidad.</p>
    </label>

    {{-- Médico (se llena por AJAX) --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Médico <span class="text-error">*</span></span>
        <select name="id_medico" id="id_medico" required {{ !$editMedicoId ? 'disabled' : '' }}
                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('id_medico') border-error @enderror">
            <option value="">— Primero selecciona una especialidad —</option>
        </select>
        @error('id_medico')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    {{-- Fecha --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Fecha de la cita <span class="text-error">*</span></span>
        <input type="date" name="fecha_cita" id="fecha_cita" value="{{ $editFecha }}" min="{{ date('Y-m-d') }}" required {{ !$editMedicoId ? 'disabled' : '' }}
               class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('fecha_cita') border-error @enderror">
        @error('fecha_cita')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    {{-- Slots de horario --}}
    <div class="sm:col-span-2">
        <p class="text-sm font-medium text-slate-600 dark:text-navy-100">Horario disponible <span class="text-error">*</span></p>

        {{-- Hidden inputs que se envían al servidor --}}
        <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ $editHoraInicio }}" required>
        <input type="hidden" name="hora_fin"    id="hora_fin"    value="{{ $editHoraFin }}"    required>

        <div id="slots-container" class="mt-2">
            <div id="slot-selected-label" class="mb-2"></div>
            @if($editHoraInicio && $editHoraFin)
                <script>
                    window.__citaSlotPrefill = { hi: @json($editHoraInicio), hf: @json($editHoraFin) };
                </script>
            @endif
            <div id="slots-grid" class="flex flex-wrap gap-2"></div>
            <p id="slots-msg" class="mt-2 text-xs text-slate-500"></p>
        </div>

        @error('hora_inicio')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    {{-- Motivo --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Motivo de consulta</span>
        <textarea name="motivo_consulta" id="motivo_consulta" rows="3"
                  class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('motivo_consulta') border-error @enderror"
                  placeholder="Describa brevemente el motivo de la consulta…">{{ old('motivo_consulta', $cita->motivo_consulta ?? '') }}</textarea>
        @error('motivo_consulta')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    {{-- Observaciones --}}
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Observaciones</span>
        <textarea name="observaciones" id="observaciones" rows="3"
                  class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('observaciones') border-error @enderror"
                  placeholder="Notas adicionales…">{{ old('observaciones', $cita->observaciones ?? '') }}</textarea>
        @error('observaciones')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    {{-- Estado (solo edición) --}}
    @if(isset($cita))
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Estado</span>
        <select name="estado_cita" id="estado_cita"
                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
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

    // ── Especialidad cambia → cargar médicos ──────────────────────────────
    selEsp.addEventListener('change', async () => {
        const espId = selEsp.value;
        resetMedico();
        resetSlots();

        if (!espId) return;

        selMedico.innerHTML = '<option value="">Cargando…</option>';
        selMedico.disabled  = true;

        try {
            const res   = await fetch(`/api/especialidades/${espId}/medicos`);
            const lista = await res.json();

            if (!lista.length) {
                selMedico.innerHTML = '<option value="">Sin médicos para esta especialidad</option>';
                espMsg.style.display = 'block';
                return;
            }

            espMsg.style.display = 'none';
            selMedico.innerHTML  = '<option value="">— Seleccionar médico —</option>';
            lista.forEach(m => {
                const opt    = document.createElement('option');
                opt.value    = m.id;
                opt.textContent = m.nombre;
                selMedico.appendChild(opt);
            });
            selMedico.disabled = false;
            inpFecha.disabled  = false;

            // En modo edición: pre-seleccionar médico si corresponde
            const premedico = '{{ $editMedicoId }}';
            if (premedico) {
                selMedico.value = premedico;
                // Cargar slots si ya hay fecha
                if (inpFecha.value) cargarSlots();
            }
        } catch (e) {
            selMedico.innerHTML = '<option value="">Error al cargar médicos</option>';
        }
    });

    // ── Médico o fecha cambia → cargar slots ─────────────────────────────
    selMedico.addEventListener('change', () => { resetSlots(); if (selMedico.value && inpFecha.value) cargarSlots(); });
    inpFecha.addEventListener('change',  () => { resetSlots(); if (selMedico.value && inpFecha.value) cargarSlots(); });

    async function cargarSlots() {
        slotsGrid.innerHTML = '<span class="text-muted small">Cargando horarios…</span>';
        slotsMsg.textContent = '';

        const params = new URLSearchParams({ fecha: inpFecha.value });
        if (excluirId) params.append('excluir_cita_id', excluirId);

        try {
            const res   = await fetch(`/api/medicos/${selMedico.value}/slots?${params}`);
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
                btn.disabled  = true;
                btn.classList.add('bg-error/10', 'text-error', 'border-error/30', 'line-through', 'cursor-not-allowed', 'opacity-70', 'slot-ocupado');
                btn.title     = 'Horario ocupado';
            } else {
                btn.classList.add('bg-success/10', 'text-success', 'border-success/30', 'hover:bg-success/20', 'slot-libre');
                btn.addEventListener('click', () => seleccionarSlot(btn, s.hora_inicio, s.hora_fin));

                // Pre-seleccionar slot en edición
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

    function resetMedico() {
        selMedico.innerHTML = '<option value="">— Primero selecciona una especialidad —</option>';
        selMedico.disabled  = true;
        inpFecha.disabled   = true;
        espMsg.style.display = 'none';
    }

    function resetSlots() {
        slotsGrid.innerHTML  = '';
        slotsMsg.textContent = '';
        inpHi.value = '';
        inpHf.value = '';

        const label = document.getElementById('slot-selected-label');
        if (label) label.innerHTML = '';
    }

    // ── Inicializar en modo edición ───────────────────────────────────────
    const preEsp = '{{ $editEspId }}';
    if (preEsp) {
        selEsp.value = preEsp;
        selEsp.dispatchEvent(new Event('change'));
    }
})();
</script>
@endpush
