<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Nombres</label>
        <input name="nombres"
            value="{{ old('nombres', $paciente->nombres ?? '') }}"
            class="form-control @error('nombres') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">Apellidos</label>
        <input name="apellidos"
            value="{{ old('apellidos', $paciente->apellidos ?? '') }}"
            class="form-control @error('apellidos') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label">CI</label>
        <input type="text" id="ci" name="ci"
            value="{{ old('ci', $paciente->ci ?? '') }}"
            class="form-control @error('ci') is-invalid @enderror">

        @error('ci')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small id="mensajeCI"></small>
    </div>

    <div class="col-md-6">
        <label class="form-label">Teléfono</label>
        <input name="telefono"
            value="{{ old('telefono', $paciente->telefono ?? '') }}"
            class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select">
            <option value="activo"
                {{ old('estado', $paciente->estado ?? '') == 'activo' ? 'selected' : '' }}>
                Activo
            </option>
            <option value="inactivo"
                {{ old('estado', $paciente->estado ?? '') == 'inactivo' ? 'selected' : '' }}>
                Inactivo
            </option>
        </select>
    </div>

</div>

<!-- ✅ BOTONES -->
<div class="mt-4 d-flex justify-content-between">

    <a href="{{ url()->previous() }}" class="btn btn-outline-dark">
        ← Volver
    </a>

</div>