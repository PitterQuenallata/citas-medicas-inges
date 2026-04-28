<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte por Especialidad</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #334155; padding: 16px; }
    .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 8px; margin-bottom: 10px; }
    .header h1 { font-size: 15px; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; }
    .header p { font-size: 8px; color: #94a3b8; }
    .titulo-doc { text-align: center; font-size: 12px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
    .filtros { font-size: 8px; color: #64748b; text-align: center; margin-bottom: 10px; }
    table.reporte { width: 100%; border-collapse: collapse; }
    table.reporte th { background: #f1f5f9; padding: 4px 6px; font-size: 8px; font-weight: 700; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; text-align: left; }
    table.reporte td { padding: 4px 6px; font-size: 9px; border-bottom: 1px solid #f1f5f9; }
    table.reporte tr:nth-child(even) td { background: #f8fafc; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 20px; font-size: 7px; font-weight: 600; }
    .badge-success { background: #dcfce7; color: #16a34a; }
    .total-row td { font-weight: 700; border-top: 2px solid #e2e8f0; }
    .footer { text-align: center; margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ config('app.name', 'Clínica Médicos Cristianos Solidarios') }}</h1>
    <p>Sistema de Citas Médicas</p>
</div>
<div class="titulo-doc">Reporte por Especialidad</div>
<div class="filtros">Período: {{ $filtros['fecha_desde'] ?? '—' }} al {{ $filtros['fecha_hasta'] ?? '—' }} · Generado: {{ $generadoEn }}</div>

<table class="reporte">
    <thead>
        <tr>
            <th>Especialidad</th>
            <th class="text-center">Médicos</th>
            <th class="text-center">Total Citas</th>
            <th class="text-center">Atendidas</th>
            <th class="text-right">Costo Consulta</th>
            <th class="text-right">Ingresos</th>
        </tr>
    </thead>
    <tbody>
        @forelse($especialidades as $esp)
        <tr>
            <td><strong>{{ $esp->nombre_especialidad }}</strong></td>
            <td class="text-center">{{ $esp->total_medicos }}</td>
            <td class="text-center"><strong>{{ $esp->total_citas }}</strong></td>
            <td class="text-center"><span class="badge badge-success">{{ $esp->citas_atendidas }}</span></td>
            <td class="text-right">Bs. {{ number_format($esp->costo_consulta, 2) }}</td>
            <td class="text-right" style="font-weight:600;color:#16a34a;">Bs. {{ number_format($esp->ingresos, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center" style="color:#94a3b8;padding:12px;">Sin registros</td></tr>
        @endforelse
    </tbody>
    @if($especialidades->count())
    <tfoot>
        <tr class="total-row">
            <td colspan="5" class="text-right">TOTAL INGRESOS:</td>
            <td class="text-right" style="color:#16a34a;">Bs. {{ number_format($especialidades->sum('ingresos'), 2) }}</td>
        </tr>
    </tfoot>
    @endif
</table>
<div class="footer">{{ config('app.name') }} · Generado el {{ $generadoEn }}</div>
</body>
</html>
