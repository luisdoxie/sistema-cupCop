<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }
    .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #1d4ed8; padding-bottom: 8px; }
    .header h1 { margin: 0; font-size: 14px; color: #1d4ed8; }
    .header h2 { margin: 3px 0; font-size: 11px; }
    .meta { font-size: 9px; color: #666; margin-top: 4px; }
    .consulta { background: #f0f9ff; border-left: 3px solid #1d4ed8; padding: 5px 8px; margin-bottom: 10px; font-size: 9px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #1d4ed8; color: white; padding: 5px 4px; text-align: left; font-size: 9px; }
    td { padding: 4px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
    tr:nth-child(even) td { background: #f9fafb; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 3px; }
</style>
</head>
<body>
<div class="footer">Página <span class="pagenum"></span> | Sistema CUP - Generado: {{ now()->format('d/m/Y H:i') }}</div>
<div class="header">
    <h1>SISTEMA CUP — Curso Pre-Universitarios</h1>
    <h2>REPORTE POR VOZ CON IA</h2>
    <div class="meta">Generado: {{ now()->format('d/m/Y H:i') }} | Total: {{ count($data) }} registros</div>
</div>

<div class="consulta">
    <strong>Consulta:</strong> {{ $texto }}
</div>

@if(!empty($columnas))
<table>
    <thead>
        <tr>
            @foreach($columnas as $col)
            <th>{{ strtoupper(str_replace('_', ' ', $col)) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data as $fila)
        <tr>
            @foreach($columnas as $col)
            <td>{{ $fila[$col] ?? '—' }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p style="text-align:center; color:#666; margin-top: 30px;">Sin resultados.</p>
@endif
</body>
</html>
