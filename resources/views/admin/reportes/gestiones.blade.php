@extends('layouts.admin')
@section('title', 'Reporte: Comparativa de Gestiones')
@section('page-title', 'Reporte 6 — Comparativa entre Gestiones')

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Año desde</label>
                <input type="number" name="desde" value="{{ $desde }}" min="{{ $anioMin }}" max="{{ $anioMax }}"
                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Año hasta</label>
                <input type="number" name="hasta" value="{{ $hasta }}" min="{{ $anioMin }}" max="{{ $anioMax }}"
                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1.5 rounded">Filtrar</button>
                <a href="{{ route('admin.reportes.gestiones') }}" class="text-sm text-gray-500 hover:underline py-1.5">Limpiar</a>
            </div>
        </form>
    </div>

    {{-- Gráfico de líneas --}}
    @if($chartData->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Comparativa por Gestión</h3>
        <canvas id="chartGestiones" height="80"></canvas>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">{{ $registros->total() }} registros</span>
        <div class="flex gap-2">
            <a href="{{ route('admin.reportes.gestiones.pdf', request()->query()) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1.5 rounded">PDF</a>
            <a href="{{ route('admin.reportes.gestiones.excel', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded">Excel</a>
            <a href="{{ route('admin.reportes.gestiones.txt', request()->query()) }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-3 py-1.5 rounded">TXT</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(['Gestión','Año','Sem','Postulantes','Admitidos','Reprobados','Sin Cupo','% Admisión'] as $col)
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($registros as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium">{{ $r->gestion }}</td>
                    <td class="px-4 py-2 text-center">{{ $r->anio }}</td>
                    <td class="px-4 py-2 text-center">{{ $r->semestre }}</td>
                    <td class="px-4 py-2 text-center font-medium">{{ $r->postulantes }}</td>
                    <td class="px-4 py-2 text-center text-green-700 font-medium">{{ $r->admitidos }}</td>
                    <td class="px-4 py-2 text-center text-red-700">{{ $r->reprobados }}</td>
                    <td class="px-4 py-2 text-center text-gray-500">{{ $r->sin_cupo }}</td>
                    <td class="px-4 py-2 text-center font-bold {{ $r->porcentaje_admision >= 50 ? 'text-green-700' : 'text-red-700' }}">
                        {{ $r->porcentaje_admision }}%
                    </td>
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
new Chart(document.getElementById('chartGestiones'), {
    type: 'line',
    data: {
        labels: @json($chartData->map(fn($r) => $r->gestion)),
        datasets: [
            { label: 'Postulantes', data: @json($chartData->pluck('postulantes')), borderColor: 'rgb(59,130,246)', tension: 0.3 },
            { label: 'Admitidos',   data: @json($chartData->pluck('admitidos')),   borderColor: 'rgb(34,197,94)',  tension: 0.3 },
            { label: 'Reprobados',  data: @json($chartData->pluck('reprobados')),  borderColor: 'rgb(239,68,68)',  tension: 0.3 },
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});
</script>
@endif
@endpush
