@extends('layouts.app')

@section('title', 'Crear médico')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Crear médico</h1>
        <a class="btn btn-outline-secondary" href="{{ route('medicos.index') }}">Volver</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('medicos.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">ID Usuario</label>
                        <input class="form-control" name="id_usuario" value="{{ old('id_usuario') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Código</label>
                        <input class="form-control" name="codigo_medico" value="{{ old('codigo_medico') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Matrícula</label>
                        <input class="form-control" name="matricula_profesional" value="{{ old('matricula_profesional') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nombres</label>
                        <input class="form-control" name="nombres" value="{{ old('nombres') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Apellidos</label>
                        <input class="form-control" name="apellidos" value="{{ old('apellidos') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">CI</label>
                        <input class="form-control" name="ci" value="{{ old('ci') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Teléfono</label>
                        <input class="form-control" name="telefono" value="{{ old('telefono') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" required>
                            <option value="activo" @selected(old('estado','activo')==='activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado')==='inactivo')>Inactivo</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
