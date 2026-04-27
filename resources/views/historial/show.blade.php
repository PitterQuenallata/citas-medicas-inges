@extends('layouts.app')
@section('title', 'Detalle Historial Clinico')

@section('content')
<div class="space-y-6">
    <div class="card p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Historial Clínico</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-navy-300">
                    {{ $paciente->codigo_paciente }} - {{ $paciente->nombre_completo }}
                </p>
                <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-slate-600 dark:text-navy-200 sm:grid-cols-3">
                    <div><span class="font-medium">CI:</span> {{ $paciente->ci }}</div>
                    <div><span class="font-medium">Teléfono:</span> {{ $paciente->telefono }}</div>
                    <div><span class="font-medium">Email:</span> {{ $paciente->email }}</div>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('historial.index') }}" class="btn border border-slate-300 px-4 text-sm hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Volver</a>
                <a href="{{ route('historial.consultas.create', $paciente) }}" class="btn bg-primary px-4 text-sm text-white hover:bg-primary-focus">Nueva consulta</a>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-700 dark:text-navy-100">Consultas médicas</h3>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 dark:border-navy-500">
                    <tr class="text-slate-600 dark:text-navy-200">
                        <th class="px-3 py-2">Fecha</th>
                        <th class="px-3 py-2">Médico</th>
                        <th class="px-3 py-2">Diagnóstico</th>
                        <th class="px-3 py-2">Receta</th>
                        <th class="px-3 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-navy-600">
                    @forelse ($consultas as $consulta)
                        <tr>
                            <td class="px-3 py-2 text-slate-700 dark:text-navy-100">
                                {{ optional($consulta->fecha_consulta)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-3 py-2 text-slate-700 dark:text-navy-100">
                                {{ $consulta->medico?->nombre_completo ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-slate-600 dark:text-navy-200">
                                {{ \Illuminate\Support\Str::limit($consulta->diagnostico, 60) }}
                            </td>
                            <td class="px-3 py-2 text-slate-600 dark:text-navy-200">
                                {{ \Illuminate\Support\Str::limit($consulta->receta, 60) }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('historial.consultas.edit', $consulta) }}" class="btn border border-slate-300 px-3 text-xs hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-10 text-center text-slate-500 dark:text-navy-300">Aún no hay consultas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $consultas->links() }}
        </div>
    </div>
</div>
@endsection
