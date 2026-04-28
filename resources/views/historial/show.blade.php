@extends('layouts.app')
@section('title', 'Detalle Historial Clinico')

@section('content')
<div class="space-y-6">
    <div class="card p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">
                    <span class="badge rounded-full bg-info/10 px-3 py-1 text-info dark:bg-info/15">Historial Clínico</span>
                </h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-navy-300">
                    {{ $paciente->codigo_paciente }} - {{ $paciente->nombre_completo }}
                </p>
                <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-slate-600 dark:text-navy-200 sm:grid-cols-3">
                    <div><span class="font-medium">Nombre y apellido:</span> {{ $paciente->nombres }} {{ $paciente->apellidos }}</div>
                    <div><span class="font-medium">Teléfono:</span> {{ $paciente->telefono ?? '—' }}</div>
                    <div><span class="font-medium">Correo:</span> {{ $paciente->email ?? '—' }}</div>
                    <div><span class="font-medium">Alergias:</span> {{ $paciente->alergias ?? '—' }}</div>
                    <div class="sm:col-span-2"><span class="font-medium">Observaciones generales:</span> {{ $paciente->observaciones_generales ?? '—' }}</div>
                    <div class="sm:col-span-3"><span class="font-medium">Contacto de emergencia:</span> {{ $paciente->contacto_emergencia_nombre ?? '—' }}{{ $paciente->contacto_emergencia_telefono ? ' - ' . $paciente->contacto_emergencia_telefono : '' }}</div>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ url()->previous() && url()->previous() !== url()->current() ? url()->previous() : route('historial.index') }}" class="btn border border-slate-300 px-4 text-sm hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Volver</a>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-700 dark:text-navy-100">
            <span class="badge rounded-full bg-warning/10 px-3 py-1 text-warning dark:bg-warning/15">Citas médicas</span>
        </h3>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 dark:border-navy-500">
                    <tr class="text-slate-600 dark:text-navy-200">
                        <th class="px-3 py-2">Código</th>
                        <th class="px-3 py-2">Fecha</th>
                        <th class="px-3 py-2">Médico</th>
                        <th class="px-3 py-2">Estado</th>
                        <th class="px-3 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-navy-600">
                    @forelse ($citas as $cita)
                        <tr>
                            <td class="px-3 py-2 font-medium text-slate-700 dark:text-navy-100">{{ $cita->codigo_cita }}</td>
                            <td class="px-3 py-2 text-slate-600 dark:text-navy-200">
                                {{ $cita->fecha_cita?->format('d/m/Y') }}
                                {{ $cita->hora_inicio ? substr($cita->hora_inicio, 0, 5) : '' }} - {{ $cita->hora_fin ? substr($cita->hora_fin, 0, 5) : '' }}
                            </td>
                            <td class="px-3 py-2 text-slate-700 dark:text-navy-100">{{ $cita->medico?->nombre_completo ?? '—' }}</td>
                            <td class="px-3 py-2">
                                <span class="badge rounded-full bg-slate-200 px-3 py-1 text-xs text-slate-700 dark:bg-navy-600 dark:text-navy-100">
                                    {{ $cita->estado_label }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('citas.show', $cita->id_cita) }}" class="btn border border-slate-300 px-3 text-xs hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-10 text-center text-slate-500 dark:text-navy-300">No hay citas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-700 dark:text-navy-100">
            <span class="badge rounded-full bg-success/10 px-3 py-1 text-success dark:bg-success/15">Consultas médicas</span>
        </h3>

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
