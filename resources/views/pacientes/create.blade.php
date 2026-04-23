@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Nuevo Paciente</span>
                <a href="{{ route('pacientes.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>

            <div class="card-body">

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('pacientes.store') }}">
            @csrf

            @include('pacientes.form')

            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-success">Guardar</button>
            </div>
        </form>

            </div>
        </div>
    </div>
</div>


@endsection