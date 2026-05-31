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
    tr:nth-child(even) td { background: #f9fafb; }
    .badge-green { background: #dcfce7; color: #166534; padding: 1px 4px; border-radius: 3px; }
    .badge-red   { background: #fee2e2; color: #991b1b; padding: 1px 4px; border-radius: 3px; }
    .badge-gray  { background: #f3f4f6; color: #374151; padding: 1px 4px; border-radius: 3px; }
    .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 3px; }
    .totals { margin-top: 8px; font-size: 9px; background: #f3f4f6; padding: 5px; border-radius: 3px; }
</style>
</head>
<body>
<div class="footer">Página <span class="pagenum"></span> | Sistema CUP - Generado: {{ now()->format('d/m/Y H:i') }}</div>
<div class="header">
    <h1>SISTEMA CUP — Curso Pre-Universitarios</h1>
    <h2>REPORTE 1: LISTA GENERAL DE POSTULANTES</h2>
    <div class="meta">
        Generado: {{ now()->format('d/m/Y H:i') }}
        @if(!empty(array_filter($filtros)))
        | Filtros:
        @if(!empty($filtros['id_gestion'])) Gestión aplicada @endif
        @if(!empty($filtros['estado'])) | Estado: {{ $filtros['estado'] }} @endif
        @if(!empty($filtros['ciudad'])) | Ciudad: {{ $filtros['ciudad'] }} @endif
        @if(!empty($filtros['colegio'])) | Colegio: {{ $filtros['colegio'] }} @endif
        @endif
        | Total: {{ $registros->count() }} registros
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>CI</th><th>Nombre</th><th>Apellido</th><th>Correo</th>
            <th>Colegio</th><th>Ciudad</th><th>Carrera 1</th><th>Carrera 2</th>
            <th>Estado</th><th>Carrera Asignada</th>
        </tr>
    </thead>
    <tbody>
        @foreach($registros as $r)
        <tr>
            <td>{{ $r->ci }}</td>
            <td>{{ $r->nombre }}</td>
            <td>{{ $r->apellido }}</td>
            <td>{{ $r->correo }}</td>
            <td>{{ $r->colegio }}</td>
            <td>{{ $r->ciudad }}</td>
            <td>{{ $r->sigla_carrera1 }}</td>
            <td>{{ $r->sigla_carrera2 }}</td>
            <td>
                @if(in_array($r->estado, ['admitido_carrera1','admitido_carrera2']))
                    <span class="badge-green">{{ $r->estado }}</span>
                @elseif($r->estado === 'reprobado')
                    <span class="badge-red">{{ $r->estado }}</span>
                @else
                    <span class="badge-gray">{{ $r->estado }}</span>
                @endif
            </td>
            <td>{{ $r->carrera_asignada ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <strong>TOTALES:</strong>
    Total: {{ $registros->count() }} |
    Admitidos: {{ $registros->whereIn('estado', ['admitido_carrera1','admitido_carrera2'])->count() }} |
    Reprobados: {{ $registros->where('estado','reprobado')->count() }} |
    En proceso: {{ $registros->whereNotIn('estado', ['admitido_carrera1','admitido_carrera2','reprobado','no_admitido'])->count() }}
</div>
</body>
</html>
