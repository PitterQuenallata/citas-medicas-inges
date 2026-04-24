@extends('layouts.app')

@section('title', 'Gestión de Citas')

@section('content')

{{-- Encabezado --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#0f172a;">Gestión de Citas</h4>
        <p class="text-muted small mb-0">{{ $citas->total() }} cita(s) encontradas</p>
    </div>
    <a href="{{ route('citas.create') }}" class="btn btn-brand">
        + Nueva cita
    </a>
</div>

{{-- Filtros --}}
<div class="card-citas mb-4 p-3">
    <form method="GET" action="{{ route('citas.index') }}" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label-citas">Buscar paciente</label>
            <input type="text" name="buscar" class="form-control form-control-sm"
                   placeholder="Nombre, apellido o CI…"
                   value="{{ request('buscar') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label-citas">Fecha</label>
            <input type="date" name="fecha" class="form-control form-control-sm"
                   value="{{ request('fecha') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label-citas">Especialidad</label>
            <select name="especialidad" class="form-select form-select-sm">
                <option value="">Todas</option>
                @foreach($especialidades as $esp)
                    <option value="{{ $esp->id_especialidad }}"
                        {{ request('especialidad') == $esp->id_especialidad ? 'selected' : '' }}>
                        {{ $esp->nombre_especialidad }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label-citas">Médico</label>
            <select name="medico" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach($medicos as $med)
                    <option value="{{ $med->id_medico }}"
                        {{ request('medico') == $med->id_medico ? 'selected' : '' }}>
                        Dr(a). {{ $med->apellidos }}, {{ $med->nombres }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label-citas">Estado</label>
            <select name="estado" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach(['pendiente','confirmada','atendida','cancelada','reprogramada','no_asistio'] as $est)
                    <option value="{{ $est }}" {{ request('estado') === $est ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $est)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-brand btn-sm w-100">Filtrar</button>
            <a href="{{ route('citas.index') }}" class="btn btn-ghost btn-sm">×</a>
        </div>
    </form>
</div>

{{-- Tabla --}}
<div class="card-citas">
    @if($citas->isEmpty())
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="mb-0">No se encontraron citas con los filtros seleccionados.</p>
            <a href="{{ route('citas.create') }}" class="btn btn-brand mt-3">Crear primera cita</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-citas mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citas as $cita)
                    <tr>
                        <td>
                            <span class="fw-semibold" style="font-size:.8125rem;color:#6366f1;">
                                {{ $cita->codigo_cita }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $cita->paciente->apellidos }}, {{ $cita->paciente->nombres }}</div>
                            @if($cita->paciente->ci)
                                <small class="text-muted">{{ $cita->paciente->ci }}</small>
                            @endif
                        </td>
                        <td>
                            <div>Dr(a). {{ $cita->medico->apellidos }}, {{ $cita->medico->nombres }}</div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</td>
                        <td>
                            <span style="font-size:.8125rem;">
                                {{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-estado {{ $cita->badge_class }}">
                                {{ $cita->estado_label }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('citas.show', $cita) }}"
                                   class="btn btn-ghost btn-sm" title="Ver detalle">
                                   Ver
                                </a>
                                @if(!in_array($cita->estado_cita, ['cancelada','atendida','reprogramada']))
                                    <a href="{{ route('citas.edit', $cita) }}"
                                       class="btn btn-ghost btn-sm" title="Editar">
                                       Editar
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($citas->hasPages())
            <div class="p-3 d-flex justify-content-end border-top" style="border-color:var(--border)!important;">
                {{ $citas->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
