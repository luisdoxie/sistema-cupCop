@extends('layouts.admin')

@section('page-title', 'Exámenes del Grupo')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                {{ $grupo->nombre }} — Paralelo {{ $grupo->paralelo }}
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Gestión: {{ $grupo->gestion->nombre ?? '—' }} |
                Modalidad: {{ ucfirst($grupo->modalidad) }} |
                Estado:
                <span class="{{ $grupo->estado === 'activo' ? 'text-green-600' : 'text-gray-500' }} font-medium">
                    {{ ucfirst($grupo->estado) }}
                </span>
            </p>
        </div>
        <div class="flex gap-3">
            @if($grupo->estado !== 'activo')
            <form method="POST" action="{{ route('admin.grupos.activar', $grupo) }}">
                @csrf
                <button type="submit"
                        onclick="return confirm('¿Activar grupo y crear exámenes automáticamente?')"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                    Activar Grupo y Crear Exámenes
                </button>
            </form>
            @endif
            <a href="{{ route('admin.examenes.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">
                Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($grupo->materiaGrupos->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            Este grupo no tiene materias asignadas.
        </div>
    @else
        @foreach($grupo->materiaGrupos as $mg)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ $mg->materia->nombre ?? 'Materia desconocida' }}
                    <span class="text-sm text-gray-500 font-normal ml-2">({{ $mg->materia->sigla ?? '' }})</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                @if($mg->examenes->isEmpty())
                    <p class="px-6 py-4 text-gray-500 text-sm">No hay exámenes creados para esta materia.</p>
                @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Puntaje Máx.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($mg->examenes as $examen)
                        <tr class="hover:bg-gray-50" x-data="{ editandoFecha: false }">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                @if($examen->tipo === 'parcial1') Primer Parcial
                                @elseif($examen->tipo === 'parcial2') Segundo Parcial
                                @else Final
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $examen->puntaje_maximo }} pts</td>

                            {{-- Celda de fecha con edición inline --}}
                            <td class="px-6 py-4 text-gray-600">
                                <span x-show="!editandoFecha" class="cursor-pointer" @click="editandoFecha = true">
                                    {{ $examen->fecha ? \Carbon\Carbon::parse($examen->fecha)->format('d/m/Y') : 'Por definir' }}
                                    <span class="text-blue-400 text-xs ml-1">✎</span>
                                </span>
                                <form x-show="editandoFecha" method="POST"
                                      action="{{ route('admin.examenes.fecha', $examen) }}"
                                      class="flex items-center gap-2">
                                    @csrf @method('PATCH')
                                    <input type="date" name="fecha"
                                           value="{{ $examen->fecha }}"
                                           class="border border-gray-300 rounded px-2 py-1 text-xs w-36">
                                    <button type="submit"
                                            class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded">
                                        Guardar
                                    </button>
                                    <button type="button" @click="editandoFecha = false"
                                            class="text-xs text-gray-500 hover:underline">
                                        Cancelar
                                    </button>
                                </form>
                            </td>

                            <td class="px-6 py-4">
                                @if($examen->estado === 'programado')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Programado</span>
                                @elseif($examen->estado === 'realizado')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Realizado</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Anulado</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($examen->estado !== 'anulado')
                                <div class="flex gap-3">
                                    @if($examen->estado === 'programado')
                                    <form method="POST" action="{{ route('admin.examenes.estado', $examen) }}">
                                        @csrf
                                        <input type="hidden" name="estado" value="realizado">
                                        <button type="submit"
                                                onclick="return confirm('¿Marcar como realizado?')"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium">
                                            Realizado
                                        </button>
                                    </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.examenes.estado', $examen) }}">
                                        @csrf
                                        <input type="hidden" name="estado" value="anulado">
                                        <button type="submit"
                                                onclick="return confirm('¿Anular este examen? Esta acción no se puede deshacer.')"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Anular
                                        </button>
                                    </form>
                                </div>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection
