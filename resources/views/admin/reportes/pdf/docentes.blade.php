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
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #1d4ed8; color: white; padding: 5px 6px; text-align: left; font-size: 9px; }
    td { padding: 5px 6px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
    .alt td { background: #f9fafb; }
    .bar-bg { background: #e5e7eb; border-radius: 3px; height: 8px; width: 80px; display: inline-block; vertical-align: middle; }
    .bar-fill-green { background: #22c55e; border-radius: 3px; height: 8px; display: block; }
    .bar-fill-red   { background: #ef4444; border-radius: 3px; height: 8px; display: block; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 3px; }
</style>
</head>
<body>
<div class="footer">Página <span class="pagenum"></span> | Sistema CUP — {{ now()->format('d/m/Y H:i') }}</div>
<div class="header">
    <h1>SISTEMA CUP — Curso Pre-Universitarios</h1>
    <h2>REPORTE 5: RENDIMIENTO POR DOCENTE</h2>
    <div class="meta">Generado: {{ now()->format('d/m/Y H:i') }} | Total: {{ $registros->count() }} registros</div>
</div>
<table>
    <thead>
        <tr>
            <th>Docente</th><th>Materia</th><th>Gestión</th>
            <th>Total Est.</th><th>Aprobados</th><th>% Aprobación</th>
        </tr>
    </thead>
    <tbody>
        @foreach($registros as $r)
        <tr class="{{ $loop->even ? 'alt' : '' }}">
            <td><strong>{{ $r->docente }}</strong></td>
            <td>{{ $r->materia }}</td>
            <td>{{ $r->gestion }}</td>
            <td>{{ $r->total_estudiantes }}</td>
            <td>{{ $r->aprobados }}</td>
            <td>
                <span class="bar-bg"><span class="{{ $r->porcentaje_aprobacion >= 60 ? 'bar-fill-green' : 'bar-fill-red' }}"
                    style="width: {{ min($r->porcentaje_aprobacion, 100) }}%"></span></span>
                {{ $r->porcentaje_aprobacion }}%
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
