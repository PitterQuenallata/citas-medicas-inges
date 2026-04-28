@extends('layouts.app')
@section('title', 'Pagos')

@section('content')
<div class="flex items-center justify-between py-2 pb-4">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="estado" class="form-select h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            <option value="">Todos los estados</option>
            @foreach(['pendiente','pagado','anulado'] as $est)
                <option value="{{ $est }}" {{ request('estado') === $est ? 'selected' : '' }}>{{ ucfirst($est) }}</option>
            @endforeach
        </select>
        <select name="metodo" class="form-select h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
            <option value="">Todos los metodos</option>
            @foreach(['qr' => 'QR VeriPagos', 'efectivo' => 'Efectivo', 'transferencia' => 'Transferencia'] as $val => $label)
                <option value="{{ $val }}" {{ request('metodo') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
            class="form-input h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" placeholder="Desde" />
        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
            class="form-input h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" placeholder="Hasta" />
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar codigo o paciente..."
            class="form-input h-9 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
        <button type="submit" class="btn h-9 bg-primary px-4 text-sm font-medium text-white hover:bg-primary-focus">Filtrar</button>
    </form>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Codigo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Cita</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Monto</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Metodo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Fecha</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagos as $pago)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-xs font-mono text-slate-500 dark:text-navy-300">{{ $pago->codigo_pago }}</td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <a href="{{ route('citas.show', $pago->cita->id_cita) }}" class="text-sm text-primary hover:underline">{{ $pago->cita->codigo_cita }}</a>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-sm text-slate-600 dark:text-navy-200">
                        {{ $pago->cita->paciente?->nombres }} {{ $pago->cita->paciente?->apellidos }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-sm font-medium text-slate-700 dark:text-navy-100">
                        Bs. {{ number_format($pago->monto, 2) }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-sm text-slate-600 dark:text-navy-200">
                        {{ $pago->metodo_label }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <span class="badge rounded-full text-xs {{ $pago->badge_class }}">
                            {{ $pago->estado_label }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5 text-sm text-slate-600 dark:text-navy-200">
                        {{ $pago->fecha_pago?->format('d/m/Y H:i') ?? $pago->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 sm:px-5">
                        <div class="flex gap-1">
                            <a href="{{ route('pagos.show', $pago->id_pago) }}" class="btn size-8 rounded-full p-0 text-slate-500 hover:bg-slate-100" title="Ver detalle">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if($pago->estado_pago === 'pagado')
                            <button type="button" class="btn size-8 rounded-full p-0 text-error hover:bg-error/10 btn-anular-pago" data-id="{{ $pago->id_pago }}" title="Anular">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-slate-400">No se encontraron pagos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $pagos->links() }}
    </div>
</div>

{{-- Form oculto para anular --}}
<form method="POST" id="form-anular-pago" style="display:none">
    @csrf @method('PATCH')
    <input type="hidden" name="motivo_anulacion" id="motivo_anulacion_pago">
</form>
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

    document.querySelectorAll('.btn-anular-pago').forEach(btn => {
        btn.addEventListener('click', function() {
            const pagoId = this.dataset.id;
            Swal.fire({
                title: '¿Anular este pago?',
                input: 'textarea',
                inputLabel: 'Motivo de anulacion',
                inputPlaceholder: 'Escriba el motivo...',
                inputAttributes: { required: true },
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, anular pago',
                cancelButtonText: 'Volver',
                inputValidator: (value) => { if (!value) return 'Debe indicar un motivo'; }
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-anular-pago');
                    form.action = `/pagos/${pagoId}/anular`;
                    document.getElementById('motivo_anulacion_pago').value = result.value;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
