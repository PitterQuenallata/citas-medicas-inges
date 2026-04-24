@extends('layouts.app')

@section('title', 'Citas')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Citas</h1>
        <a class="btn btn-primary" href="{{ route('citas.create') }}">Crear</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($citas as $cita)
                    <tr>
                        <td>{{ $cita->id_cita }}</td>
                        <td>{{ $cita->codigo_cita }}</td>
                        <td>{{ optional($cita->paciente)->apellidos }} {{ optional($cita->paciente)->nombres }}</td>
                        <td>{{ optional($cita->medico)->apellidos }} {{ optional($cita->medico)->nombres }}</td>
                        <td>{{ optional($cita->fecha_cita)->format('Y-m-d') }}</td>
                        <td>{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</td>
                        <td>{{ $cita->estado_cita }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('citas.edit', $cita) }}">Editar</a>

                            <form method="POST" action="{{ route('citas.destroy', $cita) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-body">
            {{ $citas->links() }}
        </div>
    </div>
@endsection
