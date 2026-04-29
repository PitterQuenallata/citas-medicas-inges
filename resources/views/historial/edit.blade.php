@extends('layouts.app')
@section('title', 'Editar Historial Clinico')

@section('content')
<div class="card p-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Editar consulta médica</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-navy-300">
                {{ $consulta->paciente?->codigo_paciente }} - {{ $consulta->paciente?->nombre_completo }}
            </p>
            <p class="mt-1 text-xs text-slate-500 dark:text-navy-300">
                Cita: {{ $consulta->cita?->codigo_cita ?? '—' }}
            </p>
        </div>

        <a href="{{ route('historial.show', $consulta->paciente) }}" class="btn border border-slate-300 px-4 text-sm hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Volver</a>
    </div>

    <form action="{{ route('historial.consultas.update', $consulta) }}" method="POST" class="mt-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Fecha de consulta</label>
                <input
                    type="datetime-local"
                    name="fecha_consulta"
                    value="{{ old('fecha_consulta', optional($consulta->fecha_consulta)->format('Y-m-d\\TH:i')) }}"
                    class="form-input mt-1 w-full"
                />
                @error('fecha_consulta')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Peso (kg)</label>
                <input type="number" step="0.01" name="peso" value="{{ old('peso', $consulta->peso) }}" class="form-input mt-1 w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Talla (cm)</label>
                <input type="number" step="0.01" name="talla" value="{{ old('talla', $consulta->talla) }}" class="form-input mt-1 w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Presión arterial</label>
                <input type="text" name="presion_arterial" value="{{ old('presion_arterial', $consulta->presion_arterial) }}" class="form-input mt-1 w-full" placeholder="120/80" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Temperatura (°C)</label>
                <input type="number" step="0.01" name="temperatura" value="{{ old('temperatura', $consulta->temperatura) }}" class="form-input mt-1 w-full" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Motivo de consulta</label>
                <textarea name="motivo_consulta" rows="3" class="form-textarea mt-1 w-full">{{ old('motivo_consulta', $consulta->motivo_consulta) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Síntomas</label>
                <textarea name="sintomas" rows="3" class="form-textarea mt-1 w-full">{{ old('sintomas', $consulta->sintomas) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Diagnóstico</label>
                <textarea name="diagnostico" rows="4" class="form-textarea mt-1 w-full">{{ old('diagnostico', $consulta->diagnostico) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Tratamiento</label>
                <textarea name="tratamiento" rows="4" class="form-textarea mt-1 w-full">{{ old('tratamiento', $consulta->tratamiento) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Receta</label>
                <textarea name="receta" rows="4" class="form-textarea mt-1 w-full">{{ old('receta', $consulta->receta) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Observaciones médicas</label>
                <textarea name="observaciones_medicas" rows="4" class="form-textarea mt-1 w-full">{{ old('observaciones_medicas', $consulta->observaciones_medicas) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('historial.show', $consulta->paciente) }}" class="btn border border-slate-300 px-5 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Cancelar</a>
            <button type="submit" class="btn bg-primary px-5 text-white hover:bg-primary-focus">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection
