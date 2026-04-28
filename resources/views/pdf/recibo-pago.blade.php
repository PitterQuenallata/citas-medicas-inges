<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo - {{ $pago->codigo_pago }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #334155;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .titulo-doc {
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }
        .monto-box {
            text-align: center;
            background-color: #dcfce7;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 12px;
        }
        .monto-box .monto {
            font-size: 20px;
            font-weight: 700;
            color: #16a34a;
        }
        .monto-box .codigo {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .monto-box .metodo {
            font-size: 9px;
            color: #16a34a;
            font-weight: 600;
            margin-top: 2px;
        }
        .seccion {
            margin-bottom: 10px;
        }
        .seccion-titulo {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 1px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            margin-bottom: 6px;
        }
        table.datos {
            width: 100%;
            border-collapse: collapse;
        }
        table.datos td {
            padding: 3px 0;
            vertical-align: top;
        }
        table.datos td.label {
            color: #64748b;
            width: 35%;
            font-size: 10px;
        }
        table.datos td.valor {
            font-weight: 600;
            color: #1e293b;
            font-size: 10px;
        }
        .detalle-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .detalle-tabla th {
            background-color: #f1f5f9;
            padding: 5px 8px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .detalle-tabla td {
            padding: 5px 8px;
            font-size: 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .detalle-tabla .text-right {
            text-align: right;
        }
        .detalle-tabla .total-row td {
            font-weight: 700;
            font-size: 11px;
            border-top: 2px solid #e2e8f0;
            color: #1e293b;
        }
        .linea-corte {
            border: none;
            border-top: 1px dashed #cbd5e1;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #94a3b8;
        }
        .sello {
            text-align: center;
            margin-top: 12px;
            font-size: 10px;
            font-weight: 700;
            color: #16a34a;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Clínica') }}</h1>
        <p>Sistema de Citas Médicas</p>
    </div>

    <div class="titulo-doc">Recibo de Pago</div>

    <div class="monto-box">
        <div class="monto">Bs. {{ number_format($pago->monto, 2) }}</div>
        <div class="codigo">{{ $pago->codigo_pago }}</div>
        <div class="metodo">{{ $pago->metodo_label }}</div>
    </div>

    <div class="seccion">
        <div class="seccion-titulo">Datos del Pago</div>
        <table class="datos">
            <tr>
                <td class="label">Código pago:</td>
                <td class="valor">{{ $pago->codigo_pago }}</td>
            </tr>
            <tr>
                <td class="label">Fecha de pago:</td>
                <td class="valor">{{ $pago->fecha_pago?->format('d/m/Y H:i') ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Método:</td>
                <td class="valor">{{ $pago->metodo_label }}</td>
            </tr>
            @if($pago->referencia_externa)
            <tr>
                <td class="label">Referencia:</td>
                <td class="valor">{{ $pago->referencia_externa }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Registrado por:</td>
                <td class="valor">{{ $pago->usuarioRegistra?->nombre }} {{ $pago->usuarioRegistra?->apellido }}</td>
            </tr>
        </table>
    </div>

    <hr class="linea-corte">

    <div class="seccion">
        <div class="seccion-titulo">Cita Asociada</div>
        <table class="datos">
            <tr>
                <td class="label">Código cita:</td>
                <td class="valor">{{ $pago->cita->codigo_cita }}</td>
            </tr>
            <tr>
                <td class="label">Fecha cita:</td>
                <td class="valor">{{ $pago->cita->fecha_cita->translatedFormat('l d \d\e F, Y') }}</td>
            </tr>
            <tr>
                <td class="label">Horario:</td>
                <td class="valor">{{ substr($pago->cita->hora_inicio, 0, 5) }} – {{ substr($pago->cita->hora_fin, 0, 5) }}</td>
            </tr>
        </table>
    </div>

    <div class="seccion">
        <div class="seccion-titulo">Paciente</div>
        <table class="datos">
            <tr>
                <td class="label">Nombre:</td>
                <td class="valor">{{ $pago->cita->paciente?->nombres }} {{ $pago->cita->paciente?->apellidos }}</td>
            </tr>
            @if($pago->cita->paciente?->ci)
            <tr>
                <td class="label">CI:</td>
                <td class="valor">{{ $pago->cita->paciente->ci }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="seccion">
        <div class="seccion-titulo">Médico</div>
        <table class="datos">
            <tr>
                <td class="label">Doctor(a):</td>
                <td class="valor">Dr(a). {{ $pago->cita->medico?->nombres }} {{ $pago->cita->medico?->apellidos }}</td>
            </tr>
            @if($pago->cita->medico?->especialidades->count())
            <tr>
                <td class="label">Especialidad:</td>
                <td class="valor">{{ $pago->cita->medico->especialidades->pluck('nombre_especialidad')->join(', ') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <hr class="linea-corte">

    <table class="detalle-tabla">
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Consulta médica — {{ $pago->cita->codigo_cita }}</td>
                <td class="text-right">Bs. {{ number_format($pago->monto, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL</td>
                <td class="text-right">Bs. {{ number_format($pago->monto, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="sello">PAGADO</div>

    @if($pago->observaciones)
    <div style="margin-top: 8px; font-size: 9px; color: #64748b;">
        <strong>Observaciones:</strong> {{ $pago->observaciones }}
    </div>
    @endif

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} — {{ config('app.name', 'Clínica') }}
    </div>
</body>
</html>
