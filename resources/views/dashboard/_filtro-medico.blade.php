{{-- Filtro de médico para admin/recepcionista --}}
@if($puedeSeleccionar && $medicos->count())
<div class="card mb-4 px-4 py-3 sm:px-5">
    <form method="GET" action="{{ url()->current() }}" class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex items-center gap-2">
            <svg class="size-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            <span class="text-sm font-medium text-slate-600 dark:text-navy-200">Seleccionar Medico:</span>
        </div>
        <select name="medico_id" onchange="this.form.submit()"
            class="form-select w-full sm:w-72 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400">
            @foreach($medicos as $m)
                <option value="{{ $m->id_medico }}" {{ $medicoSeleccionado == $m->id_medico ? 'selected' : '' }}>
                    Dr(a). {{ $m->apellidos }}, {{ $m->nombres }}
                </option>
            @endforeach
        </select>
    </form>
</div>
@endif
