@extends('layouts.app')

@section('content')

<h2 class="mb-3">Pacientes</h2>

<div class="d-flex justify-content-between mb-3">
    <form method="GET" class="d-flex">
        <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar...">
        <button class="btn btn-primary">Buscar</button>
    </form>

    <a href="{{ route('pacientes.create') }}" class="btn btn-success">
        + Nuevo Paciente
    </a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>CI</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th width="180">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pacientes as $p)
        <tr>
            <td>{{ $p->codigo_paciente }}</td>
            <td>{{ $p->nombres }} {{ $p->apellidos }}</td>
            <td>{{ $p->ci }}</td>
            <td>{{ $p->telefono }}</td>
            <td>
                <span class="badge bg-{{ $p->estado == 'activo' ? 'success' : 'secondary' }}">
                    {{ $p->estado }}
                </span>
            </td>
            <td>
                <a href="{{ route('pacientes.edit',$p) }}" class="btn btn-warning btn-sm">Editar</a>

                <form action="{{ route('pacientes.destroy',$p) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $pacientes->links() }}

@endsection