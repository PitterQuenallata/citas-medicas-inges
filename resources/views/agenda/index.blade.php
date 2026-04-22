@extends('layouts.app')

@section('title', 'Agenda Médica')

@section('content')

{{-- Encabezado --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#0f172a;">Agenda Médica</h4>
        <p class="text-muted small mb-0">Vista de citas por médico y fecha</p>
    </div>
    <a href="{{ route('citas.create') }}" class="btn btn-brand">+ Nueva cita</a>
</div>

{{-- Filtros --}}
<div class="card-citas mb-4 p-3">
    <form method="GET" action="{{ route('agenda') }}" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label-citas">Médico</label>
            <select name="id_medico" class="form-select form-select-sm" required>
                <option value="">— Seleccionar médico —</option>
                @foreach($medicos as $med)
                    <option value="{{ $med->id_medico }}"
                        {{ $medicoId == $med->id_medico ? 'selected' : '' }}>
                        Dr(a). {{ $med->apellidos }}, {{ $med->nombres }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label-citas">Fecha</label>
            <input type="date" name="fecha" class="form-control form-control-sm"
                   value="{{ $fecha }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-brand btn-sm w-100">Ver agenda</button>
        </div>
        @if($medicoId)
        <div class="col-md-2">
            <a href="{{ route('agenda', ['id_medico' => $medicoId, 'fecha' => \Carbon\Carbon::parse($fecha)->subDay()->format('Y-m-d')]) }}"
               class="btn btn-ghost btn-sm w-100">← Día anterior</a>
        </div>
        @endif
    </form>
</div>

@if(!$medicoId)
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <p class="mb-0">Selecciona un médico para ver su agenda.</p>
    </div>
@else
    @php
        $medicoActual = $medicos->firstWhere('id_medico', $medicoId);
        $fechaCarbon  = \Carbon\Carbon::parse($fecha);
        $dias         = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $diaNombre    = $dias[$fechaCarbon->dayOfWeekIso];
    @endphp

    <div class="card-citas">
        {{-- Header del día --}}
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">
                    Dr(a). {{ $medicoActual?->apellidos }}, {{ $medicoActual?->nombres }}
                </h5>
                <small class="text-muted">
                    {{ $diaNombre }}, {{ $fechaCarbon->format('d/m/Y') }}
                </small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('agenda', ['id_medico' => $medicoId, 'fecha' => $fechaCarbon->copy()->subDay()->format('Y-m-d')]) }}"
                   class="btn btn-ghost btn-sm">← Anterior</a>
                <a href="{{ route('agenda', ['id_medico' => $medicoId, 'fecha' => today()->format('Y-m-d')]) }}"
                   class="btn btn-ghost btn-sm">Hoy</a>
                <a href="{{ route('agenda', ['id_medico' => $medicoId, 'fecha' => $fechaCarbon->copy()->addDay()->format('Y-m-d')]) }}"
                   class="btn btn-ghost btn-sm">Siguiente →</a>
            </div>
        </div>

        <div class="p-4">
            @if($horarios->isEmpty())
                <div class="empty-state py-4">
                    <p class="mb-0">El médico no tiene horario configurado para {{ $diaNombre }}.</p>
                </div>
            @else
                {{-- Resumen del día --}}
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="card-citas p-3 text-center">
                            <div style="font-size:1.5rem;font-weight:700;color:var(--brand);">
                                {{ $citas->count() }}
                            </div>
                            <div class="detail-label">Citas del día</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card-citas p-3 text-center">
                            <div style="font-size:1.5rem;font-weight:700;color:#15803d;">
                                {{ $citas->whereIn('estado_cita', ['pendiente','confirmada'])->count() }}
                            </div>
                            <div class="detail-label">Pendientes / Confirmadas</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card-citas p-3 text-center">
                            <div style="font-size:1.5rem;font-weight:700;color:#92400e;">
                                {{ $horarios->sum(function($h) { return \Carbon\Carbon::parse($h->hora_inicio)->diffInMinutes(\Carbon\Carbon::parse($h->hora_fin)) / $h->duracion_cita_minutos; }) }}
                            </div>
                            <div class="detail-label">Slots totales del día</div>
                        </div>
                    </div>
                </div>

                {{-- Horario del día --}}
                @foreach($horarios as $horario)
                    @php
                        $cursor  = \Carbon\Carbon::createFromFormat('H:i:s', $horario->hora_inicio);
                        $finHor  = \Carbon\Carbon::createFromFormat('H:i:s', $horario->hora_fin);
                        $durMin  = (int)$horario->duracion_cita_minutos;
                    @endphp

                    <div class="mb-3">
                        <p class="form-label-citas mb-2">
                            Bloque: {{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}
                            ({{ $durMin }} min/cita)
                        </p>

                        @while($cursor->copy()->addMinutes($durMin)->lessThanOrEqualTo($finHor))
                            @php
                                $slotIni  = $cursor->format('H:i');
                                $slotFin  = $cursor->copy()->addMinutes($durMin)->format('H:i');
                                $slotIniS = $slotIni . ':00';
                                $slotFinS = $slotFin . ':00';

                                $citaSlot = $citas->first(function($c) use ($slotIniS, $slotFinS) {
                                    return $c->hora_inicio === $slotIniS || (
                                        $slotIniS < $c->hora_fin && $slotFinS > $c->hora_inicio
                                    );
                                });
                            @endphp

                            @if($citaSlot)
                                <div class="agenda-slot d-flex align-items-center gap-3">
                                    <span class="agenda-hora">{{ $slotIni }} – {{ $slotFin }}</span>
                                    <span class="badge-estado {{ $citaSlot->badge_class }}">
                                        {{ $citaSlot->estado_label }}
                                    </span>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold" style="font-size:.875rem;">
                                            {{ $citaSlot->paciente->apellidos }}, {{ $citaSlot->paciente->nombres }}
                                        </span>
                                        @if($citaSlot->motivo_consulta)
                                            <span class="text-muted" style="font-size:.8125rem;">
                                                — {{ Str::limit($citaSlot->motivo_consulta, 60) }}
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('citas.show', $citaSlot) }}"
                                       class="btn btn-ghost btn-sm ms-auto">Ver</a>
                                </div>
                            @else
                                <div class="agenda-slot agenda-libre d-flex align-items-center gap-3">
                                    <span class="agenda-hora">{{ $slotIni }} – {{ $slotFin }}</span>
                                    <span style="font-size:.8125rem;">Disponible</span>
                                    <a href="{{ route('citas.create') }}"
                                       class="btn btn-sm ms-auto"
                                       style="border:1px solid var(--border);border-radius:var(--radius);font-size:.8125rem;color:var(--brand);padding:.2rem .6rem;">
                                        + Agendar
                                    </a>
                                </div>
                            @endif

                            @php $cursor->addMinutes($durMin); @endphp
                        @endwhile
                    </div>
                @endforeach

            @endif
        </div>
    </div>
@endif

@endsection
