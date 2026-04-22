@extends('layouts.app')

@section('content')

<div class="card shadow">
    <div class="card-header bg-warning">
        Editar Paciente
    </div>

    <div class="card-body">

        <form method="POST" action="{{ route('pacientes.update', $paciente) }}">
            @csrf
            @method('PUT')

            @include('pacientes.form')

            <div class="mt-3 text-end">
                <button class="btn btn-primary">Actualizar</button>
            </div>
        </form>

    </div>
</div>

@endsection