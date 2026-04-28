@extends('layouts.app')
@section('title', 'Detalle Pago')

@section('content')
<div class="flex items-center justify-between pb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('pagos.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
            <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-lg font-medium text-slate-700 dark:text-navy-100">Pago {{ $pago->codigo_pago }}</h2>
        <span class="badge rounded-full text-xs {{ $pago->badge_class }}">
            {{ $pago->estado_label }}
        </span>
    </div>

    <div class="flex items-center gap-2">
        @if($pago->estado_pago === 'pendiente' && $pago->metodo_pago === 'qr' && $pago->referencia_externa)
            <button type="button" id="btn-verificar-qr-show" class="btn h-8 rounded-full bg-info px-4 text-xs font-medium text-white hover:bg-info/90" data-movimiento="{{ $pago->referencia_externa }}">Verificar QR</button>
        @endif
        @if($pago->estado_pago === 'pagado')
            <button type="button" class="btn h-8 rounded-full bg-error px-4 text-xs font-medium text-white hover:bg-error/90 btn-anular-pago" data-id="{{ $pago->id_pago }}">Anular Pago</button>
        @endif
        <a href="{{ route('citas.show', $pago->cita->id_cita) }}" class="btn h-8 rounded-full border border-primary px-4 text-xs font-medium text-primary hover:bg-primary/10">Ver Cita</a>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    {{-- Info del pago --}}
    <div class="card px-4 py-4 sm:px-5">
        <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Datos del pago</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Codigo</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $pago->codigo_pago }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Monto</span>
                <span class="text-sm font-bold text-slate-700 dark:text-navy-100">Bs. {{ number_format($pago->monto, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Metodo</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $pago->metodo_label }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Estado</span>
                <span class="badge rounded-full text-xs {{ $pago->badge_class }}">{{ $pago->estado_label }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Fecha de pago</span>
                <span class="text-sm text-slate-700 dark:text-navy-100">{{ $pago->fecha_pago?->format('d/m/Y H:i') ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Registrado por</span>
                <span class="text-sm text-slate-700 dark:text-navy-100">{{ $pago->usuarioRegistra?->nombre }} {{ $pago->usuarioRegistra?->apellido }}</span>
            </div>
            @if($pago->referencia_externa)
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Referencia VeriPagos</span>
                <span class="text-sm font-mono text-slate-700 dark:text-navy-100">{{ $pago->referencia_externa }}</span>
            </div>
            @endif
            @if($pago->observaciones)
            <div>
                <span class="text-sm text-slate-500 dark:text-navy-300">Observaciones</span>
                <p class="mt-1 text-sm text-slate-700 dark:text-navy-100">{{ $pago->observaciones }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Info de la cita --}}
    <div class="space-y-4">
        <div class="card px-4 py-4 sm:px-5">
            <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Cita asociada</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500 dark:text-navy-300">Codigo</span>
                    <a href="{{ route('citas.show', $pago->cita->id_cita) }}" class="text-sm text-primary hover:underline">{{ $pago->cita->codigo_cita }}</a>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500 dark:text-navy-300">Fecha</span>
                    <span class="text-sm text-slate-700 dark:text-navy-100">{{ $pago->cita->fecha_cita->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500 dark:text-navy-300">Horario</span>
                    <span class="text-sm text-slate-700 dark:text-navy-100">{{ substr($pago->cita->hora_inicio,0,5) }} – {{ substr($pago->cita->hora_fin,0,5) }}</span>
                </div>
            </div>
        </div>

        <div class="card px-4 py-4 sm:px-5">
            <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Paciente</h3>
            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $pago->cita->paciente?->nombres }} {{ $pago->cita->paciente?->apellidos }}</p>
            @if($pago->cita->paciente?->ci)
                <p class="text-xs text-slate-400 mt-1">CI: {{ $pago->cita->paciente->ci }}</p>
            @endif
        </div>

        <div class="card px-4 py-4 sm:px-5">
            <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Medico</h3>
            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">Dr(a). {{ $pago->cita->medico?->nombres }} {{ $pago->cita->medico?->apellidos }}</p>
            @if($pago->cita->medico?->especialidades->count())
                <p class="text-xs text-slate-400 mt-1">{{ $pago->cita->medico->especialidades->pluck('nombre_especialidad')->join(', ') }}</p>
            @endif
        </div>
    </div>
</div>

{{-- Datos del remitente QR --}}
@if($pago->datos_remitente)
<div class="card mt-4 border border-info/20 bg-info/5 px-4 py-4 sm:px-5">
    <h3 class="text-sm font-semibold text-info mb-2">Datos del remitente (QR)</h3>
    <div class="grid grid-cols-2 gap-2 text-sm">
        @if(isset($pago->datos_remitente['nombre']))
        <div>
            <span class="text-slate-500">Nombre:</span>
            <span class="font-medium text-slate-700 dark:text-navy-100">{{ $pago->datos_remitente['nombre'] }}</span>
        </div>
        @endif
        @if(isset($pago->datos_remitente['banco']))
        <div>
            <span class="text-slate-500">Banco:</span>
            <span class="font-medium text-slate-700 dark:text-navy-100">{{ $pago->datos_remitente['banco'] }}</span>
        </div>
        @endif
        @if(isset($pago->datos_remitente['documento']))
        <div>
            <span class="text-slate-500">Documento:</span>
            <span class="font-medium text-slate-700 dark:text-navy-100">{{ $pago->datos_remitente['documento'] }}</span>
        </div>
        @endif
    </div>
</div>
@endif

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

    // ── Verificar QR ──
    const btnVerificar = document.getElementById('btn-verificar-qr-show');
    if (btnVerificar) {
        btnVerificar.addEventListener('click', async function() {
            const movId = this.dataset.movimiento;
            const originalText = this.textContent;
            this.disabled = true;
            this.textContent = 'Verificando...';

            try {
                const res = await fetch(`/api/pagos/verificar-qr/${movId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();

                if (data.success && data.estado === 'pagado') {
                    Swal.fire({
                        title: 'Pago confirmado',
                        html: 'El QR fue pagado exitosamente.<br>La pagina se recargara.',
                        icon: 'success',
                        confirmButtonColor: '#4f46e5',
                    }).then(() => location.reload());
                } else if (data.success) {
                    Swal.fire({
                        title: 'QR aun no pagado',
                        text: `Estado actual: ${data.estado || 'pendiente'}`,
                        icon: 'info',
                        confirmButtonColor: '#4f46e5',
                    });
                } else {
                    Swal.fire('Error', data.error || 'No se pudo verificar', 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Error de conexion al verificar QR', 'error');
            } finally {
                this.disabled = false;
                this.textContent = originalText;
            }
        });
    }

    // ── Anular pago ──
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
