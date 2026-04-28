<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Pagos</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #334155; padding: 16px; }
    .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 8px; margin-bottom: 10px; }
    .header h1 { font-size: 15px; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; }
    .header p { font-size: 8px; color: #94a3b8; margin-top: 2px; }
    .titulo-doc { text-align: center; font-size: 12px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
    .filtros { font-size: 8px; color: #64748b; text-align: center; margin-bottom: 10px; }
    .resumen { display: table; width: 100%; margin-bottom: 10px; border: 1px solid #e2e8f0; border-radius: 6px; }
    .resumen-item { display: table-cell; text-align: center; padding: 8px; }
    .resumen-item .val { font-size: 14px; font-weight: 700; }
    .resumen-item .lbl { font-size: 7px; color: #94a3b8; margin-top: 2px; }
    table.reporte { width: 100%; border-collapse: collapse; }
    table.reporte th { background: #f1f5f9; padding: 4px 6px; font-size: 8px; font-weight: 700; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; text-align: left; }
    table.reporte td { padding: 4px 6px; font-size: 9px; border-bottom: 1px solid #f1f5f9; }
    table.reporte tr:nth-child(even) td { background: #f8fafc; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 20px; font-size: 7px; font-weight: 600; }
    .badge-success { background: #dcfce7; color: #16a34a; }
    .badge-warning { background: #fef9c3; color: #ca8a04; }
    .badge-error { background: #fee2e2; color: #dc2626; }
    .total-row td { font-weight: 700; border-top: 2px solid #e2e8f0; }
    .footer { text-align: center; margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ config('app.name', 'Clínica Médicos Cristianos Solidarios') }}</h1>
    <p>Sistema de Citas Médicas</p>
</div>
<div class="titulo-doc">Reporte de Ingresos y Pagos</div>
<div class="filtros">Período: {{ $filtros['fecha_desde'] ?? '—' }} al {{ $filtros['fecha_hasta'] ?? '—' }} · Generado: {{ $generadoEn }}</div>

<div class="resumen">
    <div class="resumen-item" style="background:#f0fdf4;"><div class="val" style="color:#16a34a;">Bs. {{ number_format($totalPagado, 2) }}</div><div class="lbl">Total Cobrado</div></div>
    <div class="resumen-item" style="background:#fefce8;"><div class="val" style="color:#ca8a04;">Bs. {{ number_format($totalPendiente, 2) }}</div><div class="lbl">Pendiente</div></div>
    <div class="resumen-item" style="background:#fff1f2;"><div class="val" style="color:#dc2626;">Bs. {{ number_format($totalAnulado, 2) }}</div><div class="lbl">Anulado</div></div>
</div>

<table class="reporte">
    <thead>
        <tr>
            <th>Código Pago</th>
            <th>Fecha</th>
            <th>Paciente</th>
            <th>Método</th>
            <th class="text-right">Monto</th>
            <th class="text-center">Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pagos as $pago)
        @php
            $bc = match($pago->estado_pago) { 'pagado' => 'badge-success', 'anulado' => 'badge-error', default => 'badge-warning' };
        @endphp
        <tr>
            <td style="font-family:monospace;font-size:8px;color:#94a3b8;">{{ $pago->codigo_pago }}</td>
            <td>{{ $pago->fecha_pago?->format('d/m/Y') ?? $pago->created_at->format('d/m/Y') }}</td>
            <td>{{ $pago->cita?->paciente?->apellidos }}, {{ $pago->cita?->paciente?->nombres }}</td>
            <td>{{ ucfirst($pago->metodo_pago) }}</td>
            <td class="text-right" style="font-weight:600;">Bs. {{ number_format($pago->monto, 2) }}</td>
            <td class="text-center"><span class="badge {{ $bc }}">{{ ucfirst($pago->estado_pago) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center" style="color:#94a3b8; padding:12px;">Sin registros</td></tr>
        @endforelse
    </tbody>
    @if($pagos->count())
    <tfoot>
        <tr class="total-row">
            <td colspan="4" class="text-right">TOTAL COBRADO:</td>
            <td class="text-right" style="color:#16a34a;">Bs. {{ number_format($totalPagado, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>
<div class="footer">{{ config('app.name') }} · Generado el {{ $generadoEn }} · {{ $pagos->count() }} registros</div>
</body>
</html>
