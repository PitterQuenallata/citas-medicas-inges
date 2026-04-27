@extends('layouts.app')
@section('title', 'Gestion de Usuarios')

@section('content')
<div x-data="{
    showModal: false,
    editando: false,
    formAction: '{{ route('usuarios.store') }}',
    nombre: '',
    apellido: '',
    email: '',
    telefono: '',
    password: '',
    rolesSeleccionados: [],
    loading: false,
    abrirCrear() {
        this.editando = false;
        this.formAction = '{{ route('usuarios.store') }}';
        this.nombre = '';
        this.apellido = '';
        this.email = '';
        this.telefono = '';
        this.password = '';
        this.rolesSeleccionados = [];
        this.showModal = true;
    },
    abrirEditar(u) {
        this.editando = true;
        this.formAction = '/usuarios/' + u.id;
        this.nombre = u.nombre;
        this.apellido = u.apellido;
        this.email = u.email;
        this.telefono = u.telefono || '';
        this.password = '';
        this.rolesSeleccionados = u.roles.map(r => r.id_rol);
        this.showModal = true;
    }
}">

<div class="flex items-center justify-between py-2 pb-4">
    <div class="flex items-center gap-3">
        <h2 class="text-base font-medium tracking-wide text-slate-700 dark:text-navy-100">Usuarios</h2>
        <div class="flex items-center" x-data="{isInputActive: {{ request('buscar') ? 'true' : 'false' }}}">
            <label class="block">
                <form method="GET">
                    <input x-effect="isInputActive === true && $nextTick(() => { $el.focus()});"
                        :class="isInputActive ? 'w-32 lg:w-48' : 'w-0'"
                        class="form-input bg-transparent px-1 text-right transition-all duration-100 placeholder:text-slate-500 dark:placeholder:text-navy-200"
                        placeholder="Buscar..." type="text" name="buscar" value="{{ request('buscar') }}" />
                </form>
            </label>
            <button @click="isInputActive = !isInputActive"
                class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </div>
    <button @click="abrirCrear()" class="btn bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">
        + Nuevo Usuario
    </button>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">#</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Nombre</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Email</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Telefono</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Roles</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $usuario->id }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 font-medium text-slate-700 dark:text-navy-100">
                        {{ $usuario->nombre }} {{ $usuario->apellido }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $usuario->email }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-slate-600 dark:text-navy-200">{{ $usuario->telefono ?? '—' }}</td>
                    <td class="px-3 py-3 sm:px-5">
                        <div class="flex flex-wrap gap-1">
                            @forelse($usuario->roles as $rol)
                                <span class="tag rounded-full bg-primary text-white text-xs">{{ $rol->nombre_rol }}</span>
                            @empty
                                <span class="text-xs text-slate-400">Sin rol</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        @if($usuario->estado === 'activo')
                            <span class="badge rounded-full bg-success/10 text-success dark:bg-success/15">Activo</span>
                        @elseif($usuario->estado === 'bloqueado')
                            <span class="badge rounded-full bg-warning/10 text-warning dark:bg-warning/15">Bloqueado</span>
                        @else
                            <span class="badge rounded-full bg-error/10 text-error dark:bg-error/15">Inactivo</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <div class="flex gap-2">
                            <button @click="abrirEditar({{ $usuario->load('roles')->toJson() }})"
                                class="btn size-8 rounded-full p-0 text-primary hover:bg-primary/10" title="Editar">
                                <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @if($usuario->estado === 'activo')
                            <form method="POST" action="{{ route('usuarios.destroy', $usuario->id) }}" class="swal-desactivar">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn size-8 rounded-full p-0 text-error hover:bg-error/10" title="Desactivar">
                                    <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('usuarios.activar', $usuario->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn size-8 rounded-full p-0 text-success hover:bg-success/10" title="Activar">
                                    <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-slate-400 dark:text-navy-300">
                        No se encontraron usuarios.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $usuarios->links() }}
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
        <div class="relative w-full max-w-lg rounded-lg bg-white px-6 py-6 transition-opacity duration-300 dark:bg-navy-700 max-h-[90vh] overflow-y-auto"
            x-show="showModal"
            x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-4" x-text="editando ? 'Editar Usuario' : 'Nuevo Usuario'"></h3>

            <form :action="formAction" method="POST" @submit="loading = true">
                @csrf
                <template x-if="editando"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Nombre</span>
                            <input type="text" name="nombre" x-model="nombre" required
                                class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700" />
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Apellido</span>
                            <input type="text" name="apellido" x-model="apellido" required
                                class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700" />
                        </label>
                    </div>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Email</span>
                        <input type="email" name="email" x-model="email" required
                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700" />
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Telefono</span>
                        <input type="text" name="telefono" x-model="telefono"
                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700" />
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">
                            <span x-text="editando ? 'Nueva contraseña (dejar vacio para no cambiar)' : 'Contraseña'"></span>
                        </span>
                        <input type="password" name="password" x-model="password" :required="!editando" minlength="6"
                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700" />
                    </label>

                    <div>
                        <span class="text-sm font-medium text-slate-600 dark:text-navy-100">Roles</span>
                        <div class="mt-2 flex flex-wrap gap-3">
                            @foreach($roles as $rol)
                            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-navy-200">
                                <input type="checkbox" name="roles[]" value="{{ $rol->id_rol }}"
                                    :checked="rolesSeleccionados.includes({{ $rol->id_rol }})"
                                    class="form-checkbox is-basic size-4 rounded border-slate-400/70 checked:bg-primary checked:border-primary" />
                                {{ $rol->nombre_rol }}
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
                    <button type="submit" class="btn bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus flex items-center" :disabled="loading">
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

    document.querySelectorAll('.swal-desactivar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Desactivar usuario?',
                text: 'El usuario no podra acceder al sistema',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, desactivar',
                cancelButtonText: 'Cancelar'
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    });
});
</script>
@endpush
