@extends('layouts.app')
@section('title', 'Lista de Historial Clinico')

@section('content')
<div class="card p-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Historial Clínico</h2>
            <p class="text-sm text-slate-500 dark:text-navy-300">Busca un paciente para ver su historial de consultas médicas.</p>
        </div>

        <form method="GET" class="flex w-full gap-2 md:w-auto">
            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                placeholder="Buscar por nombre, CI o código"
                class="form-input w-full md:w-80"
            />
            <button class="btn bg-primary px-4 text-white hover:bg-primary-focus" type="submit">Buscar</button>
        </form>
    </div>

    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-slate-200 dark:border-navy-500">
                <tr class="text-slate-600 dark:text-navy-200">
                    <th class="px-3 py-2">Código</th>
                    <th class="px-3 py-2">Paciente</th>
                    <th class="px-3 py-2">CI</th>
                    <th class="px-3 py-2">Estado</th>
                    <th class="px-3 py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-navy-600">
                @forelse ($pacientes as $paciente)
                    <tr>
                        <td class="px-3 py-2 font-medium text-slate-700 dark:text-navy-100">{{ $paciente->codigo_paciente }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-navy-100">{{ $paciente->nombre_completo }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-navy-200">{{ $paciente->ci }}</td>
                        <td class="px-3 py-2">
                            <span class="badge rounded-full {{ ($paciente->estado ?? 'activo') === 'activo' ? 'bg-success/10 text-success' : 'bg-slate-200 text-slate-700 dark:bg-navy-600 dark:text-navy-100' }} px-3 py-1 text-xs">
                                {{ ($paciente->estado ?? 'activo') === 'activo' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('historial.show', $paciente) }}" class="btn border border-slate-300 px-3 text-xs hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                                Ver historial
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-10 text-center text-slate-500 dark:text-navy-300">No se encontraron pacientes.</td>
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
