@extends('layouts.admin')

@section('title', 'Docentes')
@section('page-title', 'Docentes')

@section('content')
<div class="space-y-4">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-700">Listado de Docentes</h2>
        <a href="{{ route('admin.docentes.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
            + Nuevo Docente
        </a>
    </div>

    {{-- Búsqueda --}}
    <form method="GET" class="flex items-center gap-3">
        <input type="text" name="buscar" value="{{ request('buscar') }}"
               placeholder="Buscar por nombre, apellido o CI..."
               class="border border-gray-300 rounded px-3 py-2 text-sm w-72">
        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-3 py-2 rounded">Buscar</button>
        <a href="{{ route('admin.docentes.index') }}" class="text-sm text-gray-500 hover:underline">Limpiar</a>
    </form>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CI</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Especialidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($docentes as $docente)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $docente->persona->ci }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        {{ $docente->persona->nombre }} {{ $docente->persona->apellido }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $docente->especialidad }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $docente->grado_academico }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $docente->grupos_activos }} / {{ $docente->max_grupos }}</td>
                    <td class="px-6 py-4">
                        @if($docente->estado === 'activo')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Activo</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right text-sm space-x-2">
                        <a href="{{ route('admin.docentes.show', $docente) }}" class="text-gray-600 hover:underline">Ver</a>
                        <a href="{{ route('admin.docentes.edit', $docente) }}" class="text-blue-600 hover:underline">Editar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">No hay docentes registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $docentes->links() }}</div>
</div>
@endsection
