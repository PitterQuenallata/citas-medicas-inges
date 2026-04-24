@extends('layouts.app')
@section('title', 'Detalle Cita')

@section('content')
<div class="flex items-center gap-2 pb-4">
    <a href="{{ route('citas.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
        <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <span class="text-sm text-slate-500 dark:text-navy-300">Volver a Citas</span>
</div>

<div class="card flex flex-col items-center justify-center py-16 text-center">
    <svg class="size-16 text-primary/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
    </svg>
    <h2 class="text-xl font-semibold text-slate-700 dark:text-navy-100">Detalle Cita</h2>
    <p class="mt-2 text-sm text-slate-400 dark:text-navy-300">Módulo en desarrollo</p>
    <span class="mt-4 badge rounded-full bg-warning/10 px-4 py-1.5 text-sm text-warning">En Desarrollo</span>
</div>
@endsection
