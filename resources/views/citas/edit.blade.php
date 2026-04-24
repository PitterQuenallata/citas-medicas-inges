@extends('layouts.app')

@section('title', 'Editar Cita')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('citas.show', $cita) }}" class="btn btn-ghost btn-sm">← Volver</a>
    <div>
        <h4 class="fw-bold mb-0" style="color:#0f172a;">Editar Cita</h4>
        <p class="text-muted small mb-0">Código: <strong>{{ $cita->codigo_cita }}</strong></p>
    </div>
</div>

<div class="card-citas p-4">
    <form method="POST" action="{{ route('citas.update', $cita) }}">
        @csrf
        @method('PUT')

        @include('citas._form', ['pacientes' => $pacientes, 'especialidades' => $especialidades, 'cita' => $cita])

        <hr class="my-4" style="border-color:var(--border);">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('citas.show', $cita) }}" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-brand">Guardar cambios</button>
        </div>
    </form>
</div>

@endsection
