<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Canceladas y No Asistidas</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #334155; padding: 16px; }
    .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 8px; margin-bottom: 10px; }
    .header h1 { font-size: 15px; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; }
    .header p { font-size: 8px; color: #94a3b8; }
    .titulo-doc { text-align: center; font-size: 12px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
    .filtros { font-size: 8px; color: #64748b; text-align: center; margin-bottom: 10px; }
    .kpis { display: table; width: 100%; margin-bottom: 10px; }
    .kpi { display: table-cell; text-align: center; border: 1px solid #e2e8f0; padding: 6px; }
    .kpi .val { font-size: 14px; font-weight: 700; }
    .kpi .lbl { font-size: 7px; color: #94a3b8; }
    table.reporte { width: 100%; border-collapse: collapse; }
    table.reporte th { background: #f1f5f9; padding: 4px 6px; font-size: 8px; font-weight: 700; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; text-align: left; }
    table.reporte td { padding: 4px 6px; font-size: 9px; border-bottom: 1px solid #f1f5f9; }
    table.reporte tr:nth-child(even) td { background: #f8fafc; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 20px; font-size: 7px; font-weight: 600; }
    .badge-error { background: #fee2e2; color: #dc2626; }
    .badge-warning { background: #fef9c3; color: #ca8a04; }
    .footer { text-align: center; margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ config('app.name', 'Clínica Médicos Cristianos Solidarios') }}</h1>
    <p>Sistema de Citas Médicas</p>
</div>
<div class="titulo-doc">Reporte: Canceladas y No Asistidas</div>
<div class="filtros">Período: {{ $filtros['fecha_desde'] ?? '—' }} al {{ $filtros['fecha_hasta'] ?? '—' }} · Generado: {{ $generadoEn }}</div>

<div class="kpis">
    <div class="kpi"><div class="val" style="color:#dc2626;">{{ $citas->count() }}</div><div class="lbl">Total</div></div>
    <div class="kpi"><div class="val" style="color:#dc2626;">{{ $porEstado['cancelada'] ?? 0 }}</div><div class="lbl">Canceladas</div></div>
    <div class="kpi"><div class="val" style="color:#ca8a04;">{{ $porEstado['no_asistio'] ?? 0 }}</div><div class="lbl">No Asistió</div></div>
</div>

<table class="reporte">
    <thead>
        <tr>
            <th>Código</th>
            <th>Fecha Cita</th>
            <th>Paciente</th>
            <th>Médico</th>
            <th>Estado</th>
            <th>Motivo</th>
        </tr>
    </thead>
    <tbody>
        @forelse($citas as $cita)
        <tr>
            <td style="font-family:monospace;font-size:8px;color:#94a3b8;">{{ $cita->codigo_cita }}</td>
            <td>{{ $cita->fecha_cita->format('d/m/Y') }}</td>
            <td>{{ $cita->paciente?->apellidos }}, {{ $cita->paciente?->nombres }}</td>
            <td>Dr. {{ $cita->medico?->apellidos }}</td>
            <td><span class="badge {{ $cita->estado_cita === 'cancelada' ? 'badge-error' : 'badge-warning' }}">{{ $cita->estado_cita === 'no_asistio' ? 'No Asistió' : 'Cancelada' }}</span></td>
            <td style="font-size:8px;color:#64748b;">{{ \Illuminate\Support\Str::limit($cita->motivo_cancelacion ?? '—', 40) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:12px;">Sin registros</td></tr>
        @endforelse
    </tbody>
</table>
<div class="footer">{{ config('app.name') }} · Generado el {{ $generadoEn }} · {{ $citas->count() }} citas</div>
</body>
</html>
