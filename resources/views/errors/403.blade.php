@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="text-center">
        <div class="mb-6">
            <svg class="mx-auto size-20 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        </div>
        <h1 class="text-4xl font-bold text-slate-700 dark:text-navy-100">403</h1>
        <p class="mt-2 text-lg text-slate-500 dark:text-navy-300">Acceso Denegado</p>
        <p class="mt-1 text-sm text-slate-400 dark:text-navy-400">
            {{ $exception->getMessage() ?: 'No tienes permiso para acceder a esta seccion.' }}
        </p>
        <a href="{{ route('dashboard') }}"
            class="btn mt-6 bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
            Volver al Dashboard
        </a>
    </div>
</div>
@endsection
