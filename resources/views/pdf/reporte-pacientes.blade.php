<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Pacientes</title>
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
    table.reporte { width: 100%; border-collapse: collapse; }
    table.reporte th { background: #f1f5f9; padding: 4px 6px; font-size: 8px; font-weight: 700; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; text-align: left; }
    table.reporte td { padding: 4px 6px; font-size: 9px; border-bottom: 1px solid #f1f5f9; }
    table.reporte tr:nth-child(even) td { background: #f8fafc; }
    .text-center { text-align: center; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 20px; font-size: 7px; font-weight: 600; }
    .badge-success { background: #dcfce7; color: #16a34a; }
    .badge-muted { background: #f1f5f9; color: #64748b; }
    .footer { text-align: center; margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; font-size: 7px; color: #94a3b8; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ config('app.name', 'Clínica Médicos Cristianos Solidarios') }}</h1>
    <p>Sistema de Citas Médicas</p>
</div>
<div class="titulo-doc">Reporte de Pacientes</div>
<div class="filtros">Generado: {{ $generadoEn }}</div>

<div class="kpis">
    <div class="kpi"><div class="val">{{ $pacientes->count() }}</div><div class="lbl">Total</div></div>
    <div class="kpi"><div class="val" style="color:#16a34a;">{{ $distribucionEstado['activo'] ?? 0 }}</div><div class="lbl">Activos</div></div>
    <div class="kpi"><div class="val" style="color:#64748b;">{{ $distribucionEstado['inactivo'] ?? 0 }}</div><div class="lbl">Inactivos</div></div>
    <div class="kpi"><div class="val" style="color:#2563eb;">{{ $distribucionSexo['masculino'] ?? 0 }}</div><div class="lbl">Masculino</div></div>
    <div class="kpi"><div class="val" style="color:#7c3aed;">{{ $distribucionSexo['femenino'] ?? 0 }}</div><div class="lbl">Femenino</div></div>
</div>

<table class="reporte">
    <thead>
        <tr>
            <th>Código</th>
            <th>Paciente</th>
            <th>CI</th>
            <th class="text-center">Sexo</th>
            <th class="text-center">Total Citas</th>
            <th class="text-center">Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pacientes as $p)
        <tr>
            <td style="font-family:monospace;font-size:8px;color:#94a3b8;">{{ $p->codigo_paciente }}</td>
            <td><strong>{{ $p->apellidos }}, {{ $p->nombres }}</strong></td>
            <td>{{ $p->ci ?? '—' }}</td>
            <td class="text-center">{{ ucfirst($p->sexo ?? '—') }}</td>
            <td class="text-center"><strong>{{ $p->total_citas }}</strong></td>
            <td class="text-center">
                <span class="badge {{ $p->estado === 'activo' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($p->estado) }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center" style="color:#94a3b8; padding:12px;">Sin registros</td></tr>
        @endforelse
    </tbody>
</table>
<div class="footer">{{ config('app.name') }} · Generado el {{ $generadoEn }} · Total: {{ $pacientes->count() }} pacientes</div>
</body>
</html>
