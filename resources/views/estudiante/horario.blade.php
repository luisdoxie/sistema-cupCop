@extends('layouts.estudiante')

@section('title', 'Mi Horario')
@section('page-title', 'Mi Horario Semanal')

@section('content')
<div class="space-y-4">

    @if(!$admision || !$admision->id_grupo)
        <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-4 rounded-lg">
            Aún no tienes un grupo asignado. El horario estará disponible una vez que seas asignado a un grupo.
        </div>
    @else

    {{-- Info grupo --}}
    <div class="bg-white rounded-lg shadow px-6 py-3 flex items-center justify-between">
        <div>
            <span class="text-sm text-gray-500">Grupo:</span>
            <span class="ml-2 font-semibold text-gray-800">{{ $admision->grupo->nombre }} — Paralelo {{ $admision->grupo->paralelo }}</span>
        </div>
        <div class="text-sm text-gray-500">
            {{ $gestion->nombre ?? '' }}
        </div>
    </div>

    {{-- Navegación semanal --}}
    <div class="bg-white rounded-lg shadow px-6 py-4 flex items-center justify-between">
        <a href="{{ route('estudiante.horario', ['semana' => $inicio->copy()->subWeek()->format('Y-W')]) }}"
           class="text-purple-700 hover:text-purple-900 font-medium text-sm">← Semana anterior</a>
        <div class="text-center">
            <p class="font-semibold text-gray-800">{{ $inicio->format('d/m/Y') }} — {{ $fin->format('d/m/Y') }}</p>
            <p class="text-xs text-gray-500">Semana {{ $inicio->weekOfYear }} de {{ $inicio->year }}</p>
        </div>
        <a href="{{ route('estudiante.horario', ['semana' => $inicio->copy()->addWeek()->format('Y-W')]) }}"
           class="text-purple-700 hover:text-purple-900 font-medium text-sm">Semana siguiente →</a>
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
            <div class="px-3 py-2 text-center {{ $esHoy ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                <p class="text-xs font-medium uppercase">{{ $dia->locale('es')->isoFormat('ddd') }}</p>
                <p class="text-lg font-bold">{{ $dia->format('d') }}</p>
                <p class="text-xs">{{ $dia->format('M') }}</p>
            </div>
            <div class="p-2 space-y-2 min-h-24">
                @forelse($clasesHoy as $clase)
                @php
                    $color = match($clase->estado) {
                        'realizada'  => 'bg-green-50 border-green-400',
                        'cancelada'  => 'bg-red-50 border-red-400',
                        default      => 'bg-purple-50 border-purple-400',
                    };
                @endphp
                <div class="border-l-4 {{ $color }} rounded p-2 text-xs">
                    <p class="font-semibold text-gray-800 truncate">
                        {{ $clase->asignacion->materiaGrupo->materia->nombre }}
                    </p>
                    <p class="text-gray-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($clase->bloque->hora_inicio)->format('H:i') }}
                        — {{ \Carbon\Carbon::parse($clase->bloque->hora_fin)->format('H:i') }}
                    </p>
                    <p class="text-purple-700 font-medium mt-0.5">
                        Aula {{ $clase->aula->numero }}
                        @if($clase->aula->piso)
                            <span class="text-gray-400">(Piso {{ $clase->aula->piso->numero }})</span>
                        @endif
                    </p>
                    @if($clase->estado === 'cancelada')
                        <span class="inline-block mt-1 bg-red-100 text-red-700 px-1 py-0.5 rounded text-xs">Cancelada</span>
                    @elseif($clase->estado === 'realizada')
                        <span class="inline-block mt-1 bg-green-100 text-green-700 px-1 py-0.5 rounded text-xs">Realizada</span>
                    @endif
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
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-purple-200 inline-block"></span> Programada</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-200 inline-block"></span> Realizada</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-200 inline-block"></span> Cancelada</span>
    </div>

    @endif
</div>
@endsection
