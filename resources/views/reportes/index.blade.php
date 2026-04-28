@extends('layouts.app')
@section('title', 'Reportes del Sistema')

@section('content')
{{-- KPIs rápidos del mes actual --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-6">
    <div class="card p-4 flex items-center gap-3">
        <div class="flex size-11 items-center justify-center rounded-xl bg-primary/10">
            <svg class="size-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 dark:text-navy-300">Citas este mes</p>
            <p class="text-xl font-bold text-slate-700 dark:text-navy-100">{{ number_format($stats['total_citas_mes']) }}</p>
        </div>
    </div>
    <div class="card p-4 flex items-center gap-3">
        <div class="flex size-11 items-center justify-center rounded-xl bg-success/10">
            <svg class="size-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 dark:text-navy-300">Pacientes activos</p>
            <p class="text-xl font-bold text-slate-700 dark:text-navy-100">{{ number_format($stats['total_pacientes']) }}</p>
        </div>
    </div>
    <div class="card p-4 flex items-center gap-3">
        <div class="flex size-11 items-center justify-center rounded-xl bg-info/10">
            <svg class="size-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 dark:text-navy-300">Médicos activos</p>
            <p class="text-xl font-bold text-slate-700 dark:text-navy-100">{{ number_format($stats['total_medicos']) }}</p>
        </div>
    </div>
    <div class="card p-4 flex items-center gap-3">
        <div class="flex size-11 items-center justify-center rounded-xl bg-warning/10">
            <svg class="size-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 dark:text-navy-300">Ingresos este mes</p>
            <p class="text-xl font-bold text-slate-700 dark:text-navy-100">Bs. {{ number_format($stats['ingresos_mes'], 2) }}</p>
        </div>
    </div>
</div>

{{-- Grid de reportes --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

    @php
    $reportes = [
        [
            'titulo'      => 'Citas por Período',
            'descripcion' => 'Detalle de citas con estados, médicos y pacientes en un rango de fechas.',
            'color'       => 'primary',
            'icon'        => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'route_ver'   => 'reportes.citas',
            'route_pdf'   => ['reportes.pdf', 'citas'],
        ],
        [
            'titulo'      => 'Actividad de Médicos',
            'descripcion' => 'Citas atendidas, canceladas y tasa de atención por médico.',
            'color'       => 'info',
            'icon'        => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
            'route_ver'   => 'reportes.medicos',
            'route_pdf'   => ['reportes.pdf', 'medicos'],
        ],
        [
            'titulo'      => 'Pacientes',
            'descripcion' => 'Lista de pacientes con historial de citas, distribución por sexo y estado.',
            'color'       => 'success',
            'icon'        => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            'route_ver'   => 'reportes.pacientes',
            'route_pdf'   => ['reportes.pdf', 'pacientes'],
        ],
        [
            'titulo'      => 'Ingresos y Pagos',
            'descripcion' => 'Total recaudado, desglose por método de pago y estado de cobros.',
            'color'       => 'warning',
            'icon'        => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'route_ver'   => 'reportes.pagos',
            'route_pdf'   => ['reportes.pdf', 'pagos'],
        ],
        [
            'titulo'      => 'Por Especialidad',
            'descripcion' => 'Citas e ingresos por especialidad médica en el período seleccionado.',
            'color'       => 'secondary',
            'icon'        => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
            'route_ver'   => 'reportes.especialidades',
            'route_pdf'   => ['reportes.pdf', 'especialidades'],
        ],
        [
            'titulo'      => 'Notificaciones',
            'descripcion' => 'Historial de notificaciones por canal (WhatsApp, email, SMS) y estado de envío.',
            'color'       => 'error',
            'icon'        => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
            'route_ver'   => 'reportes.notificaciones',
            'route_pdf'   => ['reportes.pdf', 'notificaciones'],
        ],
        [
            'titulo'      => 'Canceladas / No Asistidas',
            'descripcion' => 'Análisis de citas canceladas y pacientes que no asistieron a su cita.',
            'color'       => 'error',
            'icon'        => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            'route_ver'   => 'reportes.canceladas',
            'route_pdf'   => ['reportes.pdf', 'canceladas'],
        ],
        [
            'titulo'      => 'Resumen Mensual',
            'descripcion' => 'KPIs del mes: citas, ingresos, tasa de asistencia y médico destacado.',
            'color'       => 'primary',
            'badge'       => 'Ejecutivo',
            'icon'        => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'route_ver'   => 'reportes.resumen-mensual',
            'route_pdf'   => ['reportes.pdf', 'resumen-mensual'],
            'destacado'   => true,
        ],
    ];
    @endphp

    @foreach($reportes as $r)
    <div class="card flex flex-col {{ !empty($r['destacado']) ? 'ring-2 ring-primary/25' : '' }}">
        {{-- Cuerpo superior --}}
        <div class="flex flex-col p-4 sm:p-5 flex-1">
            {{-- Icono + Título lado a lado --}}
            <div class="flex items-center gap-3 mb-3">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-{{ $r['color'] }}/10">
                    <svg class="size-6 text-{{ $r['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $r['icon'] }}"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 class="font-semibold text-slate-700 dark:text-navy-100 leading-tight">{{ $r['titulo'] }}</h3>
                    @if(!empty($r['badge']))
                        <span class="badge rounded-full bg-{{ $r['color'] }}/10 px-2 py-0.5 text-xs text-{{ $r['color'] }} mt-0.5 inline-block">{{ $r['badge'] }}</span>
                    @endif
                </div>
            </div>
            {{-- Descripción --}}
            <p class="text-xs text-slate-400 dark:text-navy-300 leading-relaxed">{{ $r['descripcion'] }}</p>
        </div>

        {{-- Botones de acción — patrón user-card-6 --}}
        <div class="flex divide-x divide-slate-150 border-t border-slate-150 dark:divide-navy-500 dark:border-navy-500">
            <a href="{{ route($r['route_ver']) }}"
               class="btn h-10 w-full rounded-none rounded-bl-lg text-xs font-medium text-{{ $r['color'] }} hover:bg-{{ $r['color'] }}/10 focus:bg-{{ $r['color'] }}/10 active:bg-{{ $r['color'] }}/15 dark:hover:bg-{{ $r['color'] }}/20">
                Ver reporte
            </a>
            <a href="{{ route($r['route_pdf'][0], $r['route_pdf'][1]) }}"
               class="btn h-10 rounded-none rounded-br-lg px-4 font-medium text-slate-500 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:text-navy-200 dark:hover:bg-navy-300/20"
               title="Exportar PDF">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </a>
        </div>
    </div>
    @endforeach

</div>

@endsection
