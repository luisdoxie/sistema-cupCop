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
    th { background: #1d4ed8; color: white; padding: 5px 4px; text-align: center; font-size: 9px; }
    th:first-child, th:nth-child(2), th:nth-child(3) { text-align: left; }
    td { padding: 4px; border-bottom: 1px solid #e5e7eb; font-size: 9px; text-align: center; }
    td:first-child, td:nth-child(2), td:nth-child(3) { text-align: left; }
    tr:nth-child(even) td { background: #f9fafb; }
    .aprobado { background: #dcfce7; color: #166534; font-weight: bold; padding: 1px 4px; border-radius: 3px; }
    .reprobado { background: #fee2e2; color: #991b1b; font-weight: bold; padding: 1px 4px; border-radius: 3px; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 3px; }
</style>
</head>
<body>
<div class="footer">Página <span class="pagenum"></span> | Sistema CUP — {{ now()->format('d/m/Y H:i') }}</div>
<div class="header">
    <h1>SISTEMA CUP — Curso Pre-Universitarios</h1>
    <h2>REPORTE 2: POSTULANTES APROBADOS Y REPROBADOS</h2>
    <div class="meta">Generado: {{ now()->format('d/m/Y H:i') }} | Total: {{ $registros->count() }} registros</div>
</div>
<table>
    <thead>
        <tr>
            <th>CI</th><th>Estudiante</th><th>Materia</th><th>Gestión</th>
            <th>P1</th><th>P2</th><th>Final</th><th>Total</th><th>Resultado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($registros as $r)
        <tr>
            <td>{{ $r->ci }}</td>
            <td>{{ $r->estudiante }}</td>
            <td>{{ $r->materia }}</td>
            <td>{{ $r->gestion }}</td>
            <td>{{ $r->p1 ?? '—' }}</td>
            <td>{{ $r->p2 ?? '—' }}</td>
            <td>{{ $r->final_nota ?? '—' }}</td>
            <td><strong>{{ $r->total ?? '—' }}</strong></td>
            <td><span class="{{ $r->resultado === 'APROBADO' ? 'aprobado' : 'reprobado' }}">{{ $r->resultado }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
