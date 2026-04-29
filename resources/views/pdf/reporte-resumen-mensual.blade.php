<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Resumen Mensual</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #334155; padding: 16px; }
    .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 8px; margin-bottom: 10px; }
    .header h1 { font-size: 15px; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; }
    .header p { font-size: 8px; color: #94a3b8; }
    .titulo-doc { text-align: center; font-size: 12px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px; }
    .subtitulo { text-align: center; font-size: 10px; color: #64748b; margin-bottom: 10px; }
    .kpis { display: table; width: 100%; margin-bottom: 12px; border: 1px solid #e2e8f0; }
    .kpi { display: table-cell; text-align: center; padding: 8px 4px; border-right: 1px solid #e2e8f0; }
    .kpi:last-child { border-right: none; }
    .kpi .val { font-size: 16px; font-weight: 700; }
    .kpi .lbl { font-size: 7px; color: #94a3b8; margin-top: 2px; }
    .seccion-titulo { font-size: 9px; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; margin-bottom: 6px; margin-top: 10px; }
    table.lista { width: 100%; border-collapse: collapse; }
    table.lista td { padding: 3px 4px; font-size: 9px; border-bottom: 1px solid #f1f5f9; }
    table.lista td.label { color: #64748b; width: 40%; }
    table.lista td.valor { font-weight: 600; }
    .barra-container { width: 100%; background: #f1f5f9; height: 6px; border-radius: 3px; overflow: hidden; margin-top: 2px; }
    .barra { height: 6px; border-radius: 3px; }
    .footer { text-align: center; margin-top: 14px; padding-top: 6px; border-top: 1px solid #e2e8f0; font-size: 7px; color: #94a3b8; }
    .two-col { display: table; width: 100%; }
    .col { display: table-cell; width: 50%; padding-right: 8px; vertical-align: top; }
    .col:last-child { padding-right: 0; padding-left: 8px; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ config('app.name', 'Clínica Médicos Cristianos Solidarios') }}</h1>
    <p>Sistema de Citas Médicas</p>
</div>
<div class="titulo-doc">Resumen Mensual Ejecutivo</div>
<div class="subtitulo">{{ $meses[$mes] }} {{ $anio }} · Generado: {{ $generadoEn }}</div>

<div class="kpis">
    <div class="kpi"><div class="val" style="color:#4f46e5;">{{ $totalCitas }}</div><div class="lbl">Total Citas</div></div>
    <div class="kpi"><div class="val" style="color:#16a34a;">{{ $citasAtendidas }}</div><div class="lbl">Atendidas</div></div>
    <div class="kpi"><div class="val" style="color:#dc2626;">{{ $citasCanceladas }}</div><div class="lbl">Cancel./NA</div></div>
    <div class="kpi"><div class="val" style="color:#2563eb;">{{ $pacientesNuevos }}</div><div class="lbl">Pacientes nuevos</div></div>
    <div class="kpi"><div class="val" style="color:#ca8a04;">Bs.{{ number_format($ingresosMes,0) }}</div><div class="lbl">Ingresos</div></div>
    <div class="kpi"><div class="val" style="color: {{ $tasaAsistencia >= 70 ? '#16a34a' : ($tasaAsistencia >= 40 ? '#ca8a04' : '#dc2626') }};">{{ $tasaAsistencia }}%</div><div class="lbl">Tasa asistencia</div></div>
</div>

<div class="two-col">
    <div class="col">
        <div class="seccion-titulo">Distribución de estados</div>
        @php $totalBarras = $citasPorEstado->sum() ?: 1; @endphp
        @foreach(['atendida' => ['Atendidas','#16a34a'], 'confirmada' => ['Confirmadas','#2563eb'], 'pendiente' => ['Pendientes','#ca8a04'], 'cancelada' => ['Canceladas','#dc2626'], 'no_asistio' => ['No Asistió','#ef4444']] as $estado => $cfg)
        @if(($citasPorEstado[$estado] ?? 0) > 0)
        @php $pct = round(($citasPorEstado[$estado] / $totalBarras) * 100); @endphp
        <div style="margin-bottom:5px;">
            <table style="width:100%;"><tr>
                <td style="font-size:8px;color:#334155;">{{ $cfg[0] }}</td>
                <td style="text-align:right;font-size:8px;font-weight:600;color:{{ $cfg[1] }};">{{ $citasPorEstado[$estado] }} ({{ $pct }}%)</td>
            </tr></table>
            <div class="barra-container"><div class="barra" style="width:{{ $pct }}%;background:{{ $cfg[1] }};"></div></div>
        </div>
        @endif
        @endforeach
    </div>

    <div class="col">
        <div class="seccion-titulo">Top 5 médicos</div>
        @php $maxCitas = $topMedicos->first()?->total_citas ?: 1; @endphp
        @foreach($topMedicos as $i => $tm)
        @php $pct = round(($tm->total_citas / $maxCitas) * 100); @endphp
        <div style="margin-bottom:5px;">
            <table style="width:100%;"><tr>
                <td style="font-size:8px;color:#334155;">{{ $i+1 }}. Dr. {{ $tm->medico?->apellidos }}</td>
                <td style="text-align:right;font-size:8px;font-weight:600;color:#4f46e5;">{{ $tm->total_citas }}</td>
            </tr></table>
            <div class="barra-container"><div class="barra" style="width:{{ $pct }}%;background:#4f46e5;"></div></div>
        </div>
        @endforeach

        @if($ingresosPorMetodo->count())
        <div class="seccion-titulo" style="margin-top:8px;">Ingresos por método</div>
        @foreach($ingresosPorMetodo as $metodo => $monto)
        <table style="width:100%;margin-bottom:2px;"><tr>
            <td style="font-size:8px;color:#64748b;">{{ ucfirst($metodo) }}</td>
            <td style="text-align:right;font-size:8px;font-weight:600;color:#16a34a;">Bs. {{ number_format($monto,2) }}</td>
        </tr></table>
        @endforeach
        <table style="width:100%;border-top:1px solid #e2e8f0;margin-top:3px;"><tr>
            <td style="font-size:8px;font-weight:700;color:#334155;">TOTAL</td>
            <td style="text-align:right;font-size:8px;font-weight:700;color:#16a34a;">Bs. {{ number_format($ingresosPorMetodo->sum(),2) }}</td>
        </tr></table>
        @endif
    </div>
</div>

@if($medicoTop && $medicoTop->medico)
<div style="margin-top:10px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:4px;padding:6px 10px;">
    <span style="font-size:8px;font-weight:700;text-transform:uppercase;color:#0369a1;letter-spacing:1px;">⭐ Médico del mes</span>
    <p style="margin-top:3px;font-size:10px;font-weight:600;color:#1e293b;">Dr. {{ $medicoTop->medico->apellidos }}, {{ $medicoTop->medico->nombres }}</p>
    <p style="font-size:8px;color:#64748b;">{{ $medicoTop->total }} citas atendidas en {{ $meses[$mes] }} {{ $anio }}</p>
</div>
@endif

<div class="footer">{{ config('app.name') }} · Resumen mensual {{ $meses[$mes] }} {{ $anio }} · Generado el {{ $generadoEn }}</div>
</body>
</html>
