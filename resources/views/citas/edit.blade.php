@extends('layouts.app')

@section('title', 'Editar cita')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Editar cita</h1>
        <a class="btn btn-outline-secondary" href="{{ route('citas.index') }}">Volver</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('citas.update', $cita) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Código</label>
                        <input class="form-control" name="codigo_cita" value="{{ old('codigo_cita', $cita->codigo_cita) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Paciente</label>
                        <select class="form-select" name="id_paciente" required>
                            <option value=""></option>
                            @foreach ($pacientes as $p)
                                <option value="{{ $p->id_paciente }}" @selected(old('id_paciente', $cita->id_paciente) == $p->id_paciente)>
                                    {{ $p->apellidos }} {{ $p->nombres }} ({{ $p->codigo_paciente }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Médico</label>
                        <select class="form-select" name="id_medico" required>
                            <option value=""></option>
                            @foreach ($medicos as $m)
                                <option value="{{ $m->id_medico }}" @selected(old('id_medico', $cita->id_medico) == $m->id_medico)>
                                    {{ $m->apellidos }} {{ $m->nombres }} ({{ $m->codigo_medico }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Fecha</label>
                        <input class="form-control" type="date" name="fecha_cita" value="{{ old('fecha_cita', optional($cita->fecha_cita)->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hora inicio</label>
                        <input class="form-control" type="time" name="hora_inicio" value="{{ old('hora_inicio', $cita->hora_inicio) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hora fin</label>
                        <input class="form-control" type="time" name="hora_fin" value="{{ old('hora_fin', $cita->hora_fin) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado_cita" required>
                            @foreach (['pendiente','confirmada','atendida','cancelada','reprogramada','no_asistio'] as $estado)
                                <option value="{{ $estado }}" @selected(old('estado_cita', $cita->estado_cita) === $estado)>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Motivo consulta</label>
                        <textarea class="form-control" name="motivo_consulta" rows="2">{{ old('motivo_consulta', $cita->motivo_consulta) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="2">{{ old('observaciones', $cita->observaciones) }}</textarea>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Actualizar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
