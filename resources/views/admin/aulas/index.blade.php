@extends('layouts.admin')

@section('title', 'Aulas')
@section('page-title', 'Aulas')

@section('content')
<div class="space-y-4">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-700">Listado de Aulas</h2>
        <a href="{{ route('admin.aulas.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
            + Nueva Aula
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <select name="id_piso" class="border border-gray-300 rounded text-sm px-3 py-2">
            <option value="">Todos los pisos</option>
            @foreach($pisos as $piso)
            <option value="{{ $piso->id }}" {{ request('id_piso') == $piso->id ? 'selected' : '' }}>
                Piso {{ $piso->nombre ?? $piso->id }}
            </option>
            @endforeach
        </select>
        <select name="modalidad" class="border border-gray-300 rounded text-sm px-3 py-2">
            <option value="">Todas las modalidades</option>
            <option value="presencial" {{ request('modalidad') === 'presencial' ? 'selected' : '' }}>Presencial</option>
            <option value="virtual"    {{ request('modalidad') === 'virtual'    ? 'selected' : '' }}>Virtual</option>
        </select>
        <select name="estado" class="border border-gray-300 rounded text-sm px-3 py-2">
            <option value="">Todos los estados</option>
            <option value="disponible"    {{ request('estado') === 'disponible'    ? 'selected' : '' }}>Disponible</option>
            <option value="ocupada"       {{ request('estado') === 'ocupada'       ? 'selected' : '' }}>Ocupada</option>
            <option value="mantenimiento" {{ request('estado') === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
        </select>
        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-3 py-2 rounded">Filtrar</button>
        <a href="{{ route('admin.aulas.index') }}" class="text-sm text-gray-500 hover:underline">Limpiar</a>
    </form>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Piso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modalidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($aulas as $aula)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-600">Piso {{ $aula->piso->nombre ?? $aula->id_piso }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $aula->numero }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $aula->capacidad }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $aula->tipo }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $aula->modalidad }}</td>
                    <td class="px-6 py-4">
                        @if($aula->estado === 'disponible')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Disponible</span>
                        @elseif($aula->estado === 'ocupada')
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Ocupada</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Mantenimiento</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.aulas.edit', $aula) }}" class="text-blue-600 hover:underline">Editar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">No hay aulas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $aulas->links() }}</div>
</div>
@endsection
