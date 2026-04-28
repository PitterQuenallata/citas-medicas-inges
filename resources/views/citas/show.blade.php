@extends('layouts.app')
@section('title', 'Detalle Cita')

@section('content')
<div class="flex items-center justify-between pb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('citas.index') }}" class="btn size-8 rounded-full p-0 hover:bg-slate-100 dark:hover:bg-navy-500">
            <svg class="size-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-lg font-medium text-slate-700 dark:text-navy-100">Cita {{ $cita->codigo_cita }}</h2>
        <span class="badge rounded-full text-xs
            @if($cita->estado_cita === 'pendiente') bg-warning/10 text-warning
            @elseif($cita->estado_cita === 'confirmada') bg-info/10 text-info
            @elseif($cita->estado_cita === 'atendida') bg-success/10 text-success
            @elseif($cita->estado_cita === 'cancelada') bg-error/10 text-error
            @elseif($cita->estado_cita === 'reprogramada') bg-slate-100 text-slate-500 dark:bg-navy-500 dark:text-navy-200
            @elseif($cita->estado_cita === 'no_asistio') bg-warning/10 text-warning
            @else bg-slate-100 text-slate-600 @endif">
            {{ $cita->estado_label }}
        </span>
        @if($cita->pago)
            <span class="badge rounded-full text-xs {{ $cita->pago->badge_class }}">
                Pago: {{ $cita->pago->estado_label }}
            </span>
        @else
            @if(!in_array($cita->estado_cita, ['cancelada', 'atendida']))
            <span class="badge rounded-full text-xs bg-slate-100 text-slate-500 dark:bg-navy-500 dark:text-navy-200">
                Sin pago
            </span>
            @endif
        @endif
    </div>

    {{-- Acciones según estado --}}
    <div class="flex items-center gap-2">
        @if(!in_array($cita->estado_cita, ['cancelada', 'atendida', 'reprogramada']))
            @if(!$cita->pago || $cita->pago->estado_pago !== 'pagado')
                <button type="button" id="btn-registrar-pago" class="btn h-8 rounded-full bg-secondary px-4 text-xs font-medium text-white hover:bg-secondary/90" style="background-color: #8b5cf6;">
                    Registrar Pago
                </button>
            @endif
        @endif

        @if($cita->estado_cita === 'pendiente')
            <form method="POST" action="{{ route('citas.confirmar', $cita->id_cita) }}" class="swal-confirmar">
                @csrf @method('PATCH')
                <button type="submit" class="btn h-8 rounded-full bg-info px-4 text-xs font-medium text-white hover:bg-info/90">Confirmar</button>
            </form>
            <a href="{{ route('citas.edit', $cita->id_cita) }}" class="btn h-8 rounded-full border border-primary px-4 text-xs font-medium text-primary hover:bg-primary/10">Editar</a>
            <a href="{{ route('citas.reprogramar', $cita->id_cita) }}" class="btn h-8 rounded-full border border-slate-300 px-4 text-xs font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200">Reprogramar</a>
            <button type="button" class="btn h-8 rounded-full bg-error px-4 text-xs font-medium text-white hover:bg-error/90 btn-cancelar" data-id="{{ $cita->id_cita }}">Cancelar</button>
        @elseif($cita->estado_cita === 'confirmada')
            <form method="POST" action="{{ route('citas.atender', $cita->id_cita) }}" class="swal-atender">
                @csrf @method('PATCH')
                <button type="submit" class="btn h-8 rounded-full bg-success px-4 text-xs font-medium text-white hover:bg-success/90">Marcar Atendida</button>
            </form>
            <a href="{{ route('citas.reprogramar', $cita->id_cita) }}" class="btn h-8 rounded-full border border-slate-300 px-4 text-xs font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200">Reprogramar</a>
            <button type="button" class="btn h-8 rounded-full bg-error px-4 text-xs font-medium text-white hover:bg-error/90 btn-cancelar" data-id="{{ $cita->id_cita }}">Cancelar</button>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    {{-- Info principal --}}
    <div class="card px-4 py-4 sm:px-5">
        <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Datos de la cita</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Codigo</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $cita->codigo_cita }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Fecha</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $cita->fecha_cita->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Horario</span>
                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ substr($cita->hora_inicio,0,5) }} – {{ substr($cita->hora_fin,0,5) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-navy-300">Registrado por</span>
                <span class="text-sm text-slate-700 dark:text-navy-100">{{ $cita->usuarioRegistra?->nombre }} {{ $cita->usuarioRegistra?->apellido }}</span>
            </div>
            @if($cita->motivo_consulta)
            <div>
                <span class="text-sm text-slate-500 dark:text-navy-300">Motivo</span>
                <p class="mt-1 text-sm text-slate-700 dark:text-navy-100">{{ $cita->motivo_consulta }}</p>
            </div>
            @endif
            @if($cita->observaciones)
            <div>
                <span class="text-sm text-slate-500 dark:text-navy-300">Observaciones</span>
                <p class="mt-1 text-sm text-slate-700 dark:text-navy-100">{{ $cita->observaciones }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Paciente y Médico --}}
    <div class="space-y-4">
        <div class="card px-4 py-4 sm:px-5">
            <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Paciente</h3>
            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}</p>
            @if($cita->paciente?->ci)
                <p class="text-xs text-slate-400 mt-1">CI: {{ $cita->paciente->ci }}</p>
            @endif
            @if($cita->paciente?->telefono)
                <p class="text-xs text-slate-400">Tel: {{ $cita->paciente->telefono }}</p>
            @endif
        </div>

        <div class="card px-4 py-4 sm:px-5">
            <h3 class="text-sm font-semibold uppercase text-slate-400 dark:text-navy-300 mb-3">Medico</h3>
            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">Dr(a). {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}</p>
            @if($cita->medico?->especialidades->count())
                <p class="text-xs text-slate-400 mt-1">{{ $cita->medico->especialidades->pluck('nombre_especialidad')->join(', ') }}</p>
            @endif
        </div>
    </div>
</div>

{{-- Info cancelación --}}
@if($cita->estado_cita === 'cancelada')
<div class="card mt-4 border border-error/20 bg-error/5 px-4 py-4 sm:px-5">
    <h3 class="text-sm font-semibold text-error mb-2">Cita cancelada</h3>
    @if($cita->fecha_cancelacion)
        <p class="text-xs text-slate-500">Fecha: {{ $cita->fecha_cancelacion->format('d/m/Y H:i') }}</p>
    @endif
    @if($cita->motivo_cancelacion)
        <p class="text-sm text-slate-700 dark:text-navy-100 mt-1">Motivo: {{ $cita->motivo_cancelacion }}</p>
    @endif
</div>
@endif

{{-- Info reprogramación --}}
@if($cita->estado_cita === 'reprogramada' && $cita->reprogramaciones->count())
<div class="card mt-4 border border-info/20 bg-info/5 px-4 py-4 sm:px-5">
    <h3 class="text-sm font-semibold text-info mb-2">Cita reprogramada</h3>
    @foreach($cita->reprogramaciones as $nueva)
        <a href="{{ route('citas.show', $nueva->id_cita) }}" class="text-sm text-info hover:underline">
            Ver nueva cita: {{ $nueva->codigo_cita }} ({{ $nueva->fecha_cita->format('d/m/Y') }} {{ substr($nueva->hora_inicio,0,5) }})
        </a>
    @endforeach
</div>
@endif

{{-- Info del pago --}}
@if($cita->pago && $cita->pago->estado_pago === 'pagado')
<div class="card mt-4 border border-success/20 bg-success/5 px-4 py-4 sm:px-5">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-semibold text-success">Pago confirmado</h3>
        <a href="{{ route('pagos.show', $cita->pago->id_pago) }}" class="text-xs text-primary hover:underline">Ver detalle</a>
    </div>
    <div class="grid grid-cols-2 gap-2 text-sm">
        <div>
            <span class="text-slate-500 dark:text-navy-300">Codigo:</span>
            <span class="font-medium text-slate-700 dark:text-navy-100">{{ $cita->pago->codigo_pago }}</span>
        </div>
        <div>
            <span class="text-slate-500 dark:text-navy-300">Monto:</span>
            <span class="font-medium text-slate-700 dark:text-navy-100">Bs. {{ number_format($cita->pago->monto, 2) }}</span>
        </div>
        <div>
            <span class="text-slate-500 dark:text-navy-300">Metodo:</span>
            <span class="font-medium text-slate-700 dark:text-navy-100">{{ $cita->pago->metodo_label }}</span>
        </div>
        <div>
            <span class="text-slate-500 dark:text-navy-300">Fecha:</span>
            <span class="font-medium text-slate-700 dark:text-navy-100">{{ $cita->pago->fecha_pago?->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    @if($cita->pago->datos_remitente)
    <div class="mt-2 text-xs text-slate-500">
        <span class="font-medium">Remitente QR:</span>
        {{ $cita->pago->datos_remitente['nombre'] ?? '' }}
        {{ isset($cita->pago->datos_remitente['banco']) ? '- ' . $cita->pago->datos_remitente['banco'] : '' }}
    </div>
    @endif
</div>
@elseif($cita->pago && $cita->pago->estado_pago === 'pendiente')
<div class="card mt-4 border border-warning/20 bg-warning/5 px-4 py-4 sm:px-5">
    <h3 class="text-sm font-semibold text-warning mb-1">Pago pendiente (QR generado)</h3>
    <p class="text-xs text-slate-500">Codigo: {{ $cita->pago->codigo_pago }} — Monto: Bs. {{ number_format($cita->pago->monto, 2) }}</p>
</div>
@endif

@if($cita->citaOriginal)
<div class="card mt-4 border border-slate-200 bg-slate-50 px-4 py-4 sm:px-5 dark:border-navy-500 dark:bg-navy-600">
    <h3 class="text-sm font-semibold text-slate-500 dark:text-navy-200 mb-2">Reprogramada desde</h3>
    <a href="{{ route('citas.show', $cita->citaOriginal->id_cita) }}" class="text-sm text-primary hover:underline dark:text-accent">
        {{ $cita->citaOriginal->codigo_cita }} ({{ $cita->citaOriginal->fecha_cita->format('d/m/Y') }})
    </a>
</div>
@endif

{{-- Modal Registrar Pago --}}
@if(!in_array($cita->estado_cita, ['cancelada', 'atendida', 'reprogramada']) && (!$cita->pago || $cita->pago->estado_pago !== 'pagado'))
<div id="modal-pago" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" id="modal-pago-overlay"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl dark:bg-navy-700" id="modal-pago-content">
            <button type="button" id="btn-cerrar-modal" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <h3 class="text-lg font-semibold text-slate-700 dark:text-navy-100 mb-4">Registrar Pago — {{ $cita->codigo_cita }}</h3>

            {{-- Selección de método --}}
            <div id="pago-metodo-selector" class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-slate-600 dark:text-navy-200">Monto (Bs.)</label>
                    <input type="number" id="pago-monto" step="0.01" min="0.01"
                        value="{{ $cita->medico?->especialidades->first()?->costo_consulta ?? 0 }}"
                        class="form-input mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
                    <p class="mt-1 text-xs text-slate-400">Pre-llenado desde costo de especialidad (editable)</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600 dark:text-navy-200">Metodo de pago</label>
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        <button type="button" id="btn-metodo-qr" class="flex flex-col items-center gap-2 rounded-lg border-2 border-slate-200 p-4 transition hover:border-primary hover:bg-primary/5 dark:border-navy-450">
                            <svg class="size-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM17 14v3m0 3h.01M14 17h3m3 0h.01"/></svg>
                            <span class="text-sm font-medium text-slate-700 dark:text-navy-100">QR VeriPagos</span>
                        </button>
                        <button type="button" id="btn-metodo-manual" class="flex flex-col items-center gap-2 rounded-lg border-2 border-slate-200 p-4 transition hover:border-primary hover:bg-primary/5 dark:border-navy-450">
                            <svg class="size-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="text-sm font-medium text-slate-700 dark:text-navy-100">Pago Manual</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Panel QR --}}
            <div id="pago-qr-panel" class="hidden space-y-4">
                <div class="text-center">
                    <div id="qr-loading" class="py-8">
                        <svg class="mx-auto size-8 animate-spin text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <p class="mt-2 text-sm text-slate-500">Generando QR...</p>
                    </div>
                    <div id="qr-image-container" class="hidden">
                        <img id="qr-image" src="" alt="QR de pago" class="mx-auto max-w-[250px] rounded-lg border" />
                        <p class="mt-2 text-sm font-medium text-slate-700 dark:text-navy-100">Monto: Bs. <span id="qr-monto-display"></span></p>
                        <p class="mt-1 text-xs text-slate-400">Escanea el QR para pagar. Verificando automaticamente...</p>
                        <div id="qr-polling-indicator" class="mt-2 flex items-center justify-center gap-2 text-xs text-info">
                            <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Esperando pago...
                        </div>
                    </div>
                    <div id="qr-error" class="hidden py-4 text-sm text-error"></div>
                </div>
                <button type="button" id="btn-volver-metodo" class="btn w-full rounded-lg border border-slate-300 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:border-navy-450 dark:text-navy-200">
                    Volver
                </button>
            </div>

            {{-- Panel Manual --}}
            <div id="pago-manual-panel" class="hidden space-y-4">
                <form method="POST" action="{{ route('pagos.store') }}" id="form-pago-manual">
                    @csrf
                    <input type="hidden" name="id_cita" value="{{ $cita->id_cita }}">
                    <input type="hidden" name="monto" id="manual-monto-input">

                    <div>
                        <label class="text-sm font-medium text-slate-600 dark:text-navy-200">Tipo de pago</label>
                        <select name="metodo_pago" class="form-select mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>

                    <div class="mt-3">
                        <label class="text-sm font-medium text-slate-600 dark:text-navy-200">Observaciones (opcional)</label>
                        <textarea name="observaciones" rows="2" class="form-textarea mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" placeholder="Notas del pago..."></textarea>
                    </div>

                    <div class="mt-3 rounded-lg bg-slate-50 p-3 dark:bg-navy-600">
                        <p class="text-sm text-slate-600 dark:text-navy-200">Monto a cobrar: <strong class="text-slate-800 dark:text-navy-50">Bs. <span id="manual-monto-display"></span></strong></p>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button type="submit" class="btn flex-1 rounded-lg bg-success py-2 text-sm font-medium text-white hover:bg-success/90">Confirmar Pago</button>
                        <button type="button" id="btn-volver-manual" class="btn flex-1 rounded-lg border border-slate-300 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:border-navy-450 dark:text-navy-200">Volver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Form oculto para cancelar --}}
<form method="POST" id="form-cancelar" style="display:none">
    @csrf @method('PATCH')
    <input type="hidden" name="motivo_cancelacion" id="motivo_cancelacion_input">
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

    document.querySelectorAll('.swal-confirmar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Confirmar esta cita?',
                text: 'El estado cambiara a confirmada',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0ea5e9',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, confirmar',
                cancelButtonText: 'Volver'
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    });

    document.querySelectorAll('.swal-atender').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Marcar como atendida?',
                text: 'Confirma que el paciente fue atendido',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, atendida',
                cancelButtonText: 'Volver'
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    });

    // ── Modal de Pago ──────────────────────────────────────────────
    const btnRegistrarPago = document.getElementById('btn-registrar-pago');
    const modalPago = document.getElementById('modal-pago');

    if (btnRegistrarPago && modalPago) {
        const overlay      = document.getElementById('modal-pago-overlay');
        const btnCerrar    = document.getElementById('btn-cerrar-modal');
        const metodoSel    = document.getElementById('pago-metodo-selector');
        const qrPanel      = document.getElementById('pago-qr-panel');
        const manualPanel  = document.getElementById('pago-manual-panel');
        const montoInput   = document.getElementById('pago-monto');
        let pollingInterval = null;

        const openModal  = () => modalPago.classList.remove('hidden');
        const closeModal = () => {
            modalPago.classList.add('hidden');
            resetPanels();
            if (pollingInterval) { clearInterval(pollingInterval); pollingInterval = null; }
        };
        const resetPanels = () => {
            metodoSel.classList.remove('hidden');
            qrPanel.classList.add('hidden');
            manualPanel.classList.add('hidden');
        };

        btnRegistrarPago.addEventListener('click', openModal);
        btnCerrar.addEventListener('click', closeModal);
        overlay.addEventListener('click', closeModal);

        // Volver buttons
        document.getElementById('btn-volver-metodo')?.addEventListener('click', () => {
            resetPanels();
            if (pollingInterval) { clearInterval(pollingInterval); pollingInterval = null; }
        });
        document.getElementById('btn-volver-manual')?.addEventListener('click', resetPanels);

        // ── QR Flow ──
        document.getElementById('btn-metodo-qr')?.addEventListener('click', async () => {
            const monto = parseFloat(montoInput.value);
            if (!monto || monto <= 0) {
                Swal.fire('Error', 'Ingrese un monto valido', 'error');
                return;
            }

            metodoSel.classList.add('hidden');
            qrPanel.classList.remove('hidden');

            document.getElementById('qr-loading').classList.remove('hidden');
            document.getElementById('qr-image-container').classList.add('hidden');
            document.getElementById('qr-error').classList.add('hidden');

            try {
                const res = await fetch(@json(route('api.pagos.generar-qr')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ id_cita: {{ $cita->id_cita }}, monto: monto }),
                });

                const data = await res.json();

                document.getElementById('qr-loading').classList.add('hidden');

                if (data.success && data.qr_url) {
                    document.getElementById('qr-image').src = data.qr_url;
                    document.getElementById('qr-monto-display').textContent = monto.toFixed(2);
                    document.getElementById('qr-image-container').classList.remove('hidden');

                    // Polling cada 5s
                    pollingInterval = setInterval(async () => {
                        try {
                            const verRes = await fetch(`/api/pagos/verificar-qr/${data.movimiento_id}`, {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                            });
                            const verData = await verRes.json();

                            if (verData.success && verData.estado === 'pagado') {
                                clearInterval(pollingInterval);
                                pollingInterval = null;
                                Swal.fire({
                                    title: 'Pago confirmado',
                                    text: 'El pago por QR fue recibido exitosamente',
                                    icon: 'success',
                                    confirmButtonColor: '#4f46e5',
                                }).then(() => location.reload());
                            }
                        } catch (e) {
                            console.error('Error polling QR:', e);
                        }
                    }, 5000);
                } else {
                    document.getElementById('qr-error').textContent = data.error || 'Error al generar QR';
                    document.getElementById('qr-error').classList.remove('hidden');
                }
            } catch (e) {
                document.getElementById('qr-loading').classList.add('hidden');
                document.getElementById('qr-error').textContent = 'Error de conexion al generar QR';
                document.getElementById('qr-error').classList.remove('hidden');
            }
        });

        // ── Manual Flow ──
        document.getElementById('btn-metodo-manual')?.addEventListener('click', () => {
            const monto = parseFloat(montoInput.value);
            if (!monto || monto <= 0) {
                Swal.fire('Error', 'Ingrese un monto valido', 'error');
                return;
            }

            metodoSel.classList.add('hidden');
            manualPanel.classList.remove('hidden');
            document.getElementById('manual-monto-input').value = monto.toFixed(2);
            document.getElementById('manual-monto-display').textContent = monto.toFixed(2);
        });

        // Confirmación manual con SweetAlert
        document.getElementById('form-pago-manual')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: '¿Confirmar pago?',
                text: `Se registrara un pago de Bs. ${document.getElementById('manual-monto-display').textContent}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, registrar pago',
                cancelButtonText: 'Cancelar',
            }).then(result => { if (result.isConfirmed) form.submit(); });
        });
    }

    document.querySelectorAll('.btn-cancelar').forEach(btn => {
        btn.addEventListener('click', function() {
            const citaId = this.dataset.id;
            Swal.fire({
                title: '¿Cancelar esta cita?',
                input: 'textarea',
                inputLabel: 'Motivo de cancelacion',
                inputPlaceholder: 'Escriba el motivo...',
                inputAttributes: { required: true },
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Si, cancelar cita',
                cancelButtonText: 'Volver',
                inputValidator: (value) => { if (!value) return 'Debe indicar un motivo'; }
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-cancelar');
                    form.action = `/citas/${citaId}/cancelar`;
                    document.getElementById('motivo_cancelacion_input').value = result.value;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
