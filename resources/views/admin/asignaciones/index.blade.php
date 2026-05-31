@extends('layouts.admin')

@section('title', 'Asignaciones Académicas')
@section('page-title', 'Asignaciones Académicas')

@section('content')
<div class="space-y-4">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-700">Horarios y Asignaciones</h2>
        <a href="{{ route('admin.asignaciones.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
            + Nueva Asignación
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Turno</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carga Hor.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bloques</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($asignaciones as $asig)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        {{ $asig->docente->persona->nombre ?? '—' }} {{ $asig->docente->persona->apellido ?? '' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $asig->materiaGrupo->materia->nombre ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $asig->materiaGrupo->grupo->nombre ?? '—' }} ({{ $asig->materiaGrupo->grupo->paralelo ?? '' }})
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $asig->materiaGrupo->turno->nombre ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $asig->carga_horaria }}h</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @foreach($asig->bloquesHorario as $b)
                            <span class="inline-block bg-blue-50 text-blue-700 text-xs px-1.5 py-0.5 rounded mr-1">
                                {{ ucfirst($b->dia) }} {{ $b->hora_inicio }}-{{ $b->hora_fin }}
                            </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4">
                        @if($asig->estado === 'activo')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Activo</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ ucfirst($asig->estado) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form method="POST" action="{{ route('admin.asignaciones.destroy', $asig) }}"
                              class="inline"
                              onsubmit="return confirm('¿Eliminar la asignación de {{ $asig->docente->persona->nombre ?? '' }} en {{ $asig->materiaGrupo->materia->nombre ?? '' }}? Se eliminarán también los bloques de horario.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">No hay asignaciones registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $asignaciones->links() }}</div>
</div>
@endsection
