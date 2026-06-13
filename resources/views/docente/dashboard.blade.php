@extends('layouts.docente')
@section('title', 'Dashboard - Docente')
@section('page-title', 'Mi Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Tarjetas de estadísticas --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Estudiantes</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $stats['total_estudiantes'] }}</p>
            <p class="text-xs text-gray-400 mt-1">a tu cargo</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Clases Realizadas</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['clases_realizadas'] }}</p>
            <p class="text-xs text-gray-400 mt-1">de {{ $stats['total_clases'] }} totales</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Pendientes</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['clases_pendientes'] }}</p>
            <p class="text-xs text-gray-400 mt-1">clases programadas</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Asistencia</p>
            <p class="text-2xl font-bold text-purple-700 mt-1">{{ $stats['porcentaje_asistencia'] }}%</p>
            <p class="text-xs text-gray-400 mt-1">promedio general</p>
        </div>
    </div>

    {{-- Alerta clases sin asistencia --}}
    @if($sinAsistencia->isNotEmpty())
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h2 class="text-sm font-semibold text-orange-700">
                Clases sin asistencia registrada
                <span class="bg-orange-200 text-orange-800 text-xs px-2 py-0.5 rounded-full ml-1">{{ $sinAsistencia->count() }}</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-orange-600 text-xs">
                        <th class="pb-2 pr-4">Fecha</th>
                        <th class="pb-2 pr-4">Materia</th>
                        <th class="pb-2 pr-4 hidden sm:table-cell">Grupo</th>
                        <th class="pb-2">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-100">
                    @foreach($sinAsistencia as $clase)
                    <tr>
                        <td class="py-2 pr-4 text-gray-700">{{ \Carbon\Carbon::parse($clase->fecha)->format('d/m/Y') }}</td>
                        <td class="py-2 pr-4 text-gray-700">{{ $clase->asignacion->materiaGrupo->materia->nombre ?? '-' }}</td>
                        <td class="py-2 pr-4 text-gray-500 hidden sm:table-cell">{{ $clase->asignacion->materiaGrupo->grupo->nombre ?? '-' }}</td>
                        <td class="py-2">
                            <a href="{{ route('docente.asistencia.pase-lista', $clase) }}"
                               class="text-orange-600 hover:text-orange-800 text-xs font-medium underline whitespace-nowrap">
                                Registrar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Clases de hoy --}}
    @if($clasesHoy->isNotEmpty())
    <div>
        <h2 class="text-base font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Clases de Hoy
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($clasesHoy as $clase)
            <div class="bg-white rounded-lg shadow p-4 border-l-4 {{ $clase->estado === 'realizada' ? 'border-green-500' : ($clase->estado === 'cancelada' ? 'border-red-400' : 'border-blue-500') }}">
                <div class="flex items-start justify-between">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">{{ $clase->asignacion->materiaGrupo->materia->nombre ?? '-' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $clase->asignacion->materiaGrupo->grupo->nombre ?? '-' }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $clase->bloque->hora_inicio ?? '' }} - {{ $clase->bloque->hora_fin ?? '' }}
                            @if($clase->aula) · Aula {{ $clase->aula->numero }} (P{{ $clase->aula->piso }}) @endif
                        </p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0 ml-2
                        {{ $clase->estado === 'realizada' ? 'bg-green-100 text-green-700' : ($clase->estado === 'cancelada' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ ucfirst($clase->estado) }}
                    </span>
                </div>
                @if($clase->estado === 'programada')
                <a href="{{ route('docente.asistencia.pase-lista', $clase) }}"
                   class="mt-3 block text-center bg-green-600 hover:bg-green-700 text-white text-xs py-1.5 rounded transition-colors">
                    Pasar Lista
                </a>
                @elseif($clase->estado === 'realizada')
                <a href="{{ route('docente.asistencia.pase-lista', $clase) }}"
                   class="mt-3 block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs py-1.5 rounded transition-colors">
                    Ver Asistencia
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Gráficos --}}
    @if(!empty($asistenciaPorGrupo) || $stats['total_clases'] > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Gráfico asistencia por grupo --}}
        @if(!empty($asistenciaPorGrupo))
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Asistencia por Materia/Grupo</h2>
            <div class="relative" style="height:220px">
                <canvas id="chartAsistencia"></canvas>
            </div>
        </div>
        @endif

        {{-- Gráfico estado de clases --}}
        @if($stats['total_clases'] > 0)
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Estado de Clases</h2>
            <div class="flex items-center justify-center" style="height:220px">
                <canvas id="chartEstado"></canvas>
            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- Accesos rápidos --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <a href="{{ route('docente.clases.index') }}"
           class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500 hover:shadow-md transition-shadow flex items-center gap-4">
            <div class="bg-blue-100 rounded-full p-3 flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-gray-800 text-sm">Mis Clases</p>
                <p class="text-xs text-gray-500">Calendario y asistencia</p>
            </div>
        </a>
        <a href="{{ route('docente.notas.index') }}"
           class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500 hover:shadow-md transition-shadow flex items-center gap-4">
            <div class="bg-green-100 rounded-full p-3 flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-gray-800 text-sm">Notas</p>
                <p class="text-xs text-gray-500">Planilla de calificaciones</p>
            </div>
        </a>
    </div>

    {{-- Mis asignaciones --}}
    <div>
        <h2 class="text-base font-semibold text-gray-700 mb-3">Mis Asignaciones</h2>
        @if($misAsignaciones->isEmpty())
        <div class="bg-white rounded-lg shadow p-6 text-center text-gray-400 text-sm">
            No tienes asignaciones activas.
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($misAsignaciones as $asig)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">{{ $asig->materiaGrupo->materia->nombre }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Grupo: {{ $asig->materiaGrupo->grupo->nombre }}</p>
                        <p class="text-xs text-blue-500">{{ $asig->materiaGrupo->grupo->gestion->nombre ?? '' }}</p>
                        <div class="mt-1 space-y-0.5">
                            @foreach($asig->bloquesHorario as $b)
                            <p class="text-xs text-gray-400">{{ ucfirst($b->dia) }} {{ \Carbon\Carbon::parse($b->hora_inicio)->format('H:i') }}-{{ \Carbon\Carbon::parse($b->hora_fin)->format('H:i') }}</p>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 flex-shrink-0">
                        <a href="{{ route('docente.notas.planilla', $asig->materiaGrupo->grupo) }}"
                           class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded text-center whitespace-nowrap">
                            Notas
                        </a>
                        <a href="{{ route('docente.clases.index') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded text-center whitespace-nowrap">
                            Clases
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Próximas clases --}}
    @if($proximasClases->isNotEmpty())
    <div>
        <h2 class="text-base font-semibold text-gray-700 mb-3">Próximas Clases</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha</th>
                            <th class="px-4 py-3 text-left">Materia</th>
                            <th class="px-4 py-3 text-left hidden sm:table-cell">Grupo</th>
                            <th class="px-4 py-3 text-left hidden md:table-cell">Horario</th>
                            <th class="px-4 py-3 text-left">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($proximasClases as $clase)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($clase->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $clase->asignacion->materiaGrupo->materia->nombre ?? '-' }}</td>
                            <td class="px-4 py-3 hidden sm:table-cell text-gray-500">{{ $clase->asignacion->materiaGrupo->grupo->nombre ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-400 text-xs hidden md:table-cell whitespace-nowrap">{{ $clase->bloque->hora_inicio ?? '-' }} - {{ $clase->bloque->hora_fin ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('docente.asistencia.pase-lista', $clase) }}"
                                   class="text-green-600 hover:text-green-800 text-xs font-medium underline whitespace-nowrap">
                                    Pase de lista
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if(!empty($asistenciaPorGrupo))
(function () {
    const data = @json($asistenciaPorGrupo);
    const labels    = data.map(d => d.label);
    const presentes = data.map(d => d.presentes);
    const ausentes  = data.map(d => d.ausentes);
    const justs     = data.map(d => d.justificados);

    new Chart(document.getElementById('chartAsistencia'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label: 'Presentes',    data: presentes, backgroundColor: '#22c55e' },
                { label: 'Ausentes',     data: ausentes,  backgroundColor: '#ef4444' },
                { label: 'Justificados', data: justs,     backgroundColor: '#f59e0b' },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
            scales: {
                x: { stacked: true, ticks: { font: { size: 10 } } },
                y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }
            }
        }
    });
})();
@endif

@if($stats['total_clases'] > 0)
(function () {
    new Chart(document.getElementById('chartEstado'), {
        type: 'doughnut',
        data: {
            labels: ['Realizadas', 'Programadas', 'Canceladas'],
            datasets: [{
                data: [{{ $stats['clases_realizadas'] }}, {{ $stats['clases_pendientes'] }}, {{ $stats['clases_canceladas'] }}],
                backgroundColor: ['#22c55e', '#3b82f6', '#ef4444'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 } } }
            },
            cutout: '60%'
        }
    });
})();
@endif
</script>
@endpush
