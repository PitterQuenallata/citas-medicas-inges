{{-- resources/views/medicos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Médicos')

@section('content')
<div class="container-fluid py-4">

    {{-- ── Encabezado ──────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-md me-2 text-primary"></i>Médicos
            </h1>
            <p class="text-muted mb-0">Gestión del personal médico del sistema</p>
        </div>
        <a href="{{ route('medicos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Médico
        </a>
    </div>

    

    {{-- ── Filtros de búsqueda ──────────────────────────────────────────────── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('medicos.index') }}" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="busqueda" class="form-label fw-semibold">Buscar</label>
                    <input type="text"
                           id="busqueda"
                           name="busqueda"
                           class="form-control"
                           placeholder="Nombre, apellido, código, matrícula..."
                           value="{{ $busqueda }}">
                </div>
                <div class="col-md-3">
                    <label for="estado" class="form-label fw-semibold">Estado</label>
                    <select id="estado" name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo"   @selected($filtroEstado === 'activo')>Activo</option>
                        <option value="inactivo" @selected($filtroEstado === 'inactivo')>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tabla de médicos ─────────────────────────────────────────────────── --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Médico</th>
                            <th>Matrícula</th>
                            <th>Especialidades</th>
                            <th>Horarios</th>
                            <th>Contacto</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicos as $medico)
                        <tr>
                            <td>
                                <span class="badge bg-secondary fw-normal font-monospace">
                                    {{ $medico->codigo_medico }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $medico->apellidos }}, {{ $medico->nombres }}</div>
                                @if($medico->ci)
                                    <small class="text-muted">CI: {{ $medico->ci }}</small>
                                @endif
                            </td>
                            <td class="font-monospace small">{{ $medico->matricula_profesional }}</td>
                            <td>
                                @forelse($medico->especialidades as $esp)
                                    <span class="badge bg-info text-dark me-1">
                                        {{ $esp->nombre_especialidad }}
                                    </span>
                                @empty
                                    <span class="text-muted small">Sin especialidad</span>
                                @endforelse
                            </td>
                            <td>
                                @if($medico->horariosActivos->count())
                                    <span class="badge bg-success">
                                        {{ $medico->horariosActivos->count() }} horario(s)
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">Sin horarios</span>
                                @endif
                            </td>
                            <td>
                                @if($medico->email)
                                    <div class="small"><i class="fas fa-envelope me-1 text-muted"></i>{{ $medico->email }}</div>
                                @endif
                                @if($medico->telefono)
                                    <div class="small"><i class="fas fa-phone me-1 text-muted"></i>{{ $medico->telefono }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($medico->estado === 'activo')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('medicos.edit', $medico->id_medico) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Editar médico">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-user-md fa-2x mb-2 d-block opacity-25"></i>
                                No se encontraron médicos con los filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginación --}}
        @if($medicos->hasPages())
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando {{ $medicos->firstItem() }}–{{ $medicos->lastItem() }}
                de {{ $medicos->total() }} médicos
            </small>
            {{ $medicos->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
