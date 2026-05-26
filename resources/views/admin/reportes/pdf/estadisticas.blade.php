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
    th { background: #1d4ed8; color: white; padding: 5px 6px; text-align: center; font-size: 9px; }
    th:first-child, th:nth-child(2) { text-align: left; }
    td { padding: 5px 6px; border-bottom: 1px solid #e5e7eb; font-size: 9px; text-align: center; }
    td:first-child, td:nth-child(2) { text-align: left; }
    tr:nth-child(even) td { background: #f9fafb; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 3px; }
</style>
</head>
<body>
<div class="footer">Página <span class="pagenum"></span> | Sistema CUP — {{ now()->format('d/m/Y H:i') }}</div>
<div class="header">
    <h1>SISTEMA CUP — Universidad</h1>
    <h2>REPORTE 3: ESTADÍSTICAS POR MATERIA</h2>
    <div class="meta">Generado: {{ now()->format('d/m/Y H:i') }} | Total: {{ $registros->count() }} materias</div>
</div>
<table>
    <thead>
        <tr>
            <th>Materia</th><th>Gestión</th><th>Total Est.</th>
            <th>Promedio</th><th>Nota Máx</th><th>Nota Mín</th>
            <th>Aprobados</th><th>Reprobados</th>
        </tr>
    </thead>
    <tbody>
        @foreach($registros as $r)
        <tr>
            <td>{{ $r->materia }}</td>
            <td>{{ $r->gestion }}</td>
            <td>{{ $r->total_estudiantes }}</td>
            <td><strong>{{ $r->promedio }}</strong></td>
            <td>{{ $r->nota_max }}</td>
            <td>{{ $r->nota_min }}</td>
            <td>{{ $r->aprobados }}</td>
            <td>{{ $r->reprobados }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
