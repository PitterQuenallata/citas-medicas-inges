@extends('layouts.app')
@section('title', 'Nuevo Paciente')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('pacientes.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Pacientes</span>
</div>

<div class="card max-w-2xl p-4 sm:p-5">
    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-6">Registrar Nuevo Paciente</h3>
    <form id="paciente-create-form" method="POST" action="{{ route('pacientes.store') }}" class="space-y-4">
        @csrf
        @include('pacientes.form', ['paciente' => null])
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn bg-primary px-5 text-sm font-medium text-white hover:bg-primary-focus">
                Guardar Paciente
            </button>
            <a href="{{ route('pacientes.index') }}" class="btn border border-slate-300 px-5 text-sm font-medium hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
