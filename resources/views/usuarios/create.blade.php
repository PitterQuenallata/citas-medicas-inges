@extends('layouts.app')
@section('title', 'Nuevo Usuarios')
@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('usuarios.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Usuarios</span>
</div>

<div class="card max-w-2xl p-4 sm:p-5">
    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-6">Registrar Nuevo Usuario</h3>
    <form method="POST" action="{{ route('usuarios.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Nombre <span class="text-error">*</span></span>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('nombre') border-error @enderror" />
                @error('nombre')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Apellido <span class="text-error">*</span></span>
                <input type="text" name="apellido" value="{{ old('apellido') }}" required
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('apellido') border-error @enderror" />
                @error('apellido')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block sm:col-span-2">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Email <span class="text-error">*</span></span>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('email') border-error @enderror" />
                @error('email')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Teléfono</span>
                <input type="text" name="telefono" value="{{ old('telefono') }}"
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('telefono') border-error @enderror" />
                @error('telefono')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Estado</span>
                <select name="estado"
                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                    <option value="activo" {{ old('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    <option value="bloqueado" {{ old('estado') === 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                </select>
            </label>
            <label class="block sm:col-span-2">
                <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Contraseña <span class="text-error">*</span></span>
                <input type="password" name="password" required
                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 @error('password') border-error @enderror" />
                @error('password')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn bg-primary px-5 text-sm font-medium text-white hover:bg-primary-focus">
                Guardar
            </button>
            <a href="{{ route('usuarios.index') }}" class="btn border border-slate-300 px-5 text-sm font-medium hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
