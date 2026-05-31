<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Admitidos CUP</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            color: #1e40af;
            margin: 0 0 4px 0;
        }
        .header h2 {
            font-size: 14px;
            color: #374151;
            margin: 0 0 4px 0;
        }
        .header p {
            font-size: 10px;
            color: #6b7280;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead th {
            background-color: #1e40af;
            color: #ffffff;
            padding: 7px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .badge-c1 {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-c2 {
            background-color: #ccfbf1;
            color: #134e4a;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
        }
        .promedio-alto { color: #059669; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Curso Pre-Universitarios (CUP)</h1>
        <h2>Lista Oficial de Estudiantes Admitidos</h2>
        @if($gestion)
            <p>Gestion: {{ $gestion->nombre }} | Fecha de generacion: {{ now()->format('d/m/Y H:i') }}</p>
        @else
            <p>Fecha de generacion: {{ now()->format('d/m/Y H:i') }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th style="width:70px;">CI</th>
                <th>Nombre Completo</th>
                <th>Carrera Asignada</th>
                <th style="width:60px;">Promedio</th>
                <th style="width:60px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admisiones as $index => $admision)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $admision->estudiante->persona->ci ?? '—' }}</td>
                <td>
                    {{ $admision->estudiante->persona->nombre ?? '' }}
                    {{ $admision->estudiante->persona->apellido ?? '' }}
                </td>
                <td>
                    @if($admision->estado === 'admitido_carrera1')
                        {{ $admision->carrera1->nombre ?? '—' }}
                        <span class="badge-c1">1ra opción</span>
                    @elseif($admision->estado === 'admitido_carrera2')
                        {{ $admision->carrera2->nombre ?? '—' }}
                        <span class="badge-c2">2da opción</span>
                    @else
                        —
                    @endif
                </td>
                <td class="promedio-alto">
                    {{ $admision->promedio_final !== null ? number_format($admision->promedio_final, 2) : '—' }}
                </td>
                <td>
                    @if($admision->estado === 'admitido_carrera1')
                        <span class="badge-c1">ADMITIDO</span>
                    @elseif($admision->estado === 'admitido_carrera2')
                        <span class="badge-c2">ADMITIDO</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; color:#6b7280; padding:20px;">
                    No hay estudiantes admitidos registrados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total admitidos: {{ $admisiones->count() }} |
           Carrera 1: {{ $admisiones->where('estado','admitido_carrera1')->count() }} |
           Carrera 2: {{ $admisiones->where('estado','admitido_carrera2')->count() }}</p>
        <p>Documento generado automaticamente por el Sistema CUP — {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
