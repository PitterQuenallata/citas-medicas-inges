@extends('layouts.app')

@section('content')

<h2>Editar Paciente</h2>

<form method="POST" action="{{ route('pacientes.update',$paciente) }}">
@csrf
@method('PUT')

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Código</label>
        <input name="codigo_paciente" value="{{ $paciente->codigo_paciente }}" class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label>Nombres</label>
        <input name="nombres" value="{{ $paciente->nombres }}" class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label>Apellidos</label>
        <input name="apellidos" value="{{ $paciente->apellidos }}" class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label>CI</label>
        <input name="ci" value="{{ $paciente->ci }}" class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label>Teléfono</label>
        <input name="telefono" value="{{ $paciente->telefono }}" class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label>Estado</label>
        <select name="estado" class="form-control">
            <option value="activo" {{ $paciente->estado=='activo'?'selected':'' }}>Activo</option>
            <option value="inactivo" {{ $paciente->estado=='inactivo'?'selected':'' }}>Inactivo</option>
        </select>
    </div>
</div>

<button class="btn btn-primary">Actualizar</button>
<a href="{{ route('pacientes.index') }}" class="btn btn-secondary">Volver</a>

</form>

@endsection