@extends('layouts.app')

@section('title', 'Crear paciente')

@php($suppressSwalErrors = true)

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Crear paciente</h1>
        <a class="btn btn-outline-secondary" href="{{ route('pacientes.index') }}">Volver</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('pacientes.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nombres</label>
                        <input class="form-control @error('nombres') is-invalid @enderror" name="nombres" value="{{ old('nombres') }}" required>
                        @error('nombres')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Apellidos</label>
                        <input class="form-control @error('apellidos') is-invalid @enderror" name="apellidos" value="{{ old('apellidos') }}" required>
                        @error('apellidos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Fecha nacimiento</label>
                        <input class="form-control @error('fecha_nacimiento') is-invalid @enderror" type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required>
                        @error('fecha_nacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Sexo</label>
                        <select class="form-select @error('sexo') is-invalid @enderror" name="sexo" required>
                            <option value="" @selected(old('sexo')==='')></option>
                            <option value="masculino" @selected(old('sexo')==='masculino')>Masculino</option>
                            <option value="femenino" @selected(old('sexo')==='femenino')>Femenino</option>
                            <option value="otro" @selected(old('sexo')==='otro')>Otro</option>
                        </select>
                        @error('sexo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">CI</label>
                        <input class="form-control @error('ci') is-invalid @enderror" name="ci" value="{{ old('ci') }}" required inputmode="numeric">
                        @error('ci')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input class="form-control @error('telefono') is-invalid @enderror" name="telefono" value="{{ old('telefono') }}" required inputmode="numeric">
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Dirección</label>
                        <input class="form-control @error('direccion') is-invalid @enderror" name="direccion" value="{{ old('direccion') }}" required>
                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contacto emergencia (nombre)</label>
                        <input class="form-control @error('contacto_emergencia_nombre') is-invalid @enderror" name="contacto_emergencia_nombre" value="{{ old('contacto_emergencia_nombre') }}">
                        @error('contacto_emergencia_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contacto emergencia (teléfono)</label>
                        <input class="form-control @error('contacto_emergencia_telefono') is-invalid @enderror" name="contacto_emergencia_telefono" value="{{ old('contacto_emergencia_telefono') }}" inputmode="numeric">
                        @error('contacto_emergencia_telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Grupo sanguíneo</label>
                        <input class="form-control" name="grupo_sanguineo" value="{{ old('grupo_sanguineo') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" required>
                            <option value="activo" @selected(old('estado','activo')==='activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado')==='inactivo')>Inactivo</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Alergias</label>
                        <textarea class="form-control" name="alergias" rows="2">{{ old('alergias') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones_generales" rows="2">{{ old('observaciones_generales') }}</textarea>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
