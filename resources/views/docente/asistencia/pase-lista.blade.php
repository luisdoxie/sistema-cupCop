@extends('layouts.docente')
@section('title', 'Pase de Lista')
@section('page-title', 'Pase de Lista')

@section('content')
<div class="space-y-4" x-data="{ cambiados: false }">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    {{-- Info de la clase --}}
    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium">Materia</p>
                <p class="font-semibold">{{ $clase->asignacion->materiaGrupo->materia->nombre }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium">Grupo</p>
                <p class="font-semibold">{{ $clase->asignacion->materiaGrupo->grupo->nombre }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium">Fecha</p>
                <p class="font-semibold">{{ \Carbon\Carbon::parse($clase->fecha)->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium">Horario</p>
                <p class="font-semibold">{{ $clase->bloque->hora_inicio }} - {{ $clase->bloque->hora_fin }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium">Aula</p>
                <p class="font-semibold">{{ $clase->aula->numero }} (Piso {{ $clase->aula->piso->numero ?? '?' }})</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium">Estado Clase</p>
                <span class="px-2 py-0.5 rounded text-xs font-medium
                    {{ $clase->estado === 'realizada' ? 'bg-green-100 text-green-800' :
                       ($clase->estado === 'cancelada' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ ucfirst($clase->estado) }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium">Estudiantes</p>
                <p class="font-semibold">{{ $admisiones->count() }}</p>
            </div>
        </div>

        @if(!$editable)
        <div class="mt-3 bg-yellow-50 border border-yellow-200 text-yellow-800 px-3 py-2 rounded text-sm">
            Esta clase ya no se puede editar
            @if($clase->estado === 'cancelada')(clase cancelada)@else(han pasado más de 24h)@endif.
        </div>
        @endif
    </div>

    @if($admisiones->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
        No hay estudiantes asignados a este grupo.
    </div>
    @else

    <form method="POST" action="{{ route('docente.asistencia.guardar', $clase) }}" @submit="cambiados = false">
        @csrf

        {{-- Acciones rápidas --}}
        @if($editable)
        <div class="flex items-center gap-3 mb-3">
            <span class="text-sm text-gray-600">Marcar todos como:</span>
            <button type="button"
                    @click="document.querySelectorAll('input[type=radio][value=presente]').forEach(r => r.checked = true)"
                    class="bg-green-100 hover:bg-green-200 text-green-800 text-xs px-3 py-1 rounded">
                Todos Presentes
            </button>
            <button type="button"
                    @click="document.querySelectorAll('input[type=radio][value=ausente]').forEach(r => r.checked = true)"
                    class="bg-red-100 hover:bg-red-200 text-red-800 text-xs px-3 py-1 rounded">
                Todos Ausentes
            </button>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CI</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-green-600 uppercase">Presente</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-red-600 uppercase">Ausente</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-yellow-600 uppercase">Justificado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Observación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($admisiones as $i => $admision)
                    @php
                        $reg = $asistenciasExistentes->get($admision->id);
                        $est = $reg ? $reg->estado : 'presente';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $admision->estudiante->persona->ci }}</td>
                        <td class="px-4 py-3 font-medium">
                            {{ $admision->estudiante->persona->nombre }} {{ $admision->estudiante->persona->apellido }}
                        </td>
                        <input type="hidden" name="asistencia[{{ $i }}][id_admision]" value="{{ $admision->id }}">
                        <td class="px-4 py-3 text-center">
                            <input type="radio" name="asistencia[{{ $i }}][estado]" value="presente"
                                   {{ $est === 'presente' ? 'checked' : '' }}
                                   {{ !$editable ? 'disabled' : '' }}
                                   class="w-4 h-4 accent-green-600">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="radio" name="asistencia[{{ $i }}][estado]" value="ausente"
                                   {{ $est === 'ausente' ? 'checked' : '' }}
                                   {{ !$editable ? 'disabled' : '' }}
                                   class="w-4 h-4 accent-red-600">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="radio" name="asistencia[{{ $i }}][estado]" value="justificado"
                                   {{ $est === 'justificado' ? 'checked' : '' }}
                                   {{ !$editable ? 'disabled' : '' }}
                                   class="w-4 h-4 accent-yellow-500">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" name="asistencia[{{ $i }}][observacion]"
                                   value="{{ $reg?->observacion }}"
                                   {{ !$editable ? 'disabled' : '' }}
                                   placeholder="Observación..."
                                   class="w-full border border-gray-200 rounded px-2 py-1 text-xs">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($editable)
        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-6 py-2 rounded">
                Guardar Asistencia
            </button>
            <a href="{{ route('docente.clases.index') }}" class="text-sm text-gray-500 hover:underline">Cancelar</a>
        </div>
        @else
        <div class="mt-4">
            <a href="{{ route('docente.clases.index') }}" class="text-sm text-green-700 hover:underline">← Volver al calendario</a>
        </div>
        @endif
    </form>
    @endif
</div>
@endsection
