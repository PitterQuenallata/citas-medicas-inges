<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Log de Auditoría</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9px; color: #334155; padding: 16px; }
    .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 8px; margin-bottom: 10px; }
    .header h1 { font-size: 15px; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; }
    .header p { font-size: 8px; color: #94a3b8; }
    .titulo-doc { text-align: center; font-size: 12px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px; }
    .filtros { font-size: 8px; color: #64748b; text-align: center; margin-bottom: 10px; }
    table.reporte { width: 100%; border-collapse: collapse; }
    table.reporte th { background: #f1f5f9; padding: 4px 5px; font-size: 7px; font-weight: 700; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; text-align: left; }
    table.reporte td { padding: 3px 5px; font-size: 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
    table.reporte tr:nth-child(even) td { background: #f8fafc; }
    .badge { display: inline-block; padding: 1px 5px; border-radius: 20px; font-size: 6.5px; font-weight: 700; }
    .badge-crear { background: #dcfce7; color: #16a34a; }
    .badge-editar { background: #dbeafe; color: #2563eb; }
    .badge-eliminar { background: #fee2e2; color: #dc2626; }
    .badge-default { background: #f1f5f9; color: #64748b; }
    .footer { text-align: center; margin-top: 12px; padding-top: 6px; border-top: 1px solid #e2e8f0; font-size: 7px; color: #94a3b8; }
    .mono { font-family: monospace; color: #64748b; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ config('app.name', 'Clínica Médicos Cristianos Solidarios') }}</h1>
    <p>Sistema de Citas Médicas</p>
</div>
<div class="titulo-doc">Registro de Auditoría</div>
<div class="filtros">
    @if(!empty($filtros['fecha_desde'])) Desde: {{ $filtros['fecha_desde'] }} @endif
    @if(!empty($filtros['fecha_hasta'])) · Hasta: {{ $filtros['fecha_hasta'] }} @endif
    @if(!empty($filtros['accion'])) · Acción: {{ ucfirst($filtros['accion']) }} @endif
    @if(!empty($filtros['tabla'])) · Tabla: {{ $filtros['tabla'] }} @endif
    · Generado: {{ $generadoEn }} · Total: {{ $registros->count() }} registros
</div>

<table class="reporte">
    <thead>
        <tr>
            <th>Fecha/Hora</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>Tabla</th>
            <th>Reg. ID</th>
            <th>IP</th>
        </tr>
    </thead>
    <tbody>
        @forelse($registros as $r)
        @php
            $bc = match($r->accion) {
                'crear'    => 'badge-crear',
                'editar'   => 'badge-editar',
                'eliminar' => 'badge-eliminar',
                default    => 'badge-default',
            };
        @endphp
        <tr>
            <td>
                <strong>{{ $r->created_at->format('d/m/Y') }}</strong><br>
                <span style="color:#94a3b8;">{{ $r->created_at->format('H:i:s') }}</span>
            </td>
            <td>
                @if($r->usuario)
                    {{ $r->usuario->nombre }} {{ $r->usuario->apellido }}<br>
                    <span style="color:#94a3b8;font-size:7px;">{{ $r->usuario->email }}</span>
                @else
                    <span style="color:#94a3b8;">Sistema</span>
                @endif
            </td>
            <td><span class="badge {{ $bc }}">{{ ucfirst($r->accion) }}</span></td>
            <td class="mono">{{ $r->tabla }}</td>
            <td class="mono" style="text-align:center;">{{ $r->registro_id ?? '—' }}</td>
            <td class="mono">{{ $r->ip ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:12px;">Sin registros de auditoría</td></tr>
        @endforelse
    </tbody>
</table>
<div class="footer">{{ config('app.name') }} · Log de Auditoría · Generado el {{ $generadoEn }}</div>
</body>
</html>
