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

<div class="row g-4">

    {{-- Paciente --}}
    <div class="col-md-6">
        <label class="form-label-citas" for="id_paciente">Paciente</label>
        <select name="id_paciente" id="id_paciente"
                class="form-select @error('id_paciente') is-invalid @enderror" required>
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
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Especialidad --}}
    <div class="col-md-6">
        <label class="form-label-citas" for="id_especialidad">Especialidad</label>
        <select id="id_especialidad" class="form-select">
            <option value="">— Seleccionar especialidad —</option>
            @foreach($especialidades as $esp)
                <option value="{{ $esp->id_especialidad }}"
                    {{ $editEspId == $esp->id_especialidad ? 'selected' : '' }}>
                    {{ $esp->nombre_especialidad }}
                </option>
            @endforeach
        </select>
        <div id="esp-msg" class="form-text text-muted" style="display:none;">Sin médicos activos para esta especialidad.</div>
    </div>

    {{-- Médico (se llena por AJAX) --}}
    <div class="col-md-6">
        <label class="form-label-citas" for="id_medico">Médico</label>
        <select name="id_medico" id="id_medico"
                class="form-select @error('id_medico') is-invalid @enderror" required
                {{ !$editMedicoId ? 'disabled' : '' }}>
            <option value="">— Primero selecciona una especialidad —</option>
        </select>
        @error('id_medico')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Fecha --}}
    <div class="col-md-6">
        <label class="form-label-citas" for="fecha_cita">Fecha de la cita</label>
        <input type="date" name="fecha_cita" id="fecha_cita"
               class="form-control @error('fecha_cita') is-invalid @enderror"
               value="{{ $editFecha }}"
               min="{{ date('Y-m-d') }}" required
               {{ !$editMedicoId ? 'disabled' : '' }}>
        @error('fecha_cita')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Slots de horario --}}
    <div class="col-12">
        <label class="form-label-citas">Horario disponible</label>

        {{-- Hidden inputs que se envían al servidor --}}
        <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ $editHoraInicio }}" required>
        <input type="hidden" name="hora_fin"    id="hora_fin"    value="{{ $editHoraFin }}"    required>

        <div id="slots-container">
            @if($editHoraInicio && $editHoraFin)
                <div id="slot-selected-label" class="mb-2">
                    <span class="badge-estado badge-confirmada" style="font-size:.875rem;padding:.4em .8em;">
                        ✓ {{ $editHoraInicio }} – {{ $editHoraFin }}
                    </span>
                </div>
            @endif
            <div id="slots-grid" class="d-flex flex-wrap gap-2"></div>
            <div id="slots-msg" class="text-muted small mt-1"></div>
        </div>

        @error('hora_inicio')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Motivo --}}
    <div class="col-md-6">
        <label class="form-label-citas" for="motivo_consulta">Motivo de consulta</label>
        <textarea name="motivo_consulta" id="motivo_consulta" rows="3"
                  class="form-control @error('motivo_consulta') is-invalid @enderror"
                  placeholder="Describa brevemente el motivo de la consulta…">{{ old('motivo_consulta', $cita->motivo_consulta ?? '') }}</textarea>
        @error('motivo_consulta')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Observaciones --}}
    <div class="col-md-6">
        <label class="form-label-citas" for="observaciones">Observaciones</label>
        <textarea name="observaciones" id="observaciones" rows="3"
                  class="form-control @error('observaciones') is-invalid @enderror"
                  placeholder="Notas adicionales…">{{ old('observaciones', $cita->observaciones ?? '') }}</textarea>
        @error('observaciones')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Estado (solo edición) --}}
    @if(isset($cita))
    <div class="col-md-4">
        <label class="form-label-citas" for="estado_cita">Estado</label>
        <select name="estado_cita" id="estado_cita" class="form-select">
            @foreach(['pendiente','confirmada','atendida','no_asistio'] as $est)
                <option value="{{ $est }}"
                    {{ old('estado_cita', $cita->estado_cita) === $est ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $est)) }}
                </option>
            @endforeach
        </select>
    </div>
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
            btn.className   = 'btn btn-sm slot-btn';

            if (!s.disponible) {
                btn.disabled  = true;
                btn.classList.add('slot-ocupado');
                btn.title     = 'Horario ocupado';
            } else {
                btn.classList.add('slot-libre');
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
        });
        btn.classList.add('slot-seleccionado');
        inpHi.value = hi;
        inpHf.value = hf;
        slotsMsg.textContent = '';
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
