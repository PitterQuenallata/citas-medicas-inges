@extends('layouts.app')
@section('title', 'Reportes')
@section('content')
<div class="space-y-6">
    <div class="card p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-700 dark:text-navy-100">Reportes</h2>
                <p class="text-sm text-slate-500 dark:text-navy-300">Reporte de citas con filtros por fecha, estado, médico y especialidad.</p>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('reportes.index', array_merge(request()->except('page'), ['tipo' => 'citas'])) }}"
                class="btn h-9 px-4 text-sm {{ ($tipo ?? 'citas') === 'citas' ? 'bg-primary text-white hover:bg-primary-focus' : 'border border-slate-300 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600' }}">
                Citas
            </a>
            <a href="{{ route('reportes.index', array_merge(request()->except('page'), ['tipo' => 'pacientes'])) }}"
                class="btn h-9 px-4 text-sm {{ ($tipo ?? 'citas') === 'pacientes' ? 'bg-primary text-white hover:bg-primary-focus' : 'border border-slate-300 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600' }}">
                Pacientes
            </a>
            <a href="{{ route('reportes.index', array_merge(request()->except('page'), ['tipo' => 'medicos'])) }}"
                class="btn h-9 px-4 text-sm {{ ($tipo ?? 'citas') === 'medicos' ? 'bg-primary text-white hover:bg-primary-focus' : 'border border-slate-300 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600' }}">
                Médicos
            </a>
        </div>

        <form method="GET" class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-12">
            <input type="hidden" name="tipo" value="{{ $tipo ?? 'citas' }}" />
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}" class="form-input mt-1 w-full" />
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}" class="form-input mt-1 w-full" />
            </div>

            @if (($tipo ?? 'citas') === 'citas')
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Estado</label>
                    <select name="estado" class="form-select mt-1 w-full">
                        <option value="">Todos</option>
                        @foreach (['pendiente','confirmada','atendida','cancelada','reprogramada','no_asistio'] as $st)
                            <option value="{{ $st }}" @selected($estado === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Médico</label>
                    <select name="id_medico" class="form-select mt-1 w-full">
                        <option value="">Todos</option>
                        @foreach ($medicos as $medico)
                            <option value="{{ $medico->id_medico }}" @selected((string) $idMedico === (string) $medico->id_medico)>
                                {{ $medico->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Especialidad</label>
                    <select name="id_especialidad" class="form-select mt-1 w-full">
                        <option value="">Todas</option>
                        @foreach ($especialidades as $esp)
                            <option value="{{ $esp->id_especialidad }}" @selected((string) $idEspecialidad === (string) $esp->id_especialidad)>
                                {{ $esp->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if (($tipo ?? 'citas') === 'medicos')
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100">Estado</label>
                    <select name="estado" class="form-select mt-1 w-full">
                        <option value="">Todos</option>
                        @foreach (['pendiente','confirmada','atendida','cancelada','reprogramada','no_asistio'] as $st)
                            <option value="{{ $st }}" @selected($estado === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="md:col-span-12 flex justify-end gap-2">
                <a href="{{ route('reportes.index', ['tipo' => ($tipo ?? 'citas')]) }}" class="btn border border-slate-300 px-4 hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">Limpiar</a>
                <button type="submit" class="btn bg-primary px-4 text-white hover:bg-primary-focus">Aplicar filtros</button>
            </div>
        </form>
    </div>

    @if (($tipo ?? 'citas') === 'citas')
        <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
            @php
                $estados = ['pendiente','confirmada','atendida','cancelada','reprogramada','no_asistio'];
            @endphp
            @foreach ($estados as $st)
                <div class="card p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-navy-300">{{ $st }}</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $totales[$st] ?? 0 }}</p>
                </div>
            @endforeach
        </div>

        <div class="card p-6">
            <h3 class="text-base font-semibold text-slate-700 dark:text-navy-100">Citas</h3>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-200 dark:border-navy-500">
                        <tr class="text-slate-600 dark:text-navy-200">
                            <th class="px-3 py-2">Código</th>
                            <th class="px-3 py-2">Fecha</th>
                            <th class="px-3 py-2">Paciente</th>
                            <th class="px-3 py-2">Médico</th>
                            <th class="px-3 py-2">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-navy-600">
                        @forelse ($citas as $cita)
                            <tr>
                                <td class="px-3 py-2 font-medium text-slate-700 dark:text-navy-100">{{ $cita->codigo_cita }}</td>
                                <td class="px-3 py-2 text-slate-600 dark:text-navy-200">{{ $cita->fecha_cita }} {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</td>
                                <td class="px-3 py-2 text-slate-700 dark:text-navy-100">{{ $cita->paciente?->nombre_completo ?? '—' }}</td>
                                <td class="px-3 py-2 text-slate-700 dark:text-navy-100">{{ $cita->medico?->nombre_completo ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    <span class="badge rounded-full bg-slate-200 px-3 py-1 text-xs text-slate-700 dark:bg-navy-600 dark:text-navy-100">
                                        {{ $cita->estado_cita }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-10 text-center text-slate-500 dark:text-navy-300">No hay datos para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $citas->links() }}
            </div>
        </div>
    @endif

    @if (($tipo ?? 'citas') === 'pacientes')
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="card p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-navy-300">Activos</p>
                <p class="mt-2 text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $pacientesTotales['activo'] ?? 0 }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-navy-300">Inactivos</p>
                <p class="mt-2 text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $pacientesTotales['inactivo'] ?? 0 }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-navy-300">Total</p>
                <p class="mt-2 text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ ($pacientesTotales['activo'] ?? 0) + ($pacientesTotales['inactivo'] ?? 0) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-700 dark:text-navy-100">Pacientes</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-slate-200 dark:border-navy-500">
                            <tr class="text-slate-600 dark:text-navy-200">
                                <th class="px-3 py-2">Código</th>
                                <th class="px-3 py-2">Paciente</th>
                                <th class="px-3 py-2">CI</th>
                                <th class="px-3 py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-navy-600">
                            @forelse ($pacientes as $pac)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-slate-700 dark:text-navy-100">{{ $pac->codigo_paciente }}</td>
                                    <td class="px-3 py-2 text-slate-700 dark:text-navy-100">{{ $pac->nombre_completo }}</td>
                                    <td class="px-3 py-2 text-slate-600 dark:text-navy-200">{{ $pac->ci }}</td>
                                    <td class="px-3 py-2 text-slate-600 dark:text-navy-200">{{ $pac->estado }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-10 text-center text-slate-500 dark:text-navy-300">No hay pacientes en el rango seleccionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $pacientes->links() }}
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-700 dark:text-navy-100">Top 10 pacientes por número de citas</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-slate-200 dark:border-navy-500">
                            <tr class="text-slate-600 dark:text-navy-200">
                                <th class="px-3 py-2">Paciente</th>
                                <th class="px-3 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-navy-600">
                            @forelse ($topPacientes as $row)
                                <tr>
                                    <td class="px-3 py-2 text-slate-700 dark:text-navy-100">{{ $row->paciente?->nombre_completo ?? '—' }}</td>
                                    <td class="px-3 py-2 text-right font-medium text-slate-700 dark:text-navy-100">{{ $row->total }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-3 py-10 text-center text-slate-500 dark:text-navy-300">Sin datos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if (($tipo ?? 'citas') === 'medicos')
        <div class="card p-6">
            <h3 class="text-base font-semibold text-slate-700 dark:text-navy-100">Citas por médico</h3>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-200 dark:border-navy-500">
                        <tr class="text-slate-600 dark:text-navy-200">
                            <th class="px-3 py-2">Médico</th>
                            <th class="px-3 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-navy-600">
                        @forelse ($reporteMedicos as $row)
                            <tr>
                                <td class="px-3 py-2 text-slate-700 dark:text-navy-100">{{ $row->medico?->nombre_completo ?? '—' }}</td>
                                <td class="px-3 py-2 text-right font-medium text-slate-700 dark:text-navy-100">{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-3 py-10 text-center text-slate-500 dark:text-navy-300">Sin datos para el rango/filtros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
