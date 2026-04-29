@extends('layouts.app')
@section('title', 'Permisos del Sistema')

@section('content')
<div class="flex items-center justify-between py-2 pb-4">
    <span class="badge rounded-full bg-slate-150 text-slate-800 px-3 py-1 text-xs dark:bg-navy-500 dark:text-navy-100">
        {{ $permisos->count() }} permisos registrados
    </span>
</div>

<div class="mb-3 rounded-lg border border-info/30 bg-info/5 px-4 py-2.5 text-sm text-info dark:border-info/20">
    <div class="flex items-center gap-2">
        <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Los permisos se gestionan a nivel de sistema. Se asignan a los roles desde el modulo de Roles.
    </div>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">#</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Permiso</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Modulo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Descripcion</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Roles que lo usan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permisos as $permiso)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $permiso->id_permiso }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 font-medium text-slate-700 dark:text-navy-100">
                        <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded dark:bg-navy-600">{{ $permiso->nombre_permiso }}</code>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <span class="tag rounded-full bg-primary text-white text-xs">{{ $permiso->modulo }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $permiso->descripcion }}</td>
                    <td class="px-3 py-3 sm:px-5">
                        <div class="flex flex-wrap gap-1">
                            @forelse($permiso->roles as $rol)
                                <span class="tag rounded-full bg-success text-white text-xs">{{ $rol->nombre_rol }}</span>
                            @empty
                                <span class="text-xs text-slate-400">Sin asignar</span>
                            @endforelse
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
