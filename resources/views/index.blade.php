@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @auth
                        <div class="mb-3">Hola, <strong>{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</strong></div>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <a class="text-decoration-none" href="{{ route('pacientes.index') }}">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="h5 mb-1">Pacientes</div>
                                            <div class="text-muted">Gestión y consulta de pacientes</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-md-4">
                                <a class="text-decoration-none" href="{{ route('medicos.index') }}">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="h5 mb-1">Médicos</div>
                                            <div class="text-muted">Médicos y especialidades</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12 col-md-4">
                                <a class="text-decoration-none" href="{{ route('citas.index') }}">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="h5 mb-1">Citas</div>
                                            <div class="text-muted">Agenda y turnos</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Inicia sesión para ver el menú.
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection
