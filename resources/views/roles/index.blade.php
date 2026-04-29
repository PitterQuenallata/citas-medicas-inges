@extends('layouts.app')
@section('title', 'Roles del Sistema')

@section('content')
<div x-data="{
    showModal: false,
    editando: false,
    formAction: '{{ route('roles.store') }}',
    formMethod: 'POST',
    nombre_rol: '',
    descripcion: '',
    permisosSeleccionados: [],
    loading: false,
    abrirCrear() {
        this.editando = false;
        this.formAction = '{{ route('roles.store') }}';
        this.formMethod = 'POST';
        this.nombre_rol = '';
        this.descripcion = '';
        this.permisosSeleccionados = [];
        this.showModal = true;
    },
    abrirEditar(rol) {
        this.editando = true;
        this.formAction = '/roles/' + rol.id_rol;
        this.formMethod = 'PUT';
        this.nombre_rol = rol.nombre_rol;
        this.descripcion = rol.descripcion || '';
        this.permisosSeleccionados = rol.permisos.map(p => p.id_permiso);
        this.showModal = true;
    }
}">

<div class="flex items-center justify-between py-2 pb-4">
    <button @click="abrirCrear()" class="btn bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
        + Nuevo Rol
    </button>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">#</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Nombre</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Descripcion</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Permisos</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $rol)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $rol->id_rol }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 font-medium text-slate-700 dark:text-navy-100">{{ $rol->nombre_rol }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $rol->descripcion ?? '—' }}</td>
                    <td class="px-3 py-3 sm:px-5">
                        <div class="flex flex-wrap gap-1">
                            @foreach($rol->permisos as $permiso)
                            <span class="tag rounded-full bg-info text-white text-xs">{{ $permiso->modulo }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        @if($rol->estado === 'activo')
                            <span class="badge rounded-full bg-success/10 text-success dark:bg-success/15">Activo</span>
                        @else
                            <span class="badge rounded-full bg-error/10 text-error dark:bg-error/15">Inactivo</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <div class="flex gap-2">
                            <button @click="abrirEditar({{ $rol->toJson() }})"
                                class="btn size-8 rounded-full p-0 text-primary hover:bg-primary/10" title="Editar">
                                <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('roles.destroy', $rol->id_rol) }}" class="swal-delete">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn size-8 rounded-full p-0 text-error hover:bg-error/10" title="Eliminar">
                                    <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal crear/editar --}}
<template x-teleport="#x-teleport-target">
    <div class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
        x-show="showModal" role="dialog" @keydown.window.escape="showModal = false">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300"
            @click="showModal = false" x-show="showModal"
            x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="relative w-full max-w-lg rounded-lg bg-white px-6 py-6 transition-opacity duration-300 dark:bg-navy-700"
            x-show="showModal"
            x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-4" x-text="editando ? 'Editar Rol' : 'Nuevo Rol'"></h3>

            <form :action="formAction" method="POST" @submit="loading = true">
                @csrf
                <template x-if="editando"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Nombre del rol</span>
                        <input type="text" name="nombre_rol" x-model="nombre_rol" required
                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700" />
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Descripcion</span>
                        <input type="text" name="descripcion" x-model="descripcion"
                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700" />
                    </label>

                    <div>
                        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Permisos</span>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach($permisos as $permiso)
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="permisos[]" value="{{ $permiso->id_permiso }}"
                                    :checked="permisosSeleccionados.includes({{ $permiso->id_permiso }})"
                                    @change="$event.target.checked ? permisosSeleccionados.push({{ $permiso->id_permiso }}) : permisosSeleccionados = permisosSeleccionados.filter(i => i !== {{ $permiso->id_permiso }})"
                                    class="form-checkbox is-basic size-5 rounded-sm border-slate-400/70 checked:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent" />
                                <p>{{ $permiso->modulo }}</p>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="showModal = false"
                        class="btn border border-slate-300 px-4 text-sm hover:bg-slate-100 dark:border-navy-450 dark:hover:bg-navy-600">
                        Cancelar
                    </button>
                    <button type="submit" class="btn bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus" :disabled="loading">
                        <template x-if="loading">
                            <div class="spinner size-4 animate-spin rounded-full border-[3px] border-white border-r-transparent mr-2"></div>
                        </template>
                        <span x-text="loading ? 'Guardando...' : (editando ? 'Actualizar' : 'Crear')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    @if(session('swal_success'))
        Swal.fire({ title: 'Listo', text: @json(session('swal_success')), icon: 'success', confirmButtonColor: '#4f46e5' });
    @endif
    @if(session('swal_error'))
        Swal.fire({ title: 'Error', text: @json(session('swal_error')), icon: 'error', confirmButtonColor: '#4f46e5' });
    @endif

    document.querySelectorAll('.swal-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Eliminar este rol?',
                text: 'Se eliminara permanentemente si no tiene usuarios asignados',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    });
});
</script>
@endpush
