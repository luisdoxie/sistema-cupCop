@extends('layouts.admin')
@section('title', 'Reporte: Estadísticas por Materia')
@section('page-title', 'Reporte 3 — Estadísticas por Materia')

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Gestión</label>
                <select name="id_gestion" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                    <option value="">Todas</option>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id }}" {{ request('id_gestion') == $g->id ? 'selected' : '' }}>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Materia</label>
                <select name="id_materia" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                    <option value="">Todas</option>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}" {{ request('id_materia') == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1.5 rounded">Filtrar</button>
                <a href="{{ route('admin.reportes.estadisticas') }}" class="text-sm text-gray-500 hover:underline py-1.5">Limpiar</a>
            </div>
        </form>
    </div>

    {{-- Gráfico --}}
    @if($chartData->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Aprobados vs Reprobados por Materia</h3>
        <canvas id="chartEstadisticas" height="100"></canvas>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">{{ $registros->total() }} registros</span>
        <div class="flex gap-2">
            <a href="{{ route('admin.reportes.estadisticas.pdf', request()->query()) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1.5 rounded">PDF</a>
            <a href="{{ route('admin.reportes.estadisticas.excel', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded">Excel</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(['Materia','Gestión','Total Est.','Promedio','Nota Máx','Nota Mín','Aprobados','Reprobados'] as $col)
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($registros as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium">{{ $r->materia }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $r->gestion }}</td>
                    <td class="px-4 py-2 text-center">{{ $r->total_estudiantes }}</td>
                    <td class="px-4 py-2 text-center font-medium">{{ $r->promedio }}</td>
                    <td class="px-4 py-2 text-center text-green-700">{{ number_format($r->nota_max, 2) }}</td>
                    <td class="px-4 py-2 text-center text-red-700">{{ number_format($r->nota_min, 2) }}</td>
                    <td class="px-4 py-2 text-center"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-medium">{{ $r->aprobados }}</span></td>
                    <td class="px-4 py-2 text-center"><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-xs font-medium">{{ $r->reprobados }}</span></td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No hay registros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $registros->links() }}</div>
</div>
@endsection

@push('scripts')
@if($chartData->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chartEstadisticas');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartData->pluck('materia')),
        datasets: [
            { label: 'Aprobados',  data: @json($chartData->pluck('aprobados')),  backgroundColor: 'rgba(34,197,94,0.7)' },
            { label: 'Reprobados', data: @json($chartData->pluck('reprobados')), backgroundColor: 'rgba(239,68,68,0.7)' },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});
</script>
@endif
@endpush
