@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <!-- Citas -->
    <div class="card p-4 sm:p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs-plus font-medium text-slate-400 dark:text-navy-300">Módulo</p>
                <p class="mt-1 text-2xl font-semibold text-slate-700 dark:text-navy-100">Citas</p>
            </div>
            <div class="flex size-12 items-center justify-center rounded-full bg-primary/10 dark:bg-accent/10">
                <svg class="size-6 text-primary dark:text-accent" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" fill="currentColor" fill-opacity=".3"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('citas.index') }}"
                class="btn h-8 rounded-full bg-primary px-4 text-xs font-medium text-white hover:bg-primary-focus">
                Ver Citas
            </a>
            <a href="{{ route('citas.create') }}"
                class="btn ml-2 h-8 rounded-full border border-slate-300 px-4 text-xs font-medium text-slate-700 hover:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500">
                Nueva Cita
            </a>
        </div>
    </div>

    <!-- Médicos -->
    <div class="card p-4 sm:p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs-plus font-medium text-slate-400 dark:text-navy-300">Módulo</p>
                <p class="mt-1 text-2xl font-semibold text-slate-700 dark:text-navy-100">Médicos</p>
            </div>
            <div class="flex size-12 items-center justify-center rounded-full bg-success/10">
                <svg class="size-6 text-success" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="4" fill="currentColor" fill-opacity=".3"/>
                    <path d="M4 20c0-3.314 3.582-6 8-6s8 2.686 8 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('medicos.index') }}"
                class="btn h-8 rounded-full bg-success px-4 text-xs font-medium text-white hover:bg-success-focus">
                Ver Médicos
            </a>
        </div>
    </div>

    <!-- Pacientes -->
    <div class="card p-4 sm:p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs-plus font-medium text-slate-400 dark:text-navy-300">Módulo</p>
                <p class="mt-1 text-2xl font-semibold text-slate-700 dark:text-navy-100">Pacientes</p>
            </div>
            <div class="flex size-12 items-center justify-center rounded-full bg-info/10">
                <svg class="size-6 text-info" viewBox="0 0 24 24" fill="none">
                    <path d="M17 21H7a4 4 0 0 1-4-4v-1a5 5 0 0 1 5-5h8a5 5 0 0 1 5 5v1a4 4 0 0 1-4 4Z" fill="currentColor" fill-opacity=".3"/>
                    <circle cx="12" cy="7" r="4" fill="currentColor"/>
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('pacientes.index') }}"
                class="btn h-8 rounded-full bg-info px-4 text-xs font-medium text-white hover:bg-info-focus">
                Ver Pacientes
            </a>
        </div>
    </div>
</div>

<!-- Bienvenida -->
<div class="card mt-4 p-4 sm:p-5">
    <div class="flex items-center space-x-4">
        <div class="flex size-14 shrink-0 items-center justify-center rounded-full bg-primary/10 dark:bg-accent/10">
            <img src="{{ asset('images/illustrations/doctor.svg') }}" alt="doctor" class="size-10" />
        </div>
        <div>
            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                Bienvenido, {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
            </h3>
            <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                Sistema de Gestión de Citas Médicas. Usa el menú lateral para navegar.
            </p>
        </div>
    </div>
</div>
@endsection
