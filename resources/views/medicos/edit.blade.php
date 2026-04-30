@extends('layouts.app')
@section('title', 'Editar Médico')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('medicos.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Médicos</span>
</div>

<form method="POST" action="{{ route('medicos.update', $medico->id_medico) }}" x-data="medicoForm()" @submit="validarSubmit($event)" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Datos principales --}}
    <div class="card p-4 sm:p-5">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-5">Datos del Médico</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Usuario vinculado <span class="text-error">*</span></span>
                    <select name="id_usuario" required
                        x-model="id_usuario.value" @blur="id_usuario.blurred = true" x-effect="id_usuario.errorMessage = getErrorMessage(id_usuario.value, 'id_usuario')"
                        :class="{ 'border-slate-300 focus:border-primary dark:border-navy-450': !id_usuario.blurred, 'border-error': id_usuario.blurred && id_usuario.errorMessage, 'border-success': id_usuario.blurred && !id_usuario.errorMessage }"
                        class="form-select mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('id_usuario') border-error @enderror">
                        <option value="" disabled>Seleccionar usuario...</option>
                        @foreach($usuariosDisponibles as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->nombre }} {{ $usuario->apellido }} ({{ $usuario->email }})</option>
                        @endforeach
                    </select>
                </label>
                <span class="mt-1 text-xs text-error" x-show="id_usuario.blurred && id_usuario.errorMessage" x-text="id_usuario.errorMessage"></span>
                @error('id_usuario')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Código médico <span class="text-error">*</span></span>
                    <input type="text" name="codigo_medico" required
                        x-model="codigo_medico.value" @blur="codigo_medico.blurred = true" x-effect="codigo_medico.errorMessage = getErrorMessage(codigo_medico.value, 'codigo_medico')"
                        :class="{ 'border-slate-300 focus:border-primary dark:border-navy-450': !codigo_medico.blurred, 'border-error': codigo_medico.blurred && codigo_medico.errorMessage, 'border-success': codigo_medico.blurred && !codigo_medico.errorMessage }"
                        class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('codigo_medico') border-error @enderror" />
                </label>
                <span class="mt-1 text-xs text-error" x-show="codigo_medico.blurred && codigo_medico.errorMessage" x-text="codigo_medico.errorMessage"></span>
                @error('codigo_medico')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Nombres <span class="text-error">*</span></span>
                    <input type="text" name="nombres" required maxlength="100"
                        @input="nombres.value = $event.target.value.toUpperCase()" x-model="nombres.value" @blur="nombres.blurred = true" x-effect="nombres.errorMessage = getErrorMessage(nombres.value, 'nombres')"
                        :class="{ 'border-slate-300 focus:border-primary dark:border-navy-450': !nombres.blurred, 'border-error': nombres.blurred && nombres.errorMessage, 'border-success': nombres.blurred && !nombres.errorMessage }"
                        class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('nombres') border-error @enderror" />
                </label>
                <span class="mt-1 text-xs text-error" x-show="nombres.blurred && nombres.errorMessage" x-text="nombres.errorMessage"></span>
                @error('nombres')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Apellidos <span class="text-error">*</span></span>
                    <input type="text" name="apellidos" required maxlength="100"
                        @input="apellidos.value = $event.target.value.toUpperCase()" x-model="apellidos.value" @blur="apellidos.blurred = true" x-effect="apellidos.errorMessage = getErrorMessage(apellidos.value, 'apellidos')"
                        :class="{ 'border-slate-300 focus:border-primary dark:border-navy-450': !apellidos.blurred, 'border-error': apellidos.blurred && apellidos.errorMessage, 'border-success': apellidos.blurred && !apellidos.errorMessage }"
                        class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('apellidos') border-error @enderror" />
                </label>
                <span class="mt-1 text-xs text-error" x-show="apellidos.blurred && apellidos.errorMessage" x-text="apellidos.errorMessage"></span>
                @error('apellidos')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">CI</span>
                    <input type="text" name="ci"
                        x-model="ci.value" @blur="ci.blurred = true" x-effect="ci.errorMessage = getErrorMessage(ci.value, 'ci')"
                        :class="{ 'border-slate-300 focus:border-primary dark:border-navy-450': !ci.blurred, 'border-error': ci.blurred && ci.errorMessage, 'border-success': ci.blurred && !ci.errorMessage && ci.value }"
                        class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('ci') border-error @enderror" />
                </label>
                <span class="mt-1 text-xs text-error" x-show="ci.blurred && ci.errorMessage" x-text="ci.errorMessage"></span>
                @error('ci')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Teléfono</span>
                    <input type="text" name="telefono" maxlength="8"
                        x-model="telefono.value" @blur="telefono.blurred = true" x-effect="telefono.errorMessage = getErrorMessage(telefono.value, 'telefono')"
                        :class="{ 'border-slate-300 focus:border-primary dark:border-navy-450': !telefono.blurred, 'border-error': telefono.blurred && telefono.errorMessage, 'border-success': telefono.blurred && !telefono.errorMessage && telefono.value }"
                        class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('telefono') border-error @enderror" />
                </label>
                <span class="mt-1 text-xs text-error" x-show="telefono.blurred && telefono.errorMessage" x-text="telefono.errorMessage"></span>
                @error('telefono')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Email</span>
                    <input type="email" name="email"
                        x-model="email.value" @blur="email.blurred = true" x-effect="email.errorMessage = getErrorMessage(email.value, 'email')"
                        :class="{ 'border-slate-300 focus:border-primary dark:border-navy-450': !email.blurred, 'border-error': email.blurred && email.errorMessage, 'border-success': email.blurred && !email.errorMessage && email.value }"
                        class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('email') border-error @enderror" />
                </label>
                <span class="mt-1 text-xs text-error" x-show="email.blurred && email.errorMessage" x-text="email.errorMessage"></span>
                @error('email')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Matrícula profesional <span class="text-error">*</span></span>
                    <input type="text" name="matricula_profesional" required maxlength="50"
                        @input="matricula_profesional.value = $event.target.value.toUpperCase()" x-model="matricula_profesional.value" @blur="matricula_profesional.blurred = true" x-effect="matricula_profesional.errorMessage = getErrorMessage(matricula_profesional.value, 'matricula_profesional')"
                        :class="{ 'border-slate-300 focus:border-primary dark:border-navy-450': !matricula_profesional.blurred, 'border-error': matricula_profesional.blurred && matricula_profesional.errorMessage, 'border-success': matricula_profesional.blurred && !matricula_profesional.errorMessage }"
                        class="form-input mt-1.5 w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none dark:bg-navy-700 dark:text-navy-100 @error('matricula_profesional') border-error @enderror" />
                </label>
                <span class="mt-1 text-xs text-error" x-show="matricula_profesional.blurred && matricula_profesional.errorMessage" x-text="matricula_profesional.errorMessage"></span>
                @error('matricula_profesional')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Estado <span class="text-error">*</span></span>
                    <select name="estado" required x-model="estado.value"
                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </label>
            </div>
        </div>
    </div>

    {{-- Especialidades --}}
    <div class="card p-4 sm:p-5">
        <span class="text-base font-medium text-slate-700 dark:text-navy-100 mb-5 block" :class="especialidadesErrorMessage ? 'text-error' : ''">Especialidades</span>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($especialidades as $esp)
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" name="especialidades[]" value="{{ $esp->id_especialidad }}"
                    :checked="especialidadesSeleccionadas.includes('{{ $esp->id_especialidad }}')"
                    @change="$event.target.checked ? especialidadesSeleccionadas.push('{{ $esp->id_especialidad }}') : especialidadesSeleccionadas = especialidadesSeleccionadas.filter(i => i !== '{{ $esp->id_especialidad }}'); validarEspecialidades()"
                    class="form-checkbox is-basic size-5 rounded border-slate-400/70 checked:border-primary checked:bg-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:border-accent dark:checked:bg-accent dark:hover:border-accent dark:focus:border-accent" />
                <span class="text-sm text-slate-600 dark:text-navy-200">{{ $esp->nombre_especialidad }}</span>
            </label>
            @endforeach
        </div>
        <span class="text-xs text-error block mt-2" x-show="especialidadesErrorMessage" x-text="especialidadesErrorMessage"></span>
        @error('especialidades')<p class="mt-2 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    {{-- Horarios --}}
    <div class="card p-4 sm:p-5">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Horarios de Atención</h3>
            <button type="button" @click="agregarHorario()"
                class="btn h-8 bg-primary px-3 text-xs font-medium text-white hover:bg-primary-focus">
                + Agregar Horario
            </button>
        </div>

        <template x-if="horarios.length === 0">
            <p class="text-sm text-slate-400 dark:text-navy-300 text-center py-4">No se han agregado horarios. Haga clic en "+ Agregar Horario".</p>
        </template>

        <template x-for="(horario, index) in horarios" :key="index">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-5 items-end border-b border-slate-150 dark:border-navy-500 pb-4 mb-4">
                <label class="block">
                    <span class="text-xs font-medium text-slate-500 dark:text-navy-200">Día</span>
                    <select :name="'horarios['+index+'][dia_semana]'" x-model="horario.dia_semana" required
                        class="form-select mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                        @foreach($diasSemana as $num => $nombre)
                            <option value="{{ $num }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-500 dark:text-navy-200">Hora inicio</span>
                    <input type="time" :name="'horarios['+index+'][hora_inicio]'" x-model="horario.hora_inicio" required
                        class="form-input mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-500 dark:text-navy-200">Hora fin</span>
                    <input type="time" :name="'horarios['+index+'][hora_fin]'" x-model="horario.hora_fin" required
                        class="form-input mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-500 dark:text-navy-200">Duración cita (min)</span>
                    <input type="number" :name="'horarios['+index+'][duracion_cita_minutos]'" x-model="horario.duracion_cita_minutos" min="5" max="240"
                        class="form-input mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
                </label>
                <div class="flex items-end">
                    <button type="button" @click="horarios.splice(index, 1)"
                        class="btn size-9 rounded-full bg-error/10 p-0 text-error hover:bg-error/20">
                        <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </template>

        @error('horarios')<p class="mt-2 text-xs text-error">{{ $message }}</p>@enderror
    </div>

    {{-- Botones --}}
    <div class="flex justify-end gap-3">
        <a href="{{ route('medicos.index') }}" class="btn h-9 border border-slate-300 px-5 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-600">
            Cancelar
        </a>
        <button type="submit" class="btn h-9 bg-primary px-5 text-sm font-medium text-white hover:bg-primary-focus">
            Actualizar Médico
        </button>
    </div>
</form>

@php
    $horariosIniciales = old('horarios', $medico->horarios->map(fn($h) => [
        'dia_semana'            => $h->dia_semana,
        'hora_inicio'           => substr($h->hora_inicio, 0, 5),
        'hora_fin'              => substr($h->hora_fin, 0, 5),
        'duracion_cita_minutos' => $h->duracion_cita_minutos,
    ])->values()->toArray());
@endphp
<script>
function medicoForm() {
    return {
        id_usuario: { value: '{{ old('id_usuario', $medico->id_usuario) }}', errorMessage: '', blurred: false },
        codigo_medico: { value: '{{ old('codigo_medico', $medico->codigo_medico) }}', errorMessage: '', blurred: false },
        nombres: { value: '{{ old('nombres', $medico->nombres) }}', errorMessage: '', blurred: false },
        apellidos: { value: '{{ old('apellidos', $medico->apellidos) }}', errorMessage: '', blurred: false },
        ci: { value: '{{ old('ci', $medico->ci) }}', errorMessage: '', blurred: false },
        telefono: { value: '{{ old('telefono', $medico->telefono) }}', errorMessage: '', blurred: false },
        email: { value: '{{ old('email', $medico->email) }}', errorMessage: '', blurred: false },
        matricula_profesional: { value: '{{ old('matricula_profesional', $medico->matricula_profesional) }}', errorMessage: '', blurred: false },
        estado: { value: '{{ old('estado', $medico->estado) }}', errorMessage: '', blurred: false },
        especialidadesSeleccionadas: @json(old('especialidades', $especialidadesSeleccionadas)).map(String),
        especialidadesErrorMessage: '',

        horarios: @json($horariosIniciales),
        agregarHorario() {
            this.horarios.push({
                dia_semana: 1,
                hora_inicio: '08:00',
                hora_fin: '12:00',
                duracion_cita_minutos: 30
            });
        },
        getErrorMessage(value, field) {
            if (['id_usuario', 'codigo_medico'].includes(field)) {
                if (!value) return 'Este campo es requerido';
            }
            if (['nombres', 'apellidos'].includes(field)) {
                if (!value) return 'Este campo es requerido';
                if (!/^[A-ZÑÁÉÍÓÚ\s]+$/.test(value)) return 'Solo letras en mayúscula y espacios';
            }
            if (field === 'ci') {
                if (value && !/^[0-9]{7,12}$/.test(value)) return 'Debe tener entre 7 y 12 dígitos';
            }
            if (field === 'telefono') {
                if (value && !/^[0-9]{8}$/.test(value)) return 'Debe tener exactamente 8 dígitos';
            }
            if (field === 'email') {
                if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Correo inválido';
            }
            if (field === 'matricula_profesional') {
                if (!value) return 'La matrícula es requerida';
                if (!/^[A-Z]+-[0-9]+$/.test(value)) return 'Formato: LETRAS-NUMEROS (Ej: MED-123)';
            }
            return '';
        },
        validarEspecialidades() {
            this.especialidadesErrorMessage = this.especialidadesSeleccionadas.length === 0 ? 'Debe seleccionar al menos una especialidad' : '';
        },
        validarSubmit(e) {
            let hasError = false;
            const fields = ['id_usuario', 'codigo_medico', 'nombres', 'apellidos', 'ci', 'telefono', 'email', 'matricula_profesional'];
            
            fields.forEach(f => {
                this[f].blurred = true;
                this[f].errorMessage = this.getErrorMessage(this[f].value, f);
                if (this[f].errorMessage) {
                    hasError = true;
                }
            });
            
            this.validarEspecialidades();
            if (this.especialidadesErrorMessage) hasError = true;

            if (this.horarios.length === 0) {
                hasError = true;
                alert('Debe agregar al menos un horario de atención.');
            }

            if (hasError) {
                e.preventDefault();
                setTimeout(() => {
                    const firstError = this.$el.querySelector('.border-error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 100);
            }
        }
    };
}
</script>
@endsection
