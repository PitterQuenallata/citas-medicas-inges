<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Nombres</label>
        <input name="nombres" type="text" autocomplete="given-name" placeholder="Ingrese nombres"
            value="{{ old('nombres', $paciente->nombres ?? '') }}"
            class="form-control @error('nombres') is-invalid @enderror">

        @error('nombres')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Apellidos</label>
        <input name="apellidos" type="text" autocomplete="family-name" placeholder="Ingrese apellidos"
            value="{{ old('apellidos', $paciente->apellidos ?? '') }}"
            class="form-control @error('apellidos') is-invalid @enderror">

        @error('apellidos')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">CI</label>
        <input type="text" id="ci" name="ci" inputmode="numeric" placeholder="Ej: 1234567"
            value="{{ old('ci', $paciente->ci ?? '') }}"
            class="form-control @error('ci') is-invalid @enderror">

        @error('ci')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small id="mensajeCI" class="form-text"></small>
    </div>

    <div class="col-md-6">
        <label class="form-label">Teléfono</label>
        <input name="telefono" type="tel" inputmode="numeric" autocomplete="tel" placeholder="Ej: 7xxxxxxx"
            value="{{ old('telefono', $paciente->telefono ?? '') }}"
            class="form-control @error('telefono') is-invalid @enderror">

        @error('telefono')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select @error('estado') is-invalid @enderror">
            <option value="activo"
                {{ old('estado', $paciente->estado ?? '') == 'activo' ? 'selected' : '' }}>
                Activo
            </option>
            <option value="inactivo"
                {{ old('estado', $paciente->estado ?? '') == 'inactivo' ? 'selected' : '' }}>
                Inactivo
            </option>
        </select>

        @error('estado')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>