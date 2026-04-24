@extends('layouts.app')
@section('title', 'Notificaciones WhatsApp')

@section('content')

{{-- PENDIENTE: integrar API de WhatsApp (Twilio / Meta Cloud API) --}}
{{-- PENDIENTE: boton "Notificar" en citas del dia y un dia antes --}}
{{-- PENDIENTE: cron/job para envio automatico de recordatorios --}}

<div class="mb-4 rounded-lg border border-warning/40 bg-warning/5 px-4 py-3 text-sm text-warning dark:border-warning/30">
    <div class="flex items-center gap-2 font-medium">
        <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        Modulo en desarrollo — Pendiente integrar API de WhatsApp (Twilio / Meta Cloud API)
    </div>
    <ul class="mt-1 ml-6 list-disc text-xs opacity-80 space-y-0.5">
        <li>Boton "Notificar" en citas del dia actual y citas del dia siguiente</li>
        <li>Envio automatico de recordatorio 24h antes de la cita</li>
        <li>Confirmacion de cita por respuesta del paciente</li>
        <li>Notificacion de cancelacion o reprogramacion</li>
    </ul>
</div>

<div class="card px-4 pb-4 sm:px-5">
    <div class="flex items-center justify-between py-4">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Historial de Notificaciones</h3>
        <div class="flex gap-2">
            <span class="badge rounded-full bg-success/10 text-success text-xs px-3 py-1">Enviadas: 0</span>
            <span class="badge rounded-full bg-error/10 text-error text-xs px-3 py-1">Fallidas: 0</span>
            <span class="badge rounded-full bg-warning/10 text-warning text-xs px-3 py-1">Pendientes: 0</span>
        </div>
    </div>

    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Fecha Envio</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Tipo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Canal</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Mensaje</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-slate-400 dark:text-navy-300">
                        <svg class="mx-auto size-10 opacity-30 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Sin notificaciones enviadas aun.<br>
                        <span class="text-xs">Las notificaciones de WhatsApp apareceran aqui una vez configurada la API.</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Diseno planeado para botones en citas/index.blade.php:
    - Boton WhatsApp verde junto a cada cita con fecha = hoy o fecha = manana
    - Al hacer clic -> modal de confirmacion -> POST /notificaciones/{cita}/enviar
    - El boton se desactiva si ya existe una notificacion 'enviado' para esa cita y tipo
--}}
@endsection
