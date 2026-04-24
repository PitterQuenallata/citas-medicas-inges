@extends('layouts.app')

@section('title', 'Nuevo Médico')

@section('content')
<div class="container py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('medicos.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0">Registrar Nuevo Médico</h1>
            <p class="text-muted mb-0">Complete todos los campos requeridos</p>
        </div>
    </div>

    <form action="{{ route('medicos.store') }}" method="POST" id="formMedico">
        @csrf

        <div class="row g-4">

            <div class="col-lg-8">

                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-semibold">
                        <i class="fas fa-id-card me-2 text-primary"></i>Datos del Médico
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Usuario del sistema <span class="text-danger">*</span></label>
                                <select name="id_usuario" class="form-select @error('id_usuario') is-invalid @enderror" required>
                                    <option value="">Seleccione un usuario…</option>
                                    @foreach($usuariosSinMedico as $usuario)
                                        <option value="{{ $usuario->id_usuario }}"
                                            {{ old('id_usuario') == $usuario->id_usuario ? 'selected' : '' }}>
                                            {{ $usuario->nombre }} {{ $usuario->apellido }} — {{ $usuario->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_usuario')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Código de médico <span class="text-danger">*</span></label>
                                <input type="text" name="codigo_medico"
                                       class="form-control font-monospace @error('codigo_medico') is-invalid @enderror"
                                       value="{{ old('codigo_medico', $codigoSugerido) }}"
                                       maxlength="30" required>
                                @error('codigo_medico')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nombres <span class="text-danger">*</span></label>
                                <input type="text" name="nombres"
                                       class="form-control @error('nombres') is-invalid @enderror"
                                       value="{{ old('nombres') }}" maxlength="100" required>
                                @error('nombres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" name="apellidos"
                                       class="form-control @error('apellidos') is-invalid @enderror"
                                       value="{{ old('apellidos') }}" maxlength="100" required>
                                @error('apellidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">CI</label>
                                <input type="text" name="ci"
                                       class="form-control @error('ci') is-invalid @enderror"
                                       value="{{ old('ci') }}" maxlength="20">
                                @error('ci')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono"
                                       class="form-control @error('telefono') is-invalid @enderror"
                                       value="{{ old('telefono') }}" maxlength="20">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Email de contacto</label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" maxlength="150">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Matrícula profesional <span class="text-danger">*</span></label>
                                <input type="text" name="matricula_profesional"
                                       class="form-control font-monospace @error('matricula_profesional') is-invalid @enderror"
                                       value="{{ old('matricula_profesional') }}" maxlength="50" required>
                                @error('matricula_profesional')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Estado <span class="text-danger">*</span></label>
                                <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                    <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
                                    <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center fw-semibold">
                        <span><i class="fas fa-clock me-2 text-primary"></i>Horarios de Atención</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarHorario">
                            <i class="fas fa-plus me-1"></i>Agregar horario
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="contenedorHorarios"></div>
                        <p class="text-muted small mb-0" id="mensajeSinHorarios">
                            <i class="fas fa-info-circle me-1"></i>
                            No hay horarios configurados. Haga clic en "Agregar horario" para agregar disponibilidad.
                        </p>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-semibold">
                        <i class="fas fa-stethoscope me-2 text-primary"></i>Especialidades
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        @forelse($especialidades as $esp)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="especialidades[]"
                                       value="{{ $esp->id_especialidad }}"
                                       id="esp_{{ $esp->id_especialidad }}"
                                       {{ in_array($esp->id_especialidad, (array) old('especialidades', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="esp_{{ $esp->id_especialidad }}">
                                    {{ $esp->nombre_especialidad }}
                                </label>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No hay especialidades disponibles.</p>
                        @endforelse
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-1"></i> Guardar Médico
                        </button>
                        <a href="{{ route('medicos.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver al listado
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<template id="templateHorario">
    <div class="horario-fila border rounded p-3 mb-3 position-relative">
        <button type="button"
                class="btn btn-sm btn-outline-danger btn-eliminar-horario position-absolute top-0 end-0 m-2"
                title="Eliminar horario">
            <i class="fas fa-times"></i>
        </button>
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Día</label>
                <select name="horarios[IDX][dia_semana]" class="form-select form-select-sm" required>
                    <option value="">Día…</option>
                    @foreach($diasSemana as $num => $nombre)
                        <option value="{{ $num }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Inicio</label>
                <input type="time" name="horarios[IDX][hora_inicio]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Fin</label>
                <input type="time" name="horarios[IDX][hora_fin]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Duración (min)</label>
                <input type="number" name="horarios[IDX][duracion_cita_minutos]"
                       class="form-control form-control-sm" value="30" min="5" max="240">
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
(function () {
    let contadorHorario = 0;
    const contenedor = document.getElementById('contenedorHorarios');
    const mensajeSin = document.getElementById('mensajeSinHorarios');
    const btnAgregar = document.getElementById('btnAgregarHorario');
    const template = document.getElementById('templateHorario');

    function actualizarMensaje() {
        mensajeSin.classList.toggle('d-none', contenedor.children.length > 0);
    }

    btnAgregar.addEventListener('click', () => {
        const clone = template.content.cloneNode(true);

        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('IDX', contadorHorario);
        });

        clone.querySelector('.btn-eliminar-horario').addEventListener('click', function () {
            this.closest('.horario-fila').remove();
            actualizarMensaje();
        });

        contenedor.appendChild(clone);
        contadorHorario++;
        actualizarMensaje();
    });
})();
</script>
@endpush
@endsection