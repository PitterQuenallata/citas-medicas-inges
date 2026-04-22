@extends('layouts.app')

@section('content')

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        Nuevo Paciente
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

            <div class="mt-3 text-end">
                <button class="btn btn-success">Guardar</button>
            </div>
        </form>

    </div>
</div>


@endsection