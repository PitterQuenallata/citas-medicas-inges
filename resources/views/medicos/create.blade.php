@extends('layouts.app')
@section('title', 'Nuevo Médico')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('medicos.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Médicos</span>
</div>

<form method="POST" action="{{ route('medicos.store') }}" x-data="medicoForm()" class="space-y-6">
    @csrf

    {{-- Datos principales --}}
    <div class="card p-4 sm:p-5">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-5">Datos del Médico</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Usuario vinculado <span class="text-error">*</span></span>
                <select name="id_usuario" required
                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('id_usuario') border-error @enderror">
                    <option value="">Seleccionar usuario...</option>
                    @foreach($usuariosSinMedico as $usuario)
                        <option value="{{ $usuario->id }}" {{ old('id_usuario') == $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->nombre }} {{ $usuario->apellido }} ({{ $usuario->email }})
                        </option>
                    @endforeach
                </select>
                @error('id_usuario')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Código médico <span class="text-error">*</span></span>
                <input type="text" name="codigo_medico" value="{{ old('codigo_medico', $codigoSugerido) }}" required
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('codigo_medico') border-error @enderror" />
                @error('codigo_medico')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Nombres <span class="text-error">*</span></span>
                <input type="text" name="nombres" value="{{ old('nombres') }}" required
                    maxlength="100" pattern="[A-Za-z\u00e1\u00e9\u00ed\u00f3\u00fa\u00fc\u00f1\u00c1\u00c9\u00cd\u00d3\u00da\u00dc\u00d1 ]+" title="Solo letras y espacios"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('nombres') border-error @enderror" />
                @error('nombres')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Apellidos <span class="text-error">*</span></span>
                <input type="text" name="apellidos" value="{{ old('apellidos') }}" required
                    maxlength="100" pattern="[A-Za-z\u00e1\u00e9\u00ed\u00f3\u00fa\u00fc\u00f1\u00c1\u00c9\u00cd\u00d3\u00da\u00dc\u00d1 ]+" title="Solo letras y espacios"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('apellidos') border-error @enderror" />
                @error('apellidos')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">CI</span>
                <input type="text" name="ci" value="{{ old('ci') }}"
                    pattern="[0-9]{7,12}" minlength="7" maxlength="12" title="Solo d\u00edgitos, entre 7 y 12 caracteres"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('ci') border-error @enderror" />
                @error('ci')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Teléfono</span>
                <input type="text" name="telefono" value="{{ old('telefono') }}"
                    pattern="[0-9]{8}" minlength="8" maxlength="8" title="Exactamente 8 d\u00edgitos num\u00e9ricos"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('telefono') border-error @enderror" />
                @error('telefono')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Email</span>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('email') border-error @enderror" />
                @error('email')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Matrícula profesional <span class="text-error">*</span></span>
                <input type="text" name="matricula_profesional" value="{{ old('matricula_profesional') }}" required
                    maxlength="50" pattern="[A-Z]+-[0-9]+" title="Formato: LETRAS-N\u00daMEROS en may\u00fasculas (ej: MED-12345)"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('matricula_profesional') border-error @enderror" />
                @error('matricula_profesional')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Estado <span class="text-error">*</span></span>
                <select name="estado" required
                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                    <option value="activo" {{ old('estado', 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </label>
        </div>
    </div>

    {{-- Especialidades --}}
    <div class="card p-4 sm:p-5">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-5">Especialidades</h3>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($especialidades as $esp)
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" name="especialidades[]" value="{{ $esp->id_especialidad }}"
                    {{ in_array($esp->id_especialidad, old('especialidades', [])) ? 'checked' : '' }}
                    class="form-checkbox is-basic size-5 rounded border-slate-400/70 checked:border-primary checked:bg-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:border-accent dark:checked:bg-accent dark:hover:border-accent dark:focus:border-accent" />
                <span class="text-sm text-slate-600 dark:text-navy-200">{{ $esp->nombre_especialidad }}</span>
            </label>
            @endforeach
        </div>
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
            Guardar Médico
        </button>
    </div>
</form>

<script>
function medicoForm() {
    return {
        horarios: @json(old('horarios', [])),
        agregarHorario() {
            this.horarios.push({
                dia_semana: 1,
                hora_inicio: '08:00',
                hora_fin: '12:00',
                duracion_cita_minutos: 30
            });
        }
    };
}
</script>
@endsection
