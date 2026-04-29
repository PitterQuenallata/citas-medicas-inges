<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Médicos</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #334155; padding: 16px; }
    .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 8px; margin-bottom: 10px; }
    .header h1 { font-size: 15px; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; }
    .header p { font-size: 8px; color: #94a3b8; margin-top: 2px; }
    .titulo-doc { text-align: center; font-size: 12px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
    .filtros { font-size: 8px; color: #64748b; text-align: center; margin-bottom: 10px; }
    table.reporte { width: 100%; border-collapse: collapse; margin-top: 6px; }
    table.reporte th { background: #f1f5f9; padding: 4px 6px; font-size: 8px; font-weight: 700; text-transform: uppercase; color: #64748b; text-align: left; border-bottom: 1px solid #e2e8f0; }
    table.reporte td { padding: 4px 6px; font-size: 9px; border-bottom: 1px solid #f1f5f9; }
    table.reporte tr:nth-child(even) td { background: #f8fafc; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 20px; font-size: 7px; font-weight: 600; }
    .badge-success { background: #dcfce7; color: #16a34a; }
    .badge-error { background: #fee2e2; color: #dc2626; }
    .color-success { color: #16a34a; }
    .color-warning { color: #ca8a04; }
    .color-error { color: #dc2626; }
    .footer { text-align: center; margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ config('app.name', 'Clínica Médicos Cristianos Solidarios') }}</h1>
    <p>Sistema de Citas Médicas</p>
</div>
<div class="titulo-doc">Reporte de Actividad de Médicos</div>
<div class="filtros">Período: {{ $filtros['fecha_desde'] ?? '—' }} al {{ $filtros['fecha_hasta'] ?? '—' }} · Generado: {{ $generadoEn }}</div>

<table class="reporte">
    <thead>
        <tr>
            <th>Médico</th>
            <th>Especialidad</th>
            <th class="text-center">Total</th>
            <th class="text-center">Atendidas</th>
            <th class="text-center">Canceladas</th>
            <th class="text-center">No Asistió</th>
            <th class="text-center">Tasa</th>
        </tr>
    </thead>
    <tbody>
        @forelse($medicos as $m)
        @php $tasa = $m->total_citas > 0 ? round(($m->citas_atendidas / $m->total_citas) * 100, 1) : 0; @endphp
        <tr>
            <td><strong>Dr. {{ $m->apellidos }}, {{ $m->nombres }}</strong><br><span style="font-size:7px;color:#94a3b8;">{{ $m->codigo_medico }}</span></td>
            <td style="font-size:8px;color:#64748b;">{{ $m->especialidades->pluck('nombre_especialidad')->join(', ') ?: '—' }}</td>
            <td class="text-center"><strong>{{ $m->total_citas }}</strong></td>
            <td class="text-center"><span class="badge badge-success">{{ $m->citas_atendidas }}</span></td>
            <td class="text-center"><span class="badge badge-error">{{ $m->citas_canceladas }}</span></td>
            <td class="text-center"><span class="badge badge-error">{{ $m->citas_no_asistio }}</span></td>
            <td class="text-center" style="font-weight:700; color: {{ $tasa >= 70 ? '#16a34a' : ($tasa >= 40 ? '#ca8a04' : '#dc2626') }};">{{ $tasa }}%</td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center" style="color:#94a3b8; padding: 12px;">Sin registros</td></tr>
        @endforelse
    </tbody>
</table>
<div class="footer">{{ config('app.name') }} · Generado el {{ $generadoEn }}</div>
</body>
</html>
