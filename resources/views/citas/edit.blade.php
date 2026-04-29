@extends('layouts.app')
@section('title', 'Editar Cita')

@section('content')
<div class="flex items-center justify-between pb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('citas.show', $cita->id_cita) }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
            <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-lg font-medium text-slate-700 dark:text-navy-100">Editar Cita {{ $cita->codigo_cita }}</h2>
    </div>
</div>

<div class="card px-4 py-5 sm:px-5">
    <form method="POST" action="{{ route('citas.update', $cita->id_cita) }}" id="form-cita">
        @csrf @method('PUT')
        @include('citas._form')

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('citas.show', $cita->id_cita) }}" class="btn border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500">
                Cancelar
            </a>
            <button type="submit" class="btn bg-primary font-medium text-white hover:bg-primary-focus dark:bg-accent dark:hover:bg-accent-focus">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    @if(session('success'))
        Swal.fire({ title: 'Listo', text: @json(session('success')), icon: 'success', confirmButtonColor: '#4f46e5' });
    @endif
    @if(session('error'))
        Swal.fire({ title: 'Error', text: @json(session('error')), icon: 'error', confirmButtonColor: '#4f46e5' });
    @endif

    document.getElementById('form-cita').addEventListener('submit', function(e) {
        const hi = document.getElementById('hora_inicio').value;
        if (!hi) {
            e.preventDefault();
            Swal.fire({ title: 'Seleccione un horario', text: 'Debe elegir un slot de horario disponible', icon: 'warning', confirmButtonColor: '#4f46e5' });
        }
    });
});
</script>
@endpush
