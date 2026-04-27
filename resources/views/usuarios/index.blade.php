@extends('layouts.app')
@section('title', 'Lista de Usuarios')
@section('content')
<div class="flex items-center justify-between py-2 pb-4">
    <form method="GET" class="flex gap-2">
        <input type="text" name="buscar" value="{{ request('buscar') }}"
            placeholder="Buscar por nombre, email o teléfono..."
            class="form-input h-9 w-80 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 placeholder-slate-400 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
        <button type="submit" class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
            Buscar
        </button>
        @if(request('buscar'))
            <a href="{{ route('usuarios.index') }}" class="btn h-9 px-4 text-sm font-medium border border-slate-300 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                Limpiar
            </a>
        @endif
    </form>
    <a href="{{ route('usuarios.create') }}" class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
        + Nuevo Usuario
    </a>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">#</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Nombre</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Email</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Teléfono</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $usuario->id }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 font-medium text-slate-700 dark:text-navy-100">
                        {{ $usuario->nombre }} {{ $usuario->apellido }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $usuario->email }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $usuario->telefono ?? '—' }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        @if($usuario->estado === 'activo')
                            <span class="badge rounded-full bg-success/10 text-success dark:bg-success/15">Activo</span>
                        @elseif($usuario->estado === 'inactivo')
                            <span class="badge rounded-full bg-error/10 text-error dark:bg-error/15">Inactivo</span>
                        @else
                            <span class="badge rounded-full bg-warning/10 text-warning dark:bg-warning/15">Bloqueado</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <div class="flex gap-2">
                            <a href="{{ route('usuarios.edit', $usuario->id) }}"
                                class="btn size-8 rounded-full p-0 text-primary hover:bg-primary/10" title="Editar">
                                <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-slate-400 dark:text-navy-300">
                        No se encontraron usuarios.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection
