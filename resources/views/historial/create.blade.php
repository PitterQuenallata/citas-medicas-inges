@extends('layouts.app')
@section('title', 'Nueva Historial Clinico')

@section('content')
<div class="card p-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Nueva consulta médica</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-navy-300">
                {{ $paciente->codigo_paciente }} - {{ $paciente->nombre_completo }}
            </p>
        </div>

        <a href="{{ route('historial.show', $paciente) }}" class="btn border border-slate-300 px-4 text-sm hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Volver</a>
    </div>

    <form action="{{ route('historial.consultas.store', $paciente) }}" method="POST" class="mt-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Cita</label>
            <select name="id_cita" class="form-select mt-1 w-full" required>
                <option value="">Seleccione una cita</option>
                @foreach ($citasDisponibles as $cita)
                    <option value="{{ $cita->id_cita }}" @selected(old('id_cita') == $cita->id_cita)>
                        {{ $cita->codigo_cita }} - {{ $cita->fecha_cita }} {{ $cita->hora_inicio }} - {{ $cita->hora_fin }} ({{ $cita->estado_cita }})
                    </option>
                @endforeach
            </select>
            @error('id_cita')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @if ($citasDisponibles->isEmpty())
                <p class="mt-2 text-xs text-slate-500 dark:text-navy-300">
                    No hay citas disponibles sin consulta registrada.
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Fecha de consulta</label>
                <input type="datetime-local" name="fecha_consulta" value="{{ old('fecha_consulta') }}" class="form-input mt-1 w-full" />
                @error('fecha_consulta')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Peso (kg)</label>
                <input type="number" step="0.01" name="peso" value="{{ old('peso') }}" class="form-input mt-1 w-full" />
                @error('peso')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Talla (cm)</label>
                <input type="number" step="0.01" name="talla" value="{{ old('talla') }}" class="form-input mt-1 w-full" />
                @error('talla')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Presión arterial</label>
                <input type="text" name="presion_arterial" value="{{ old('presion_arterial') }}" class="form-input mt-1 w-full" placeholder="120/80" />
                @error('presion_arterial')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Temperatura (°C)</label>
                <input type="number" step="0.01" name="temperatura" value="{{ old('temperatura') }}" class="form-input mt-1 w-full" />
                @error('temperatura')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Motivo de consulta</label>
                <textarea name="motivo_consulta" rows="3" class="form-textarea mt-1 w-full">{{ old('motivo_consulta') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Síntomas</label>
                <textarea name="sintomas" rows="3" class="form-textarea mt-1 w-full">{{ old('sintomas') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Diagnóstico</label>
                <textarea name="diagnostico" rows="4" class="form-textarea mt-1 w-full">{{ old('diagnostico') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Tratamiento</label>
                <textarea name="tratamiento" rows="4" class="form-textarea mt-1 w-full">{{ old('tratamiento') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Receta</label>
                <textarea name="receta" rows="4" class="form-textarea mt-1 w-full">{{ old('receta') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Observaciones médicas</label>
                <textarea name="observaciones_medicas" rows="4" class="form-textarea mt-1 w-full">{{ old('observaciones_medicas') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('historial.show', $paciente) }}" class="btn border border-slate-300 px-5 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Cancelar</a>
            <button type="submit" class="btn bg-primary px-5 text-white hover:bg-primary-focus" @disabled($citasDisponibles->isEmpty())>
                Guardar
            </button>
        </div>
    </form>
</div>
@endsection
