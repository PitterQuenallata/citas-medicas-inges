<div class="grid grid-cols-1 gap-4 sm:grid-cols-2" id="paciente-form-wrapper" x-data="pacienteValidations({
    nombres: '{{ old('nombres', $paciente->nombres ?? '') }}',
    apellidos: '{{ old('apellidos', $paciente->apellidos ?? '') }}',
    fecha_nacimiento: '{{ old('fecha_nacimiento', $paciente->fecha_nacimiento ?? '') }}',
    sexo: '{{ old('sexo', $paciente->sexo ?? '') }}',
    ci: '{{ old('ci', $paciente->ci ?? '') }}',
    telefono: '{{ old('telefono', $paciente->telefono ?? '') }}',
    direccion: '{{ old('direccion', $paciente->direccion ?? '') }}',
    email: '{{ old('email', $paciente->email ?? '') }}',
    grupo_sanguineo: '{{ old('grupo_sanguineo', $paciente->grupo_sanguineo ?? '') }}',
    contacto_emergencia_nombre: '{{ old('contacto_emergencia_nombre', $paciente->contacto_emergencia_nombre ?? '') }}',
    contacto_emergencia_telefono: '{{ old('contacto_emergencia_telefono', $paciente->contacto_emergencia_telefono ?? '') }}',
    alergias: '{{ old('alergias', $paciente->alergias ?? '') }}',
    observaciones_generales: '{{ old('observaciones_generales', $paciente->observaciones_generales ?? '') }}',
    estado: '{{ old('estado', $paciente->estado ?? 'activo') }}'
})">

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Nombres <span class="text-error">*</span></span>
            <input name="nombres" type="text" autocomplete="given-name" placeholder="Ingrese nombres" required
                @input="nombres.value = $event.target.value.toUpperCase()"
                x-model="nombres.value"
                @blur="nombres.blurred = true"
                x-effect="nombres.errorMessage = getErrorMessage(nombres.value, 'nombres')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !nombres.blurred,
                    'border-error': nombres.blurred && nombres.errorMessage,
                    'border-success': nombres.blurred && !nombres.errorMessage
                }"
                class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('nombres') border-error @enderror">
        </label>
        <span class="mt-1 text-xs text-error" x-show="nombres.blurred && nombres.errorMessage" x-text="nombres.errorMessage"></span>
        @error('nombres')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Apellidos <span class="text-error">*</span></span>
            <input name="apellidos" type="text" autocomplete="family-name" placeholder="Ingrese apellidos" required
                @input="apellidos.value = $event.target.value.toUpperCase()"
                x-model="apellidos.value"
                @blur="apellidos.blurred = true"
                x-effect="apellidos.errorMessage = getErrorMessage(apellidos.value, 'apellidos')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !apellidos.blurred,
                    'border-error': apellidos.blurred && apellidos.errorMessage,
                    'border-success': apellidos.blurred && !apellidos.errorMessage
                }"
                class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('apellidos') border-error @enderror">
        </label>
        <span class="mt-1 text-xs text-error" x-show="apellidos.blurred && apellidos.errorMessage" x-text="apellidos.errorMessage"></span>
        @error('apellidos')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Fecha de nacimiento <span class="text-error">*</span></span>
            <input type="date" name="fecha_nacimiento" required
                x-model="fecha_nacimiento.value"
                @blur="fecha_nacimiento.blurred = true"
                x-effect="fecha_nacimiento.errorMessage = getErrorMessage(fecha_nacimiento.value, 'fecha_nacimiento')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !fecha_nacimiento.blurred,
                    'border-error': fecha_nacimiento.blurred && fecha_nacimiento.errorMessage,
                    'border-success': fecha_nacimiento.blurred && !fecha_nacimiento.errorMessage
                }"
                class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('fecha_nacimiento') border-error @enderror">
        </label>
        <span class="mt-1 text-xs text-error" x-show="fecha_nacimiento.blurred && fecha_nacimiento.errorMessage" x-text="fecha_nacimiento.errorMessage"></span>
        @error('fecha_nacimiento')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Sexo <span class="text-error">*</span></span>
            <select name="sexo" required
                x-model="sexo.value"
                @blur="sexo.blurred = true"
                x-effect="sexo.errorMessage = getErrorMessage(sexo.value, 'sexo')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !sexo.blurred,
                    'border-error': sexo.blurred && sexo.errorMessage,
                    'border-success': sexo.blurred && !sexo.errorMessage
                }"
                class="form-select mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('sexo') border-error @enderror">
                <option value="" disabled>Seleccionar...</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
                <option value="otro">Otro</option>
            </select>
        </label>
        <span class="mt-1 text-xs text-error" x-show="sexo.blurred && sexo.errorMessage" x-text="sexo.errorMessage"></span>
        @error('sexo')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">CI <span class="text-error">*</span></span>
            <input type="text" id="ci" name="ci" inputmode="numeric" placeholder="Ej: 1234567" required
                x-model="ci.value"
                @blur="ci.blurred = true"
                x-effect="ci.errorMessage = getErrorMessage(ci.value, 'ci')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !ci.blurred,
                    'border-error': ci.blurred && ci.errorMessage,
                    'border-success': ci.blurred && !ci.errorMessage
                }"
                class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('ci') border-error @enderror">
        </label>
        <span class="mt-1 text-xs text-error" x-show="ci.blurred && ci.errorMessage" x-text="ci.errorMessage"></span>
        @error('ci')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Teléfono <span class="text-error">*</span></span>
            <input name="telefono" type="tel" inputmode="numeric" autocomplete="tel" placeholder="Ej: 7xxxxxxx" required maxlength="8"
                x-model="telefono.value"
                @blur="telefono.blurred = true"
                x-effect="telefono.errorMessage = getErrorMessage(telefono.value, 'telefono')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !telefono.blurred,
                    'border-error': telefono.blurred && telefono.errorMessage,
                    'border-success': telefono.blurred && !telefono.errorMessage
                }"
                class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('telefono') border-error @enderror">
        </label>
        <span class="mt-1 text-xs text-error" x-show="telefono.blurred && telefono.errorMessage" x-text="telefono.errorMessage"></span>
        @error('telefono')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Dirección <span class="text-error">*</span></span>
            <input type="text" name="direccion" required
                x-model="direccion.value"
                @blur="direccion.blurred = true"
                x-effect="direccion.errorMessage = getErrorMessage(direccion.value, 'direccion')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !direccion.blurred,
                    'border-error': direccion.blurred && direccion.errorMessage,
                    'border-success': direccion.blurred && !direccion.errorMessage
                }"
                class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('direccion') border-error @enderror">
        </label>
        <span class="mt-1 text-xs text-error" x-show="direccion.blurred && direccion.errorMessage" x-text="direccion.errorMessage"></span>
        @error('direccion')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Correo <span class="text-error">*</span></span>
            <input type="email" name="email" required
                x-model="email.value"
                @blur="email.blurred = true"
                x-effect="email.errorMessage = getErrorMessage(email.value, 'email')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !email.blurred,
                    'border-error': email.blurred && email.errorMessage,
                    'border-success': email.blurred && !email.errorMessage
                }"
                class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('email') border-error @enderror">
        </label>
        <span class="mt-1 text-xs text-error" x-show="email.blurred && email.errorMessage" x-text="email.errorMessage"></span>
        @error('email')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Grupo sanguíneo <span class="text-error">*</span></span>
            <input type="text" name="grupo_sanguineo" required
                x-model="grupo_sanguineo.value"
                @blur="grupo_sanguineo.blurred = true"
                x-effect="grupo_sanguineo.errorMessage = getErrorMessage(grupo_sanguineo.value, 'grupo_sanguineo')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !grupo_sanguineo.blurred,
                    'border-error': grupo_sanguineo.blurred && grupo_sanguineo.errorMessage,
                    'border-success': grupo_sanguineo.blurred && !grupo_sanguineo.errorMessage
                }"
                class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('grupo_sanguineo') border-error @enderror">
        </label>
        <span class="mt-1 text-xs text-error" x-show="grupo_sanguineo.blurred && grupo_sanguineo.errorMessage" x-text="grupo_sanguineo.errorMessage"></span>
        @error('grupo_sanguineo')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Contacto emergencia (nombre) <span class="text-error">*</span></span>
            <input type="text" name="contacto_emergencia_nombre" required
                @input="contacto_emergencia_nombre.value = $event.target.value.toUpperCase()"
                x-model="contacto_emergencia_nombre.value"
                @blur="contacto_emergencia_nombre.blurred = true"
                x-effect="contacto_emergencia_nombre.errorMessage = getErrorMessage(contacto_emergencia_nombre.value, 'contacto_emergencia_nombre')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !contacto_emergencia_nombre.blurred,
                    'border-error': contacto_emergencia_nombre.blurred && contacto_emergencia_nombre.errorMessage,
                    'border-success': contacto_emergencia_nombre.blurred && !contacto_emergencia_nombre.errorMessage
                }"
                class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('contacto_emergencia_nombre') border-error @enderror">
        </label>
        <span class="mt-1 text-xs text-error" x-show="contacto_emergencia_nombre.blurred && contacto_emergencia_nombre.errorMessage" x-text="contacto_emergencia_nombre.errorMessage"></span>
        @error('contacto_emergencia_nombre')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Contacto emergencia (teléfono) <span class="text-error">*</span></span>
            <input type="text" name="contacto_emergencia_telefono" required maxlength="8"
                x-model="contacto_emergencia_telefono.value"
                @blur="contacto_emergencia_telefono.blurred = true"
                x-effect="contacto_emergencia_telefono.errorMessage = getErrorMessage(contacto_emergencia_telefono.value, 'contacto_emergencia_telefono')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !contacto_emergencia_telefono.blurred,
                    'border-error': contacto_emergencia_telefono.blurred && contacto_emergencia_telefono.errorMessage,
                    'border-success': contacto_emergencia_telefono.blurred && !contacto_emergencia_telefono.errorMessage
                }"
                class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('contacto_emergencia_telefono') border-error @enderror">
        </label>
        <span class="mt-1 text-xs text-error" x-show="contacto_emergencia_telefono.blurred && contacto_emergencia_telefono.errorMessage" x-text="contacto_emergencia_telefono.errorMessage"></span>
        @error('contacto_emergencia_telefono')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Alergias <span class="text-error">*</span></span>
            <textarea name="alergias" rows="3" required
                x-model="alergias.value"
                @blur="alergias.blurred = true"
                x-effect="alergias.errorMessage = getErrorMessage(alergias.value, 'alergias')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !alergias.blurred,
                    'border-error': alergias.blurred && alergias.errorMessage,
                    'border-success': alergias.blurred && !alergias.errorMessage
                }"
                class="form-textarea mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('alergias') border-error @enderror"></textarea>
        </label>
        <span class="mt-1 text-xs text-error" x-show="alergias.blurred && alergias.errorMessage" x-text="alergias.errorMessage"></span>
        @error('alergias')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Observaciones generales <span class="text-error">*</span></span>
            <textarea name="observaciones_generales" rows="3" required
                x-model="observaciones_generales.value"
                @blur="observaciones_generales.blurred = true"
                x-effect="observaciones_generales.errorMessage = getErrorMessage(observaciones_generales.value, 'observaciones_generales')"
                :class="{
                    'border-slate-300 focus:border-primary dark:border-navy-450 dark:focus:border-accent': !observaciones_generales.blurred,
                    'border-error': observaciones_generales.blurred && observaciones_generales.errorMessage,
                    'border-success': observaciones_generales.blurred && !observaciones_generales.errorMessage
                }"
                class="form-textarea mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('observaciones_generales') border-error @enderror"></textarea>
        </label>
        <span class="mt-1 text-xs text-error" x-show="observaciones_generales.blurred && observaciones_generales.errorMessage" x-text="observaciones_generales.errorMessage"></span>
        @error('observaciones_generales')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block">
            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Estado</span>
            <select name="estado" x-model="estado.value"
                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('estado') border-error @enderror">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </label>
        @error('estado')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pacienteValidations', (initialData) => ({
        nombres: { value: initialData.nombres, errorMessage: '', blurred: false },
        apellidos: { value: initialData.apellidos, errorMessage: '', blurred: false },
        fecha_nacimiento: { value: initialData.fecha_nacimiento, errorMessage: '', blurred: false },
        sexo: { value: initialData.sexo, errorMessage: '', blurred: false },
        ci: { value: initialData.ci, errorMessage: '', blurred: false },
        telefono: { value: initialData.telefono, errorMessage: '', blurred: false },
        direccion: { value: initialData.direccion, errorMessage: '', blurred: false },
        email: { value: initialData.email, errorMessage: '', blurred: false },
        grupo_sanguineo: { value: initialData.grupo_sanguineo, errorMessage: '', blurred: false },
        contacto_emergencia_nombre: { value: initialData.contacto_emergencia_nombre, errorMessage: '', blurred: false },
        contacto_emergencia_telefono: { value: initialData.contacto_emergencia_telefono, errorMessage: '', blurred: false },
        alergias: { value: initialData.alergias, errorMessage: '', blurred: false },
        observaciones_generales: { value: initialData.observaciones_generales, errorMessage: '', blurred: false },
        estado: { value: initialData.estado },

        getErrorMessage(value, field) {
            if (['nombres', 'apellidos', 'contacto_emergencia_nombre'].includes(field)) {
                if (!value) return 'Este campo es requerido';
                if (!/^[A-ZÑÁÉÍÓÚ\s]+$/.test(value)) return 'Solo mayúsculas y espacios';
            }
            if (field === 'email') {
                if (!value) return 'El correo es requerido';
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Correo inválido';
            }
            if (['telefono', 'contacto_emergencia_telefono'].includes(field)) {
                if (!value) return 'Este campo es requerido';
                if (!/^[678]\d{7}$/.test(value)) return 'Debe tener 8 dígitos (inicia con 6, 7 u 8)';
            }
            if (field === 'ci') {
                if (!value) return 'El CI es requerido';
                if (!/^\d+$/.test(value)) return 'Solo números permitidos';
            }
            if (['fecha_nacimiento', 'sexo', 'direccion', 'grupo_sanguineo', 'alergias', 'observaciones_generales'].includes(field)) {
                if (!value) return 'Este campo es requerido';
            }
            return '';
        },
        
        init() {
            this.$nextTick(() => {
                const form = this.$el.closest('form');
                if (form) {
                    form.addEventListener('submit', (e) => {
                        let hasError = false;
                        const fields = [
                            'nombres', 'apellidos', 'fecha_nacimiento', 'sexo', 'ci', 
                            'telefono', 'direccion', 'email', 'grupo_sanguineo', 
                            'contacto_emergencia_nombre', 'contacto_emergencia_telefono', 
                            'alergias', 'observaciones_generales'
                        ];
                        
                        fields.forEach(f => {
                            this[f].blurred = true;
                            this[f].errorMessage = this.getErrorMessage(this[f].value, f);
                            if (this[f].errorMessage) {
                                hasError = true;
                            }
                        });

                        if (hasError) {
                            e.preventDefault();
                            
                            // Scroll to first error
                            setTimeout(() => {
                                const firstError = form.querySelector('.border-error');
                                if (firstError) {
                                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            }, 100);
                        }
                    });
                }
            });
        }
    }));
});
</script>
@endpush