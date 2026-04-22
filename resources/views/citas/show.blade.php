@extends('layouts.app')

@section('title', 'Detalle de Cita')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('citas.index') }}" class="btn btn-ghost btn-sm">← Volver</a>
    <div>
        <h4 class="fw-bold mb-0" style="color:#0f172a;">Detalle de Cita</h4>
        <p class="text-muted small mb-0">{{ $cita->codigo_cita }}</p>
    </div>
    <div class="ms-auto d-flex gap-2">
        @if(!in_array($cita->estado_cita, ['cancelada','atendida','reprogramada']))
            <a href="{{ route('citas.edit', $cita) }}" class="btn btn-ghost btn-sm">Editar</a>
            <a href="{{ route('citas.reprogramar', $cita) }}" class="btn btn-ghost btn-sm"
               style="border-color:#fde68a;color:#92400e;">Reprogramar</a>
            <button type="button" class="btn btn-sm"
                    style="background:#fee2e2;border-color:#fca5a5;color:#b91c1c;border-radius:var(--radius);"
                    data-bs-toggle="modal" data-bs-target="#modalCancelar">
                Cancelar cita
            </button>
        @endif
    </div>
</div>

<div class="row g-4">

    {{-- Info principal --}}
    <div class="col-lg-8">
        <div class="card-citas p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <span class="badge-estado {{ $cita->badge_class }} me-2">{{ $cita->estado_label }}</span>
                    @if($cita->citaOriginal)
                        <small class="text-muted">
                            Reprogramada desde
                            <a href="{{ route('citas.show', $cita->citaOriginal) }}"
                               class="text-decoration-none" style="color:#6366f1;">
                                {{ $cita->citaOriginal->codigo_cita }}
                            </a>
                        </small>
                    @endif
                </div>
                <span class="detail-label">Registrada {{ $cita->created_at->diffForHumans() }}</span>
            </div>

            <div class="row g-3">
                <div class="col-sm-6">
                    <p class="detail-label mb-1">Paciente</p>
                    <p class="detail-value mb-0">{{ $cita->paciente->apellidos }}, {{ $cita->paciente->nombres }}</p>
                    @if($cita->paciente->ci)
                        <small class="text-muted">{{ $cita->paciente->ci }}</small>
                    @endif
                </div>
                <div class="col-sm-6">
                    <p class="detail-label mb-1">Médico</p>
                    <p class="detail-value mb-0">
                        Dr(a). {{ $cita->medico->apellidos }}, {{ $cita->medico->nombres }}
                    </p>
                    @if($cita->medico->especialidades->isNotEmpty())
                        <small class="text-muted">
                            {{ $cita->medico->especialidades->pluck('nombre_especialidad')->join(', ') }}
                        </small>
                    @endif
                </div>
                <div class="col-sm-4">
                    <p class="detail-label mb-1">Fecha</p>
                    <p class="detail-value mb-0">
                        {{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}
                    </p>
                </div>
                <div class="col-sm-4">
                    <p class="detail-label mb-1">Hora inicio</p>
                    <p class="detail-value mb-0">{{ substr($cita->hora_inicio, 0, 5) }}</p>
                </div>
                <div class="col-sm-4">
                    <p class="detail-label mb-1">Hora fin</p>
                    <p class="detail-value mb-0">{{ substr($cita->hora_fin, 0, 5) }}</p>
                </div>
                @if($cita->motivo_consulta)
                <div class="col-12">
                    <p class="detail-label mb-1">Motivo de consulta</p>
                    <p class="detail-value mb-0">{{ $cita->motivo_consulta }}</p>
                </div>
                @endif
                @if($cita->observaciones)
                <div class="col-12">
                    <p class="detail-label mb-1">Observaciones</p>
                    <p class="detail-value mb-0">{{ $cita->observaciones }}</p>
                </div>
                @endif
            </div>

            @if($cita->estado_cita === 'cancelada')
                <hr style="border-color:var(--border);">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <p class="detail-label mb-1">Fecha de cancelación</p>
                        <p class="detail-value mb-0">{{ $cita->fecha_cancelacion?->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="detail-label mb-1">Motivo de cancelación</p>
                        <p class="detail-value mb-0">{{ $cita->motivo_cancelacion }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="card-citas p-3">
            <div class="card-header mb-3">
                <h6>Datos del paciente</h6>
            </div>
            <div class="row g-2" style="font-size:.875rem;">
                @if($cita->paciente->telefono)
                <div class="col-12">
                    <span class="detail-label">Teléfono</span><br>
                    <span>{{ $cita->paciente->telefono }}</span>
                </div>
                @endif
                @if($cita->paciente->fecha_nacimiento)
                <div class="col-12">
                    <span class="detail-label">Nacimiento</span><br>
                    <span>{{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($cita->paciente->grupo_sanguineo)
                <div class="col-12">
                    <span class="detail-label">Grupo sanguíneo</span><br>
                    <span class="badge-estado badge-confirmada">{{ $cita->paciente->grupo_sanguineo }}</span>
                </div>
                @endif
                @if($cita->paciente->alergias)
                <div class="col-12">
                    <span class="detail-label">Alergias</span><br>
                    <span class="text-danger" style="font-size:.8125rem;">{{ $cita->paciente->alergias }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($cita->usuarioRegistra)
        <div class="card-citas p-3 mt-3">
            <p class="detail-label mb-1">Registrada por</p>
            <p class="mb-0" style="font-size:.875rem;">
                {{ $cita->usuarioRegistra->name }}
            </p>
        </div>
        @endif
    </div>
</div>

{{-- Modal cancelar --}}
<div class="modal fade" id="modalCancelar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius);border:1px solid var(--border);">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Cancelar cita</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('citas.cancelar', $cita) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Cita: <strong>{{ $cita->codigo_cita }}</strong> —
                        {{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}
                        {{ substr($cita->hora_inicio, 0, 5) }}
                    </p>
                    <label class="form-label-citas" for="motivo_cancelacion">
                        Motivo de cancelación <span class="text-danger">*</span>
                    </label>
                    <textarea name="motivo_cancelacion" id="motivo_cancelacion"
                              class="form-control @error('motivo_cancelacion') is-invalid @enderror"
                              rows="3" placeholder="Indique el motivo…" required></textarea>
                    @error('motivo_cancelacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm"
                            style="background:#b91c1c;color:#fff;border-radius:var(--radius);">
                        Confirmar cancelación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
