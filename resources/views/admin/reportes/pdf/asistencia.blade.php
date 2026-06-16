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
    th { background: #1d4ed8; color: white; padding: 5px 4px; text-align: left; font-size: 9px; }
    td { padding: 4px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
    .alt td { background: #f9fafb; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 3px; }
</style>
</head>
<body>
<div class="footer">Página <span class="pagenum"></span> | Sistema CUP — {{ now()->format('d/m/Y H:i') }}</div>
<div class="header">
    <h1>SISTEMA CUP — Curso Pre-Universitarios</h1>
    <h2>REPORTE DE ASISTENCIA</h2>
    <div class="meta">
        Generado: {{ now()->format('d/m/Y H:i') }}
        @if(!empty($filtros['fecha_desde'])) | Desde: {{ $filtros['fecha_desde'] }} @endif
        @if(!empty($filtros['fecha_hasta'])) | Hasta: {{ $filtros['fecha_hasta'] }} @endif
        | Total: {{ $registros->count() }} registros
    </div>
</div>
<table>
    <thead>
        <tr>
            <th>CI</th><th>Estudiante</th><th>Materia</th><th>Grupo</th>
            <th>Total</th><th>Presentes</th><th>Ausentes</th><th>Justif.</th><th>%</th>
        </tr>
    </thead>
    <tbody>
        @foreach($registros as $r)
        <tr class="{{ $loop->even ? 'alt' : '' }}">
            <td>{{ $r->ci }}</td>
            <td>{{ $r->estudiante }}</td>
            <td>{{ $r->materia }}</td>
            <td>{{ $r->grupo }}</td>
            <td>{{ $r->total_clases }}</td>
            <td>{{ $r->presentes }}</td>
            <td>{{ $r->ausentes }}</td>
            <td>{{ $r->justificados }}</td>
            <td><strong>{{ $r->porcentaje }}%</strong></td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
