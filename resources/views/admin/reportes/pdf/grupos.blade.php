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
    th:first-child { text-align: left; }
    td { padding: 5px 6px; border-bottom: 1px solid #e5e7eb; font-size: 9px; text-align: center; }
    td:first-child { text-align: left; }
    tr:nth-child(even) td { background: #f9fafb; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 3px; }
    .info { background: #eff6ff; border: 1px solid #bfdbfe; padding: 5px 8px; border-radius: 3px; margin-bottom: 8px; font-size: 9px; }
</style>
</head>
<body>
<div class="footer">Página <span class="pagenum"></span> | Sistema CUP — {{ now()->format('d/m/Y H:i') }}</div>
<div class="header">
    <h1>SISTEMA CUP — Curso Pre-Universitarios</h1>
    <h2>REPORTE 4: GRUPOS HABILITADOS</h2>
    <div class="meta">Generado: {{ now()->format('d/m/Y H:i') }} | Divisor configurado: {{ $divisor }}</div>
</div>
<div class="info">Fórmula grupos necesarios: CEIL(estudiantes_asignados / {{ $divisor }})</div>
<table>
    <thead>
        <tr>
            <th>Gestión</th><th>Año</th><th>Total Grupos</th>
            <th>Capacidad Total</th><th>Est. Asignados</th>
            <th>Docentes</th><th>Grupos Necesarios</th>
        </tr>
    </thead>
    <tbody>
        @foreach($registros as $r)
        @php $necesarios = ceil($r->estudiantes_asignados / $divisor) @endphp
        <tr>
            <td>{{ $r->gestion }}</td>
            <td>{{ $r->anio }}</td>
            <td>{{ $r->total_grupos }}</td>
            <td>{{ $r->capacidad_total }}</td>
            <td><strong>{{ $r->estudiantes_asignados }}</strong></td>
            <td>{{ $r->total_docentes }}</td>
            <td>{{ $necesarios }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
