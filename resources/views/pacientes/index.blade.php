@extends('layouts.app')

@section('title', 'Pacientes')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Pacientes</h1>
        <a class="btn btn-primary" href="{{ route('pacientes.create') }}">Crear</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($pacientes as $paciente)
                    <tr>
                        <td>{{ $paciente->id_paciente }}</td>
                        <td>{{ $paciente->codigo_paciente }}</td>
                        <td>{{ $paciente->apellidos }}</td>
                        <td>{{ $paciente->nombres }}</td>
                        <td>{{ $paciente->estado }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('pacientes.edit', $paciente) }}">Editar</a>

                            <form method="POST" action="{{ route('pacientes.destroy', $paciente) }}" class="d-inline">
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
            {{ $pacientes->links() }}
        </div>
    </div>
@endsection
