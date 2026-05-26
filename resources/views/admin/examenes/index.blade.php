@extends('layouts.admin')

@section('page-title', 'Gestión de Exámenes')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Grupos y Exámenes</h2>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($grupos->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            No hay grupos registrados.
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gestión</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materias</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exámenes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($grupos as $grupo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $grupo->nombre }} — Paralelo {{ $grupo->paralelo }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $grupo->gestion->nombre ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $grupo->materiaGrupos->count() }}</td>
                        <td class="px-6 py-4">
                            @if($grupo->estado === 'activo')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Activo</span>
                            @else
                                <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-0.5 rounded">{{ ucfirst($grupo->estado) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            @php
                                $totalExamenes = $grupo->materiaGrupos->sum(fn($mg) => $mg->examenes->count());
                            @endphp
                            {{ $totalExamenes }}
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="{{ route('admin.examenes.porGrupo', $grupo) }}"
                               class="text-blue-600 hover:underline text-sm">Ver Detalle</a>

                            @if($grupo->estado !== 'activo')
                            <form method="POST" action="{{ route('admin.grupos.activar', $grupo) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('¿Activar el grupo y crear exámenes automáticamente?')"
                                        class="text-green-600 hover:underline text-sm">
                                    Activar
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $grupos->links() }}
        </div>
    @endif
</div>
@endsection
