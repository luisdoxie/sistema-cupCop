@extends('layouts.estudiante')

@section('page-title', 'Mis Resultados')

@section('content')
<div class="space-y-6">

    <h2 class="text-2xl font-bold text-gray-800">Mis Resultados</h2>

    @if(!$admision)
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            No tienes una admisión activa en la gestión actual.
        </div>
    @else

    {{-- Tablas por materia --}}
    @if($materias->isEmpty())
        <div class="bg-white rounded-lg shadow p-6 text-gray-500">
            Aún no hay materias o notas disponibles para mostrar.
        </div>
    @else
        @foreach($materias as $item)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ $item['materia']->nombre ?? 'Materia' }}
                    <span class="text-sm text-gray-500 font-normal">({{ $item['materia']->sigla ?? '' }})</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parcial 1</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parcial 2</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Final</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-6 py-4 text-gray-900 font-medium">
                                {{ $item['notas']['parcial1'] !== null ? number_format($item['notas']['parcial1'], 2) : '—' }}
                                <span class="text-xs text-gray-400">/ 30</span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium">
                                {{ $item['notas']['parcial2'] !== null ? number_format($item['notas']['parcial2'], 2) : '—' }}
                                <span class="text-xs text-gray-400">/ 30</span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium">
                                {{ $item['notas']['final'] !== null ? number_format($item['notas']['final'], 2) : '—' }}
                                <span class="text-xs text-gray-400">/ 40</span>
                            </td>
                            <td class="px-6 py-4 text-xl font-bold
                                {{ $item['total'] !== null ? ($item['total'] >= 60 ? 'text-green-700' : 'text-red-700') : 'text-gray-500' }}">
                                {{ $item['total'] !== null ? number_format($item['total'], 2) : '—' }}
                                <span class="text-xs text-gray-400 font-normal">/ 100</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($item['total'] !== null)
                                    @if($item['total'] >= 60)
                                        <span class="bg-green-100 text-green-800 text-sm font-bold px-3 py-1 rounded-full">
                                            APROBADO
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-sm font-bold px-3 py-1 rounded-full">
                                            REPROBADO
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-sm">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    @endif

    {{-- Resultado Final --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-800 text-white">
            <h3 class="text-lg font-bold">Resultado Final de Admisión</h3>
        </div>
        <div class="p-6">
            @if($admision->estado === 'admitido_carrera1')
                <div class="text-center">
                    <div class="text-5xl mb-4">✓</div>
                    <p class="text-3xl font-bold text-green-600">ADMITIDO</p>
                    <p class="text-xl text-gray-700 mt-2">
                        en <strong>{{ $admision->carrera1->nombre ?? 'Carrera 1' }}</strong>
                    </p>
                    @if($admision->promedio_final !== null)
                        <p class="mt-3 text-gray-500">
                            Promedio final: <strong class="text-green-700">{{ number_format($admision->promedio_final, 2) }}</strong>
                        </p>
                    @endif
                </div>

            @elseif($admision->estado === 'admitido_carrera2')
                <div class="text-center">
                    <div class="text-5xl mb-4">✓</div>
                    <p class="text-3xl font-bold text-teal-600">ADMITIDO</p>
                    <p class="text-xl text-gray-700 mt-2">
                        en <strong>{{ $admision->carrera2->nombre ?? 'Carrera 2' }}</strong>
                    </p>
                    @if($admision->promedio_final !== null)
                        <p class="mt-3 text-gray-500">
                            Promedio final: <strong class="text-teal-700">{{ number_format($admision->promedio_final, 2) }}</strong>
                        </p>
                    @endif
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded p-4 text-sm text-yellow-800">
                        Fue reasignado a su segunda opción porque
                        <strong>{{ $admision->carrera1->nombre ?? 'su primera carrera' }}</strong>
                        no tenia cupo disponible al momento del procesamiento.
                    </div>
                </div>

            @elseif($admision->estado === 'reprobado')
                <div class="text-center">
                    <div class="text-5xl mb-4">✗</div>
                    <p class="text-3xl font-bold text-red-600">REPROBADO</p>
                    <p class="text-gray-500 mt-2">No alcanzaste el mínimo requerido en 4 materias (promedio >= 60).</p>
                    @if($materias->isNotEmpty())
                        <div class="mt-4 text-left max-w-md mx-auto">
                            <p class="font-semibold text-gray-700 mb-2">Materias con resultado insuficiente:</p>
                            <ul class="space-y-1">
                                @foreach($materias as $item)
                                    @if($item['total'] !== null && $item['total'] < 60)
                                    <li class="flex justify-between bg-red-50 rounded px-4 py-2 text-sm">
                                        <span class="text-gray-800">{{ $item['materia']->nombre ?? '—' }}</span>
                                        <span class="font-bold text-red-700">{{ number_format($item['total'], 2) }}</span>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

            @elseif($admision->estado === 'no_admitido')
                <div class="text-center">
                    <div class="text-5xl mb-4">⚠</div>
                    <p class="text-3xl font-bold text-orange-600">SIN CUPO DISPONIBLE</p>
                    <p class="text-gray-500 mt-3">
                        No hay cupo disponible en ninguna de tus carreras elegidas:
                    </p>
                    <ul class="mt-2 text-gray-700 space-y-1">
                        <li><strong>1ra opción:</strong> {{ $admision->carrera1->nombre ?? '—' }}</li>
                        <li><strong>2da opción:</strong> {{ $admision->carrera2->nombre ?? '—' }}</li>
                    </ul>
                </div>

            @else
                <div class="text-center">
                    <div class="text-5xl mb-4">⏳</div>
                    <p class="text-2xl font-bold text-blue-600">Proceso en Curso</p>
                    <p class="text-gray-500 mt-2">
                        Los resultados finales aún no han sido procesados.
                        Estado actual: <strong>{{ ucfirst(str_replace('_', ' ', $admision->estado)) }}</strong>
                    </p>
                </div>
            @endif
        </div>
    </div>

    @endif
</div>
@endsection
