@extends('layouts.app')
@section('title', 'Pacientes')

@section('content')
<div class="flex items-center justify-between py-2 pb-4">
    <form method="GET" class="flex gap-2">
        <input type="text" name="buscar" value="{{ request('buscar') }}"
            placeholder="Buscar por nombre, apellido o CI..."
            class="form-input h-9 w-72 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 placeholder-slate-400 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
        <button type="submit" class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
            Buscar
        </button>
        @if(request('buscar'))
            <a href="{{ route('pacientes.index') }}" class="btn h-9 px-4 text-sm font-medium border border-slate-300 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                Limpiar
            </a>
        @endif
    </form>
    <a href="{{ route('pacientes.create') }}" class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
        + Nuevo Paciente
    </a>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">#</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Nombre</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">CI</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Teléfono</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pacientes as $paciente)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $paciente->id_paciente }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 font-medium text-slate-700 dark:text-navy-100">
                        {{ $paciente->nombres }} {{ $paciente->apellidos }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $paciente->ci ?? '—' }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $paciente->telefono ?? '—' }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        @if($paciente->estado === 'activo')
                            <span class="badge rounded-full bg-success/10 text-success dark:bg-success/15">Activo</span>
                        @else
                            <span class="badge rounded-full bg-error/10 text-error dark:bg-error/15">Inactivo</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <div class="flex gap-2">
                            <a href="{{ route('pacientes.show', $paciente->id_paciente) }}"
                                class="btn size-8 rounded-full p-0 text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-500" title="Ver">
                                <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('pacientes.edit', $paciente->id_paciente) }}"
                                class="btn size-8 rounded-full p-0 text-primary hover:bg-primary/10" title="Editar">
                                <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($paciente->estado === 'activo')
                                <form method="POST" action="{{ route('pacientes.destroy', $paciente->id_paciente) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn size-8 rounded-full p-0 text-error hover:bg-error/10" title="Desactivar">
                                        <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('pacientes.activar', $paciente->id_paciente) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn size-8 rounded-full p-0 text-success hover:bg-success/10" title="Activar">
                                        <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-slate-400 dark:text-navy-300">
                        No se encontraron pacientes.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $pacientes->links() }}
    </div>
</div>
@endsection

@if(session('swal_success'))
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    title: 'Listo',
                    text: @json(session('swal_success')),
                    icon: 'success',
                });
            });
        </script>
    @endpush
@endif
