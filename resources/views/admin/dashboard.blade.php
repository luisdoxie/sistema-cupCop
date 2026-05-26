@extends('layouts.admin')

@section('title', 'Dashboard - Administrador')
@section('page-title', 'Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
{{-- Gestion activa --}}
@if($gestionActiva)
<div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <span class="text-blue-800 font-medium">Gestión activa: {{ $gestionActiva->nombre }}</span>
    <span class="text-blue-600 text-sm">{{ \Carbon\Carbon::parse($gestionActiva->fecha_inicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($gestionActiva->fecha_fin)->format('d/m/Y') }}</span>
</div>
@else
<div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <span class="text-yellow-800">No hay gestión activa en este momento.</span>
</div>
@endif

{{-- Stats cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500 mb-1">Total Postulantes</p>
        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_postulantes'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <p class="text-sm text-gray-500 mb-1">Admitidos</p>
        <p class="text-3xl font-bold text-gray-800">{{ $stats['admitidos'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
        <p class="text-sm text-gray-500 mb-1">Reprobados</p>
        <p class="text-3xl font-bold text-gray-800">{{ $stats['reprobados'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500 mb-1">Grupos Habilitados</p>
        <p class="text-3xl font-bold text-gray-800">{{ $stats['grupos_habilitados'] }}</p>
    </div>
</div>

{{-- Chart --}}
@if($gestionActiva && array_sum($chartData) > 0)
<div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Distribución de Admisiones</h2>
    <div class="max-w-md mx-auto">
        <canvas id="admisionChart"></canvas>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
@if($gestionActiva && array_sum($chartData) > 0)
const ctx = document.getElementById('admisionChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Inscritos', 'Cursando', 'Admitidos', 'Reprobados', 'No Admitidos'],
        datasets: [{
            data: [
                {{ $chartData['inscrito'] }},
                {{ $chartData['cursando'] }},
                {{ $chartData['admitidos'] }},
                {{ $chartData['reprobados'] }},
                {{ $chartData['no_admitido'] }}
            ],
            backgroundColor: ['#3B82F6','#F59E0B','#10B981','#EF4444','#6B7280'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
@endif
</script>
@endpush
