@extends('layouts.app')
@section('title', 'Mis Estadísticas')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
            Estadísticas - Dr. {{ $medico->nombres }} {{ $medico->apellidos }}
        </h2>
        <p class="mt-1 text-sm text-slate-400 dark:text-navy-300">Resumen de actividad médica</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn h-9 border border-slate-300 px-4 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200 dark:hover:bg-navy-600">
            <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('dashboard.agenda') }}" class="btn h-9 border border-slate-300 px-4 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:border-navy-450 dark:text-navy-200 dark:hover:bg-navy-600">
            Mi Agenda
        </a>
    </div>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-6">
    <div class="card p-4">
        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-navy-300">Total citas</p>
        <p class="mt-2 text-2xl font-semibold text-slate-700 dark:text-navy-100">{{ $totalCitas }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-navy-300">Tasa de atención</p>
        <p class="mt-2 text-2xl font-semibold text-success">{{ $tasaAtencion }}%</p>
    </div>
    <div class="card p-4">
        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-navy-300">Cancelaciones</p>
        <p class="mt-2 text-2xl font-semibold text-error">{{ $tasaCancelacion }}%</p>
    </div>
    <div class="card p-4">
        <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-navy-300">Atendidas</p>
        <p class="mt-2 text-2xl font-semibold text-primary">{{ $estadosCitas['atendida'] ?? 0 }}</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    {{-- Gráfico: Citas por mes --}}
    <div class="card p-4 sm:p-5">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Citas por Mes (últimos 6 meses)</h3>
        <div id="chart-citas-mes" class="min-h-[280px]"></div>
    </div>

    {{-- Gráfico: Distribución por estado --}}
    <div class="card p-4 sm:p-5">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">Distribución por Estado</h3>
        <div id="chart-estados" class="min-h-[280px]"></div>
    </div>
</div>

{{-- Top pacientes --}}
<div class="card mt-4 px-4 pb-4 sm:px-5">
    <div class="flex items-center py-4">
        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">Pacientes más Frecuentes</h3>
    </div>
    <div class="min-w-full overflow-x-auto">
        <table class="is-hoverable w-full text-left">
            <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">#</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Paciente</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Total Citas</th>
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100">Frecuencia</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topPacientes as $index => $item)
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-500">{{ $index + 1 }}</td>
                    <td class="whitespace-nowrap px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-navy-100">
                        {{ $item->paciente?->nombres }} {{ $item->paciente?->apellidos }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-2.5 text-sm text-slate-600">{{ $item->total_citas }}</td>
                    <td class="px-3 py-2.5">
                        <div class="progress h-2 bg-slate-150 dark:bg-navy-500">
                            <div class="rounded-full bg-primary dark:bg-accent" style="width: {{ $totalCitas > 0 ? round(($item->total_citas / $totalCitas) * 100) : 0 }}%"></div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-3 py-6 text-center text-slate-400">Sin datos de pacientes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Citas por mes - Bar chart
    const chartCitasMes = new ApexCharts(document.querySelector('#chart-citas-mes'), {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        series: [{ name: 'Citas', data: @json($mesesData) }],
        xaxis: { categories: @json($mesesLabels) },
        colors: ['#4C78DD'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
        tooltip: { theme: 'light' }
    });
    chartCitasMes.render();

    // Estados - Donut chart
    @php
        $estadoLabels = [];
        $estadoValues = [];
        $estadoColors = [
            'pendiente' => '#F59E0B',
            'confirmada' => '#3B82F6',
            'atendida' => '#10B981',
            'cancelada' => '#EF4444',
            'reprogramada' => '#8B5CF6',
            'no_asistio' => '#6B7280',
        ];
        $colorsArr = [];
        foreach ($estadosCitas as $estado => $total) {
            $estadoLabels[] = ucfirst($estado);
            $estadoValues[] = $total;
            $colorsArr[] = $estadoColors[$estado] ?? '#94A3B8';
        }
    @endphp

    const chartEstados = new ApexCharts(document.querySelector('#chart-estados'), {
        chart: { type: 'donut', height: 280 },
        series: @json($estadoValues),
        labels: @json($estadoLabels),
        colors: @json($colorsArr),
        legend: { position: 'bottom' },
        dataLabels: { enabled: true, formatter: function(val) { return val.toFixed(1) + '%'; } },
        plotOptions: { pie: { donut: { size: '60%' } } },
        tooltip: { theme: 'light' }
    });
    chartEstados.render();
});
</script>
@endpush
