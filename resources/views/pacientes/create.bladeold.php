@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Nuevo Paciente</h4>
        </div>

        <div class="card-body">

            <!-- 🔴 ERRORES -->
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('pacientes.store') }}">
            @csrf

            <div class="row g-3">

                <!-- Nombres -->
                <div class="col-md-6">
                    <label class="form-label">Nombres</label>
                    <input name="nombres" value="{{ old('nombres') }}"
                        class="form-control @error('nombres') is-invalid @enderror">
                </div>

                <!-- Apellidos -->
                <div class="col-md-6">
                    <label class="form-label">Apellidos</label>
                    <input name="apellidos" value="{{ old('apellidos') }}"
                        class="form-control @error('apellidos') is-invalid @enderror">
                </div>

                <!-- CI -->
                <div class="col-md-6">
                    <label class="form-label">CI</label>
                    <input type="text" id="ci" name="ci"
                        value="{{ old('ci') }}"
                        class="form-control @error('ci') is-invalid @enderror">

                    @error('ci')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <small id="mensajeCI"></small>
                </div>

                <!-- Teléfono -->
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input name="telefono" value="{{ old('telefono') }}" class="form-control">
                </div>

                <!-- Estado -->
                <div class="col-md-6">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="activo" {{ old('estado')=='activo'?'selected':'' }}>Activo</option>
                        <option value="inactivo" {{ old('estado')=='inactivo'?'selected':'' }}>Inactivo</option>
                    </select>
                </div>

            </div>

            <!-- BOTONES -->
            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('pacientes.index') }}" class="btn btn-secondary">
                    Volver
                </a>
                <button class="btn btn-success" id="btnGuardar">
                    Guardar
                </button>
            </div>

            </form>

        </div>
    </div>

</div>

@endsection