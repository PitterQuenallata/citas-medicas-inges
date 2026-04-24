@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                <span>Editar Paciente</span>
                <a href="{{ route('pacientes.index') }}" class="btn btn-outline-dark btn-sm">Volver</a>
            </div>

            <div class="card-body">

                <form method="POST" action="{{ route('pacientes.update', $paciente) }}" class="js-paciente-update">
                    @csrf
                    @method('PUT')

                    @include('pacientes.form')

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button class="btn btn-primary">Actualizar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection