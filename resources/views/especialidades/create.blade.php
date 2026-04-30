@extends('layouts.app')
@section('title', 'Nueva Especialidad')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('especialidades.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Especialidades</span>
</div>

<div class="card max-w-2xl p-4 sm:p-5">
    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-6">Registrar Nueva Especialidad</h3>
    {{-- NUEVO: costo_consulta y estado como fila completa --}}
    <form method="POST" action="{{ route('especialidades.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="block sm:col-span-2">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Nombre <span class="text-error">*</span></span>
                <input type="text" name="nombre_especialidad" value="{{ old('nombre_especialidad') }}" required
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('nombre_especialidad') border-error @enderror" />
                @error('nombre_especialidad')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block sm:col-span-2">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Descripción</span>
                <input type="text" name="descripcion" value="{{ old('descripcion') }}"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('descripcion') border-error @enderror" />
                @error('descripcion')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Costo de consulta (Bs.)</span>
                <input type="number" name="costo_consulta" min="0" max="99999.99" step="0.01"
                    value="{{ old('costo_consulta', '0.00') }}"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('costo_consulta') border-error @enderror" />
                @error('costo_consulta')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Estado</span>
                <select name="estado"
                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                    <option value="activo" {{ old('estado') !== 'inactivo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn bg-primary px-5 text-sm font-medium text-white hover:bg-primary-focus">
                Guardar
            </button>
            <a href="{{ route('especialidades.index') }}" class="btn border border-slate-300 px-5 text-sm font-medium hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
