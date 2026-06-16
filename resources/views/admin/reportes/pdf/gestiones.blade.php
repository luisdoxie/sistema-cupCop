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
    .left { text-align: left; }
    td { padding: 5px 6px; border-bottom: 1px solid #e5e7eb; font-size: 9px; text-align: center; }
    .alt td { background: #f9fafb; }
    tfoot td { font-weight: bold; background: #f3f4f6; border-top: 2px solid #d1d5db; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 3px; }
</style>
</head>
<body>
<div class="footer">Página <span class="pagenum"></span> | Sistema CUP — {{ now()->format('d/m/Y H:i') }}</div>
<div class="header">
    <h1>SISTEMA CUP — Curso Pre-Universitarios</h1>
    <h2>REPORTE 6: COMPARATIVA ENTRE GESTIONES</h2>
    <div class="meta">
        Generado: {{ now()->format('d/m/Y H:i') }}
        | Período: {{ $filtros['desde'] }} — {{ $filtros['hasta'] }}
        | Total gestiones: {{ $registros->count() }}
    </div>
</div>
<table>
    <thead>
        <tr>
            <th class="left">Gestión</th><th>Año</th><th>Sem</th>
            <th>Postulantes</th><th>Admitidos</th><th>Reprobados</th>
            <th>Sin Cupo</th><th>% Admisión</th>
        </tr>
    </thead>
    <tbody>
        @foreach($registros as $r)
        <tr class="{{ $loop->even ? 'alt' : '' }}">
            <td class="left">{{ $r->gestion }}</td>
            <td>{{ $r->anio }}</td>
            <td>{{ $r->semestre }}</td>
            <td>{{ $r->postulantes }}</td>
            <td>{{ $r->admitidos }}</td>
            <td>{{ $r->reprobados }}</td>
            <td>{{ $r->sin_cupo }}</td>
            <td><strong>{{ $r->porcentaje_admision }}%</strong></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="left">TOTALES</td>
            <td>{{ $registros->sum('postulantes') }}</td>
            <td>{{ $registros->sum('admitidos') }}</td>
            <td>{{ $registros->sum('reprobados') }}</td>
            <td>{{ $registros->sum('sin_cupo') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
</body>
</html>
