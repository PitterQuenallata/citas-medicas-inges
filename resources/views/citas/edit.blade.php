@extends('layouts.app')
@section('title', 'Editar Cita')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('citas.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Citas</span>
</div>

<div class="card p-4 sm:p-5">
    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-6">Editar Cita</h3>

    <form method="POST" action="{{ route('citas.update', $cita->id_cita) }}">
        @csrf
        @method('PUT')

        @include('citas._form', ['cita' => $cita, 'pacientes' => $pacientes, 'especialidades' => $especialidades])

        <div class="mt-6 flex gap-3">
            <button type="submit" class="btn bg-primary px-5 text-sm font-medium text-white hover:bg-primary-focus">
                Actualizar
            </button>
            <a href="{{ route('citas.show', $cita->id_cita) }}" class="btn border border-slate-300 px-5 text-sm font-medium hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
