@extends('layouts.app')

@section('title', 'Médicos')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Médicos</h1>
        <a class="btn btn-primary" href="{{ route('medicos.create') }}">Crear</a>
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
                @foreach ($medicos as $medico)
                    <tr>
                        <td>{{ $medico->id_medico }}</td>
                        <td>{{ $medico->codigo_medico }}</td>
                        <td>{{ $medico->apellidos }}</td>
                        <td>{{ $medico->nombres }}</td>
                        <td>{{ $medico->estado }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('medicos.edit', $medico) }}">Editar</a>

                            <form method="POST" action="{{ route('medicos.destroy', $medico) }}" class="d-inline">
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
            {{ $medicos->links() }}
        </div>
    </div>
@endsection
