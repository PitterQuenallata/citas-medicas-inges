<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Nombres <span class="text-error">*</span></span>
        <input name="nombres" type="text" autocomplete="given-name" placeholder="Ingrese nombres"
            value="{{ old('nombres', $paciente->nombres ?? '') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('nombres') border-error @enderror">
        @error('nombres')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Apellidos <span class="text-error">*</span></span>
        <input name="apellidos" type="text" autocomplete="family-name" placeholder="Ingrese apellidos"
            value="{{ old('apellidos', $paciente->apellidos ?? '') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('apellidos') border-error @enderror">
        @error('apellidos')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Fecha de nacimiento <span class="text-error">*</span></span>
        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento ?? '') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('fecha_nacimiento') border-error @enderror">
        @error('fecha_nacimiento')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Sexo <span class="text-error">*</span></span>
        <select name="sexo" required
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('sexo') border-error @enderror">
            <option value="" {{ old('sexo', $paciente->sexo ?? null) === null ? 'selected' : '' }} disabled>Seleccionar...</option>
            <option value="masculino" {{ old('sexo', $paciente->sexo ?? '') === 'masculino' ? 'selected' : '' }}>Masculino</option>
            <option value="femenino" {{ old('sexo', $paciente->sexo ?? '') === 'femenino' ? 'selected' : '' }}>Femenino</option>
            <option value="otro" {{ old('sexo', $paciente->sexo ?? '') === 'otro' ? 'selected' : '' }}>Otro</option>
        </select>
        @error('sexo')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">CI <span class="text-error">*</span></span>
        <input type="text" id="ci" name="ci" inputmode="numeric" placeholder="Ej: 1234567"
            value="{{ old('ci', $paciente->ci ?? '') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('ci') border-error @enderror">
        @error('ci')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
        <p id="mensajeCI" class="mt-1 text-xs text-slate-500"></p>
    </label>

    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Teléfono <span class="text-error">*</span></span>
        <input name="telefono" type="tel" inputmode="numeric" autocomplete="tel" placeholder="Ej: 7xxxxxxx"
            value="{{ old('telefono', $paciente->telefono ?? '') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('telefono') border-error @enderror">
        @error('telefono')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block sm:col-span-2">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Dirección <span class="text-error">*</span></span>
        <input type="text" name="direccion" value="{{ old('direccion', $paciente->direccion ?? '') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('direccion') border-error @enderror">
        @error('direccion')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Correo <span class="text-error">*</span></span>
        <input type="email" name="email" value="{{ old('email', $paciente->email ?? '') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('email') border-error @enderror">
        @error('email')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Grupo sanguíneo <span class="text-error">*</span></span>
        <input type="text" name="grupo_sanguineo" value="{{ old('grupo_sanguineo', $paciente->grupo_sanguineo ?? '') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('grupo_sanguineo') border-error @enderror">
        @error('grupo_sanguineo')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Contacto de emergencia (nombre) <span class="text-error">*</span></span>
        <input type="text" name="contacto_emergencia_nombre" value="{{ old('contacto_emergencia_nombre', $paciente->contacto_emergencia_nombre ?? '') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('contacto_emergencia_nombre') border-error @enderror">
        @error('contacto_emergencia_nombre')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Contacto de emergencia (teléfono) <span class="text-error">*</span></span>
        <input type="text" name="contacto_emergencia_telefono" value="{{ old('contacto_emergencia_telefono', $paciente->contacto_emergencia_telefono ?? '') }}" required
            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('contacto_emergencia_telefono') border-error @enderror">
        @error('contacto_emergencia_telefono')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block sm:col-span-2">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Alergias <span class="text-error">*</span></span>
        <textarea name="alergias" rows="3" required
            class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('alergias') border-error @enderror">{{ old('alergias', $paciente->alergias ?? '') }}</textarea>
        @error('alergias')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block sm:col-span-2">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Observaciones generales <span class="text-error">*</span></span>
        <textarea name="observaciones_generales" rows="3" required
            class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('observaciones_generales') border-error @enderror">{{ old('observaciones_generales', $paciente->observaciones_generales ?? '') }}</textarea>
        @error('observaciones_generales')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>

    <label class="block">
        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Estado</span>
        <select name="estado"
            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('estado') border-error @enderror">
            <option value="activo" {{ old('estado', $paciente->estado ?? 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ old('estado', $paciente->estado ?? '') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
        @error('estado')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </label>
</div>