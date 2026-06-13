@extends('layouts.admin')

@section('title', 'Detalle Gestión')
@section('page-title', 'Detalle de Gestión')

@section('content')
<div class="space-y-6 max-w-4xl">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $gestion->nombre }}</h2>
            <p class="text-sm text-gray-500">Año {{ $gestion->anio }} — Semestre {{ $gestion->semestre }}°</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.gestiones.edit', $gestion) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
                Editar
            </a>
            <a href="{{ route('admin.gestiones.index') }}"
               class="text-sm text-gray-500 hover:underline">← Volver</a>
        </div>
    </div>

    {{-- Stats generales --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        @foreach([
            ['label' => 'Postulantes', 'value' => $stats['total_postulantes'], 'color' => 'blue'],
            ['label' => 'Cursando',    'value' => $stats['cursando'],          'color' => 'indigo'],
            ['label' => 'Admitidos',   'value' => $stats['admitidos'],         'color' => 'green'],
            ['label' => 'Reprobados',  'value' => $stats['reprobados'],        'color' => 'red'],
            ['label' => 'Grupos',      'value' => $stats['grupos'],            'color' => 'gray'],
        ] as $stat)
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-{{ $stat['color'] }}-600">{{ $stat['value'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Cupos por carrera --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Cupos por Carrera</h3>
            <p class="text-xs text-gray-400 mt-0.5">El cupo disponible se reduce al procesar la admisión final.</p>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carrera</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sigla</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cupo Máximo</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cupo Disponible</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ocupados</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">% Llenado</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($carrerasGestion as $cg)
                @php
                    $ocupados  = $cg->cupo_maximo - $cg->cupo_disponible;
                    $porcentaje = $cg->cupo_maximo > 0 ? round(($ocupados / $cg->cupo_maximo) * 100) : 0;
                    $barColor  = $porcentaje >= 90 ? 'bg-red-500' : ($porcentaje >= 60 ? 'bg-yellow-400' : 'bg-green-500');
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $cg->carrera->nombre }}</td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded">{{ $cg->carrera->sigla }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-center text-gray-600">{{ $cg->cupo_maximo }}</td>
                    <td class="px-6 py-4 text-sm text-center font-semibold {{ $cg->cupo_disponible === 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $cg->cupo_disponible }}
                    </td>
                    <td class="px-6 py-4 text-sm text-center text-gray-600">{{ $ocupados }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 w-8">{{ $porcentaje }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Info de la gestión --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 border-b pb-2">Información General</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Estado</dt>
                <dd class="font-medium mt-0.5">
                    @if($gestion->estado === 'activo')
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Activo</span>
                    @elseif($gestion->estado === 'planificado')
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Planificado</span>
                    @else
                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Cerrado</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Año / Semestre</dt>
                <dd class="font-medium mt-0.5">{{ $gestion->anio }} — {{ $gestion->semestre }}°</dd>
            </div>
            <div>
                <dt class="text-gray-500">Fecha Inicio</dt>
                <dd class="font-medium mt-0.5">{{ \Carbon\Carbon::parse($gestion->fecha_inicio)->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Fecha Fin</dt>
                <dd class="font-medium mt-0.5">{{ \Carbon\Carbon::parse($gestion->fecha_fin)->format('d/m/Y') }}</dd>
            </div>
        </dl>
    </div>

</div>
@endsection
