@extends('layouts.app')
@section('title', 'Editar Horarios Medicos')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('horarios.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Horarios</span>
</div>

<div class="card max-w-2xl p-4 sm:p-5">
    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-6">Editar Horario</h3>
    <form method="POST" action="{{ route('horarios.update', $horario->id_horario) }}" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="block sm:col-span-2">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Médico <span class="text-error">*</span></span>
                <select name="id_medico" required
                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('id_medico') border-error @enderror">
                    @foreach($medicos as $medico)
                        <option value="{{ $medico->id_medico }}" {{ (string)old('id_medico', $horario->id_medico) === (string)$medico->id_medico ? 'selected' : '' }}>
                            {{ $medico->nombre_completo }} ({{ $medico->codigo_medico }})
                        </option>
                    @endforeach
                </select>
                @error('id_medico')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Día <span class="text-error">*</span></span>
                <select name="dia_semana" required
                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('dia_semana') border-error @enderror">
                    @foreach($dias as $k => $v)
                        <option value="{{ $k }}" {{ (string)old('dia_semana', $horario->dia_semana) === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                @error('dia_semana')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Duración cita (min) <span class="text-error">*</span></span>
                <input type="number" name="duracion_cita_minutos" min="5" max="480" value="{{ old('duracion_cita_minutos', $horario->duracion_cita_minutos) }}" required
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('duracion_cita_minutos') border-error @enderror" />
                @error('duracion_cita_minutos')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Hora inicio <span class="text-error">*</span></span>
                <input type="time" name="hora_inicio" value="{{ old('hora_inicio', substr($horario->hora_inicio, 0, 5)) }}" required
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('hora_inicio') border-error @enderror" />
                @error('hora_inicio')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Hora fin <span class="text-error">*</span></span>
                <input type="time" name="hora_fin" value="{{ old('hora_fin', substr($horario->hora_fin, 0, 5)) }}" required
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('hora_fin') border-error @enderror" />
                @error('hora_fin')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Activo</span>
                <select name="activo"
                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                    <option value="1" {{ old('activo', $horario->activo ? '1' : '0') === '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ old('activo', $horario->activo ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
                </select>
            </label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn bg-primary px-5 text-sm font-medium text-white hover:bg-primary-focus">
                Actualizar
            </button>
            <a href="{{ route('horarios.index') }}" class="btn border border-slate-300 px-5 text-sm font-medium hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
