@extends('layouts.docente')
@section('title', 'Mis Clases')
@section('page-title', 'Mis Clases')

@section('content')
<div class="space-y-4">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    {{-- Navegación semanal --}}
    <div class="bg-white rounded-lg shadow px-6 py-4 flex items-center justify-between">
        <a href="{{ route('docente.clases.index', ['semana' => $inicio->copy()->subWeek()->format('Y-W')]) }}"
           class="text-green-700 hover:text-green-900 font-medium text-sm">← Semana anterior</a>
        <div class="text-center">
            <p class="font-semibold text-gray-800">{{ $inicio->format('d/m/Y') }} — {{ $fin->format('d/m/Y') }}</p>
            <p class="text-xs text-gray-500">Semana {{ $inicio->weekOfYear }} de {{ $inicio->year }}</p>
        </div>
        <a href="{{ route('docente.clases.index', ['semana' => $inicio->copy()->addWeek()->format('Y-W')]) }}"
           class="text-green-700 hover:text-green-900 font-medium text-sm">Semana siguiente →</a>
    </div>

    {{-- Botón nueva clase --}}
    <div class="flex justify-end">
        <a href="{{ route('docente.clases.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded">
            + Programar Clase
        </a>
    </div>

    {{-- Calendario semanal --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach($dias as $dia)
        @php
            $fechaStr  = $dia->toDateString();
            $clasesHoy = $clases->get($fechaStr, collect());
            $esHoy     = $dia->isToday();
        @endphp
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-3 py-2 text-center {{ $esHoy ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                <p class="text-xs font-medium uppercase">{{ $dia->locale('es')->isoFormat('ddd') }}</p>
                <p class="text-lg font-bold">{{ $dia->format('d') }}</p>
                <p class="text-xs">{{ $dia->format('M') }}</p>
            </div>
            <div class="p-2 space-y-2 min-h-24">
                @forelse($clasesHoy as $clase)
                @php
                    $sinAsistencia = $clase->estado === 'realizada' && $clase->asistencias->isEmpty();
                    $colorBase = match($clase->estado) {
                        'realizada'  => $sinAsistencia ? 'bg-orange-50 border-orange-400' : 'bg-green-50 border-green-400',
                        'cancelada'  => 'bg-red-50 border-red-400',
                        default      => 'bg-blue-50 border-blue-400',
                    };
                @endphp
                <div class="border-l-4 {{ $colorBase }} rounded p-2 text-xs">
                    <p class="font-medium truncate">{{ $clase->asignacion->materiaGrupo->materia->nombre }}</p>
                    <p class="text-gray-500">{{ $clase->bloque->hora_inicio }} - {{ $clase->bloque->hora_fin }}</p>
                    <p class="text-gray-400 truncate">Aula {{ $clase->aula->numero }}</p>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="px-1 py-0.5 rounded text-xs {{ $clase->estado === 'realizada' ? 'bg-green-100 text-green-700' : ($clase->estado === 'cancelada' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($clase->estado) }}
                        </span>
                    </div>
                    <div class="flex gap-1 mt-1">
                        @if($clase->estado !== 'cancelada')
                        <a href="{{ route('docente.asistencia.pase-lista', $clase) }}"
                           class="flex-1 text-center bg-green-600 hover:bg-green-700 text-white text-xs py-0.5 rounded">
                            {{ $clase->asistencias->isNotEmpty() ? 'Ver' : 'Lista' }}
                        </a>
                        @endif
                        @if($clase->estado === 'programada')
                        <form method="POST" action="{{ route('docente.clases.estado', $clase) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="estado" value="cancelada">
                            <button type="submit" class="w-full text-center bg-red-500 hover:bg-red-600 text-white text-xs py-0.5 rounded"
                                    onclick="return confirm('¿Cancelar esta clase?')">
                                Cancelar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-300 text-center mt-4">Sin clases</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

    {{-- Leyenda --}}
    <div class="flex gap-4 text-xs text-gray-500">
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-200 inline-block"></span> Programada</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-200 inline-block"></span> Realizada</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-orange-200 inline-block"></span> Sin asistencia</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-200 inline-block"></span> Cancelada</span>
    </div>
</div>
@endsection
