<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Citas</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #334155; padding: 16px; }
    .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 8px; margin-bottom: 10px; }
    .header h1 { font-size: 15px; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; }
    .header p { font-size: 8px; color: #94a3b8; margin-top: 2px; }
    .titulo-doc { text-align: center; font-size: 12px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
    .filtros { font-size: 8px; color: #64748b; text-align: center; margin-bottom: 10px; }
    .kpis { display: table; width: 100%; margin-bottom: 10px; }
    .kpi { display: table-cell; text-align: center; border: 1px solid #e2e8f0; padding: 6px; }
    .kpi .val { font-size: 14px; font-weight: 700; }
    .kpi .lbl { font-size: 7px; color: #94a3b8; margin-top: 2px; }
    table.reporte { width: 100%; border-collapse: collapse; margin-top: 6px; }
    table.reporte th { background: #f1f5f9; padding: 4px 6px; font-size: 8px; font-weight: 700; text-transform: uppercase; color: #64748b; text-align: left; border-bottom: 1px solid #e2e8f0; }
    table.reporte td { padding: 4px 6px; font-size: 9px; border-bottom: 1px solid #f1f5f9; }
    table.reporte tr:nth-child(even) td { background: #f8fafc; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 20px; font-size: 7px; font-weight: 600; }
    .badge-success { background: #dcfce7; color: #16a34a; }
    .badge-warning { background: #fef9c3; color: #ca8a04; }
    .badge-error { background: #fee2e2; color: #dc2626; }
    .badge-info { background: #dbeafe; color: #2563eb; }
    .badge-secondary { background: #ede9fe; color: #7c3aed; }
    .footer { text-align: center; margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; font-size: 7px; color: #94a3b8; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .color-success { color: #16a34a; }
    .color-error { color: #dc2626; }
    .color-warning { color: #ca8a04; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ config('app.name', 'Clínica Médicos Cristianos Solidarios') }}</h1>
    <p>Sistema de Citas Médicas</p>
</div>
<div class="titulo-doc">Reporte de Citas por Período</div>
<div class="filtros">
    Período: {{ $filtros['fecha_desde'] ?? '—' }} al {{ $filtros['fecha_hasta'] ?? '—' }}
    @if(!empty($filtros['estado'])) · Estado: {{ ucfirst($filtros['estado']) }} @endif
    · Generado: {{ $generadoEn }}
</div>

{{-- Totales por estado --}}
<div class="kpis">
    @foreach(['pendiente' => ['Pendiente','#ca8a04'], 'confirmada' => ['Confirmada','#2563eb'], 'atendida' => ['Atendida','#16a34a'], 'cancelada' => ['Cancelada','#dc2626'], 'no_asistio' => ['No Asistió','#dc2626']] as $e => $cfg)
    <div class="kpi">
        <div class="val" style="color: {{ $cfg[1] }};">{{ $totalesPorEstado[$e] ?? 0 }}</div>
        <div class="lbl">{{ $cfg[0] }}</div>
    </div>
    @endforeach
</div>

<table class="reporte">
    <thead>
        <tr>
            <th>Código</th>
            <th>Fecha</th>
            <th>Paciente</th>
            <th>Médico</th>
            <th>Hora</th>
            <th class="text-center">Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($citas as $cita)
        @php
            $bc = match($cita->estado_cita) {
                'atendida' => 'badge-success', 'confirmada' => 'badge-info',
                'cancelada','no_asistio' => 'badge-error',
                'reprogramada' => 'badge-secondary', default => 'badge-warning'
            };
        @endphp
        <tr>
            <td style="font-family: monospace; font-size: 8px; color: #64748b;">{{ $cita->codigo_cita }}</td>
            <td>{{ $cita->fecha_cita->format('d/m/Y') }}</td>
            <td>{{ $cita->paciente?->apellidos }}, {{ $cita->paciente?->nombres }}</td>
            <td>Dr. {{ $cita->medico?->apellidos }}</td>
            <td>{{ substr($cita->hora_inicio,0,5) }}–{{ substr($cita->hora_fin,0,5) }}</td>
            <td class="text-center"><span class="badge {{ $bc }}">{{ ucfirst(str_replace('_',' ',$cita->estado_cita)) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center" style="color:#94a3b8; padding: 12px;">Sin registros</td></tr>
        @endforelse
    </tbody>
</table>
<div class="footer">{{ config('app.name') }} · Generado el {{ $generadoEn }} · Total: {{ $citas->count() }} citas</div>
</body>
</html>
