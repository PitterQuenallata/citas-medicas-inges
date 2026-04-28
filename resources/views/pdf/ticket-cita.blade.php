<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket - {{ $cita->codigo_cita }}</title>
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
        .codigo-box {
            text-align: center;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px;
            margin-bottom: 12px;
        }
        .codigo-box .codigo {
            font-size: 16px;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: 1px;
        }
        .codigo-box .estado {
            font-size: 9px;
            color: #64748b;
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
        .pago-estado {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }
        .pago-pagado {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .pago-pendiente {
            background-color: #fef9c3;
            color: #ca8a04;
        }
        .pago-sin {
            background-color: #f1f5f9;
            color: #94a3b8;
        }
        .nota {
            text-align: center;
            margin-top: 14px;
            padding: 8px;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            font-size: 9px;
            color: #1d4ed8;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #94a3b8;
        }
        .linea-corte {
            border: none;
            border-top: 1px dashed #cbd5e1;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Clínica') }}</h1>
        <p>Sistema de Citas Médicas</p>
    </div>

    <div class="titulo-doc">Ticket de Cita</div>

    <div class="codigo-box">
        <div class="codigo">{{ $cita->codigo_cita }}</div>
        <div class="estado">Estado: {{ $cita->estado_label }}</div>
    </div>

    <div class="seccion">
        <div class="seccion-titulo">Datos de la Cita</div>
        <table class="datos">
            <tr>
                <td class="label">Fecha:</td>
                <td class="valor">{{ $cita->fecha_cita->translatedFormat('l d \d\e F, Y') }}</td>
            </tr>
            <tr>
                <td class="label">Horario:</td>
                <td class="valor">{{ substr($cita->hora_inicio, 0, 5) }} – {{ substr($cita->hora_fin, 0, 5) }}</td>
            </tr>
            @if($cita->motivo_consulta)
            <tr>
                <td class="label">Motivo:</td>
                <td class="valor">{{ $cita->motivo_consulta }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="seccion">
        <div class="seccion-titulo">Paciente</div>
        <table class="datos">
            <tr>
                <td class="label">Nombre:</td>
                <td class="valor">{{ $cita->paciente?->nombres }} {{ $cita->paciente?->apellidos }}</td>
            </tr>
            @if($cita->paciente?->ci)
            <tr>
                <td class="label">CI:</td>
                <td class="valor">{{ $cita->paciente->ci }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="seccion">
        <div class="seccion-titulo">Médico</div>
        <table class="datos">
            <tr>
                <td class="label">Doctor(a):</td>
                <td class="valor">Dr(a). {{ $cita->medico?->nombres }} {{ $cita->medico?->apellidos }}</td>
            </tr>
            @if($cita->medico?->especialidades->count())
            <tr>
                <td class="label">Especialidad:</td>
                <td class="valor">{{ $cita->medico->especialidades->pluck('nombre_especialidad')->join(', ') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <hr class="linea-corte">

    <div class="seccion" style="text-align: center;">
        @if($cita->pago && $cita->pago->estado_pago === 'pagado')
            <span class="pago-estado pago-pagado">PAGADO — Bs. {{ number_format($cita->pago->monto, 2) }}</span>
        @elseif($cita->pago && $cita->pago->estado_pago === 'pendiente')
            <span class="pago-estado pago-pendiente">PENDIENTE — Bs. {{ number_format($cita->pago->monto, 2) }}</span>
        @else
            <span class="pago-estado pago-sin">Sin pago registrado</span>
        @endif
    </div>

    <div class="nota">
        Por favor, presentarse 15 minutos antes de la hora de su cita.
    </div>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} — {{ config('app.name', 'Clínica') }}
    </div>
</body>
</html>
