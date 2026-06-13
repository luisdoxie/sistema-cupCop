@extends('layouts.admin')

@section('title', 'Gestiones')
@section('page-title', 'Gestiones Académicas')

@section('content')
<div class="space-y-4">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-700">Listado de Gestiones</h2>
        <a href="{{ route('admin.gestiones.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
            + Nueva Gestión
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex items-center gap-3">
        <select name="estado" class="border border-gray-300 rounded text-sm px-3 py-2">
            <option value="">Todos los estados</option>
            <option value="activo"      {{ request('estado') === 'activo'      ? 'selected' : '' }}>Activo</option>
            <option value="planificado" {{ request('estado') === 'planificado' ? 'selected' : '' }}>Planificado</option>
            <option value="cerrado"     {{ request('estado') === 'cerrado'     ? 'selected' : '' }}>Cerrado</option>
        </select>
        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-3 py-2 rounded">Filtrar</button>
        <a href="{{ route('admin.gestiones.index') }}" class="text-sm text-gray-500 hover:underline">Limpiar</a>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Año / Sem.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Fin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($gestiones as $gestion)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $gestion->nombre }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $gestion->anio }} / {{ $gestion->semestre }}°</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($gestion->fecha_inicio)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($gestion->fecha_fin)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @if($gestion->estado === 'activo')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Activo</span>
                        @elseif($gestion->estado === 'planificado')
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Planificado</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Cerrado</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right text-sm space-x-2">
                        <a href="{{ route('admin.gestiones.show', $gestion) }}"
                           class="text-gray-600 hover:underline">Ver</a>
                        <a href="{{ route('admin.gestiones.edit', $gestion) }}"
                           class="text-blue-600 hover:underline">Editar</a>
                        @if($gestion->estado === 'activo')
                        <form method="POST" action="{{ route('admin.gestiones.cerrar', $gestion) }}" class="inline"
                              onsubmit="return confirm('¿Cerrar esta gestión?')">
                            @csrf
                            <button type="submit" class="text-red-600 hover:underline">Cerrar</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No hay gestiones registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div>{{ $gestiones->links() }}</div>
</div>
@endsection
