@extends('layouts.app')
@section('title', 'Notificaciones WhatsApp')

@section('content')

<div class="flex items-center justify-between pb-4">
    
    <div class="flex items-center gap-2">
        <div class="flex gap-2">
            <span class="badge rounded-full bg-success/10 text-success text-xs px-3 py-1">Enviadas: {{ $conteos['enviadas'] }}</span>
            <span class="badge rounded-full bg-error/10 text-error text-xs px-3 py-1">Fallidas: {{ $conteos['fallidas'] }}</span>
            <span class="badge rounded-full bg-warning/10 text-warning text-xs px-3 py-1">Pendientes: {{ $conteos['pendientes'] }}</span>
        </div>
        <form method="POST" action="{{ route('notificaciones.enviar-hoy') }}" id="form-enviar-hoy">
            @csrf
            <button type="submit" class="btn h-9 rounded-full px-5 text-xs font-medium text-white hover:opacity-90" style="background-color: #25D366;">
                <svg class="mr-1.5 inline size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.61.61l4.458-1.495A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.37 0-4.567-.82-6.3-2.188l-.44-.352-3.2 1.073 1.073-3.2-.352-.44A9.955 9.955 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
                Notificar citas de hoy ({{ $citasHoy }})
            </button>
        </form>
    </div>
</div>

{{-- Filtros --}}
<div class="card mb-4 px-4 py-3 sm:px-5">
    <form method="GET" action="{{ route('notificaciones.index') }}" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-navy-300">Estado</label>
            <select name="estado" class="form-select mt-1 h-8 rounded-full border border-slate-300 bg-white px-3 text-xs dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                <option value="">Todos</option>
                <option value="enviado" @selected(request('estado') === 'enviado')>Enviado</option>
                <option value="fallido" @selected(request('estado') === 'fallido')>Fallido</option>
                <option value="pendiente" @selected(request('estado') === 'pendiente')>Pendiente</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-navy-300">Tipo</label>
            <select name="tipo" class="form-select mt-1 h-8 rounded-full border border-slate-300 bg-white px-3 text-xs dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100">
                <option value="">Todos</option>
                <option value="reserva" @selected(request('tipo') === 'reserva')>Reserva</option>
                <option value="confirmacion" @selected(request('tipo') === 'confirmacion')>Confirmacion</option>
                <option value="cancelacion" @selected(request('tipo') === 'cancelacion')>Cancelacion</option>
                <option value="reprogramacion" @selected(request('tipo') === 'reprogramacion')>Reprogramacion</option>
                <option value="recordatorio" @selected(request('tipo') === 'recordatorio')>Recordatorio</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500 dark:text-navy-300">Fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-input mt-1 h-8 rounded-full border border-slate-300 bg-white px-3 text-xs dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100" />
        </div>
        <button type="submit" class="btn h-8 rounded-full bg-primary px-4 text-xs font-medium text-white hover:bg-primary/90">Filtrar</button>
        @if(request()->hasAny(['estado', 'tipo', 'fecha']))
            <a href="{{ route('notificaciones.index') }}" class="btn h-8 rounded-full border border-slate-300 px-4 text-xs text-slate-600 hover:bg-slate-50 dark:border-navy-450 dark:text-navy-200">Limpiar</a>
        @endif
    </form>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Fecha Envio</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Cita</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Tipo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Mensaje</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notificaciones as $notif)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-3 text-sm text-slate-600 dark:text-navy-100 lg:px-5">
                        {{ $notif->fecha_envio?->format('d/m/Y H:i') }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 text-sm text-slate-600 dark:text-navy-100 lg:px-5">
                        {{ $notif->paciente?->nombres }} {{ $notif->paciente?->apellidos }}
                        @if($notif->paciente?->telefono)
                            <span class="block text-xs text-slate-400">{{ $notif->paciente->telefono }}</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 text-sm lg:px-5">
                        @if($notif->cita)
                            <a href="{{ route('citas.show', $notif->cita->id_cita) }}" class="text-primary hover:underline dark:text-accent">
                                {{ $notif->cita->codigo_cita }}
                            </a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 text-sm text-slate-600 dark:text-navy-100 lg:px-5">
                        {{ $notif->tipo_label }}
                    </td>
                    <td class="max-w-xs truncate px-3 py-3 text-sm text-slate-600 dark:text-navy-100 lg:px-5" title="{{ $notif->mensaje }}">
                        {{ Str::limit($notif->mensaje, 60) }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-3 lg:px-5">
                        <span class="badge rounded-full text-xs px-2.5 py-0.5 {{ $notif->estado_badge_class }}">
                            {{ ucfirst($notif->estado_envio) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-slate-400 dark:text-navy-300">
                        <svg class="mx-auto size-10 opacity-30 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Sin notificaciones enviadas aun.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($notificaciones->hasPages())
    <div class="mt-4">
        {{ $notificaciones->links() }}
    </div>
    @endif
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

    document.getElementById('form-enviar-hoy')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: '¿Enviar recordatorios?',
            text: 'Se enviara un WhatsApp a todas las citas pendientes/confirmadas de hoy que aun no tienen recordatorio',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#25D366',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Si, enviar a todos',
            cancelButtonText: 'Cancelar'
        }).then(result => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
