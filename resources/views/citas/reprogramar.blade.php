@extends('layouts.app')

@section('title', 'Reprogramar Cita')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('citas.show', $cita) }}" class="btn btn-ghost btn-sm">← Volver</a>
    <div>
        <h4 class="fw-bold mb-0" style="color:#0f172a;">Reprogramar Cita</h4>
        <p class="text-muted small mb-0">Código original: <strong>{{ $cita->codigo_cita }}</strong></p>
    </div>
</div>

{{-- Resumen de la cita original --}}
<div class="card-citas p-3 mb-4" style="background:#fffbeb;border-color:#fde68a;">
    <p class="detail-label mb-2" style="color:#92400e;">Cita original a reprogramar</p>
    <div class="row g-2" style="font-size:.875rem;">
        <div class="col-sm-4">
            <span class="text-muted">Paciente:</span>
            <strong>{{ $cita->paciente->apellidos }}, {{ $cita->paciente->nombres }}</strong>
        </div>
        <div class="col-sm-4">
            <span class="text-muted">Médico:</span>
            <strong>Dr(a). {{ $cita->medico->apellidos }}, {{ $cita->medico->nombres }}</strong>
        </div>
        <div class="col-sm-4">
            <span class="text-muted">Fecha/hora anterior:</span>
            <strong>
                {{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}
                {{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}
            </strong>
        </div>
    </div>
</div>

<div class="card-citas p-4">
    <form method="POST" action="{{ route('citas.storeReprogramar', $cita) }}">
        @csrf
        @method('PATCH')

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

        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label-citas" for="fecha_cita">Nueva fecha</label>
                <input type="date" name="fecha_cita" id="fecha_cita"
                       class="form-control @error('fecha_cita') is-invalid @enderror"
                       value="{{ old('fecha_cita') }}"
                       min="{{ date('Y-m-d') }}" required>
                @error('fecha_cita')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label-citas">Horario disponible</label>
                <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio') }}" required>
                <input type="hidden" name="hora_fin"    id="hora_fin"    value="{{ old('hora_fin') }}">
                <div id="slots-grid" class="d-flex flex-wrap gap-2 mt-1"></div>
                <div id="slots-msg" class="text-muted small mt-1"></div>
                @error('hora_inicio')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <hr class="my-4" style="border-color:var(--border);">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('citas.show', $cita) }}" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-brand">Confirmar reprogramación</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const inpFecha  = document.getElementById('fecha_cita');
    const inpHi     = document.getElementById('hora_inicio');
    const inpHf     = document.getElementById('hora_fin');
    const slotsGrid = document.getElementById('slots-grid');
    const slotsMsg  = document.getElementById('slots-msg');
    const medicoId  = {{ $cita->id_medico }};

    inpFecha.addEventListener('change', () => {
        inpHi.value = '';
        inpHf.value = '';
        if (inpFecha.value) cargarSlots();
    });

    async function cargarSlots() {
        slotsGrid.innerHTML  = '<span class="text-muted small">Cargando horarios…</span>';
        slotsMsg.textContent = '';

        try {
            const res   = await fetch(`/api/medicos/${medicoId}/slots?fecha=${inpFecha.value}`);
            const slots = await res.json();

            if (!slots.length) {
                slotsGrid.innerHTML  = '';
                slotsMsg.textContent = 'El médico no tiene horario para este día.';
                return;
            }

            slotsGrid.innerHTML = '';
            slots.forEach(s => {
                const btn = document.createElement('button');
                btn.type  = 'button';
                btn.textContent = `${s.hora_inicio} – ${s.hora_fin}`;
                btn.className   = 'btn btn-sm slot-btn';

                if (!s.disponible) {
                    btn.disabled = true;
                    btn.classList.add('slot-ocupado');
                } else {
                    btn.classList.add('slot-libre');
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('.slot-seleccionado').forEach(b => b.classList.remove('slot-seleccionado'));
                        btn.classList.add('slot-seleccionado');
                        inpHi.value = s.hora_inicio;
                        inpHf.value = s.hora_fin;
                        slotsMsg.textContent = '';
                    });
                }
                slotsGrid.appendChild(btn);
            });
        } catch (e) {
            slotsGrid.innerHTML  = '';
            slotsMsg.textContent = 'Error al cargar horarios.';
        }
    }
})();
</script>
@endpush
