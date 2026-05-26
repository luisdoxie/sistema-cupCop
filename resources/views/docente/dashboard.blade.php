@extends('layouts.docente')
@section('title', 'Dashboard - Docente')
@section('page-title', 'Mi Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Accesos rápidos --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('docente.clases.index') }}"
           class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500 hover:shadow-md transition-shadow flex items-center gap-4">
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Mis Clases</p>
                <p class="text-xs text-gray-500">Calendario semanal y asistencia</p>
            </div>
        </a>
        <a href="{{ route('docente.notas.index') }}"
           class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500 hover:shadow-md transition-shadow flex items-center gap-4">
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Notas</p>
                <p class="text-xs text-gray-500">Planilla de calificaciones</p>
            </div>
        </a>
        <a href="{{ route('docente.clases.create') }}"
           class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500 hover:shadow-md transition-shadow flex items-center gap-4">
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Programar Clase</p>
                <p class="text-xs text-gray-500">Nueva clase con aula y bloque</p>
            </div>
        </a>
    </div>

    {{-- Clases pendientes de asistencia --}}
    @if($sinAsistencia->isNotEmpty())
    <div>
        <h2 class="text-base font-semibold text-gray-700 mb-3 flex items-center gap-2">
            Clases sin Asistencia
            <span class="bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded-full">{{ $sinAsistencia->count() }}</span>
        </h2>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-orange-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Grupo</th>
                        <th class="px-4 py-3 text-left">Materia</th>
                        <th class="px-4 py-3 text-left">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sinAsistencia as $clase)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($clase->fecha)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $clase->asignacion->materiaGrupo->grupo->nombre ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $clase->asignacion->materiaGrupo->materia->nombre ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('docente.asistencia.pase-lista', $clase) }}"
                               class="text-orange-600 hover:text-orange-800 text-xs font-medium underline">
                                Registrar asistencia
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Mis asignaciones --}}
    <div>
        <h2 class="text-base font-semibold text-gray-700 mb-3">Mis Asignaciones</h2>
        @if($misAsignaciones->isEmpty())
        <div class="bg-white rounded-lg shadow p-6 text-center text-gray-400 text-sm">
            No tienes asignaciones activas.
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($misAsignaciones as $asig)
            <div class="bg-white rounded-lg shadow p-4 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-800">{{ $asig->materiaGrupo->materia->nombre }}</p>
                    <p class="text-sm text-gray-500">Grupo: {{ $asig->materiaGrupo->grupo->nombre }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        @foreach($asig->bloquesHorario as $b)
                            {{ ucfirst($b->dia) }} {{ $b->hora_inicio }}-{{ $b->hora_fin }}
                        @endforeach
                    </p>
                </div>
                <div class="flex flex-col gap-2 ml-4">
                    <a href="{{ route('docente.notas.planilla', $asig->materiaGrupo->grupo) }}"
                       class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded text-center">
                        Notas
                    </a>
                    <a href="{{ route('docente.clases.create') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded text-center">
                        + Clase
                    </a>
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
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Materia</th>
                        <th class="px-4 py-3 text-left">Grupo</th>
                        <th class="px-4 py-3 text-left">Horario</th>
                        <th class="px-4 py-3 text-left">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($proximasClases as $clase)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ \Carbon\Carbon::parse($clase->fecha)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $clase->asignacion->materiaGrupo->materia->nombre ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $clase->asignacion->materiaGrupo->grupo->nombre ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $clase->bloque->hora_inicio ?? '-' }} - {{ $clase->bloque->hora_fin ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('docente.asistencia.pase-lista', $clase) }}"
                               class="text-green-600 hover:text-green-800 text-xs font-medium underline">
                                Pase de lista
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
