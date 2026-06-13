@extends('layouts.admin')

@section('title', 'Postulantes')
@section('page-title', 'Gestión de Postulantes')

@section('content')
<div class="space-y-4">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Listado de Postulantes</h2>
            @if($gestionActiva)
                <p class="text-sm text-gray-500">Gestión activa: <span class="font-medium text-blue-600">{{ $gestionActiva->nombre }}</span></p>
            @endif
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="text" name="buscar" value="{{ request('buscar') }}"
               placeholder="Buscar por CI, nombre o apellido..."
               class="border border-gray-300 rounded px-3 py-2 text-sm w-72">
        <select name="estado" class="border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">Todos los estados</option>
            @foreach($estados as $estado)
                <option value="{{ $estado }}" {{ request('estado') === $estado ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $estado)) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-4 py-2 rounded">Buscar</button>
        <a href="{{ route('admin.postulantes.index') }}" class="text-sm text-gray-500 hover:underline">Limpiar</a>
    </form>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CI</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carrera 1</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carrera 2</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($admisiones as $admision)
                @php
                    $colores = [
                        'inscrito'          => 'bg-gray-100 text-gray-700',
                        'documentos_pendientes' => 'bg-yellow-100 text-yellow-800',
                        'pago_pendiente'    => 'bg-orange-100 text-orange-800',
                        'cursando'          => 'bg-blue-100 text-blue-800',
                        'admitido_carrera1' => 'bg-green-100 text-green-800',
                        'admitido_carrera2' => 'bg-teal-100 text-teal-800',
                        'reprobado'         => 'bg-red-100 text-red-800',
                        'no_admitido'       => 'bg-red-50 text-red-600',
                    ];
                    $color = $colores[$admision->estado] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $admision->estudiante->persona->ci }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                        {{ $admision->estudiante->persona->nombre }} {{ $admision->estudiante->persona->apellido }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $admision->carrera1->sigla ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $admision->carrera2->sigla ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $color }}">
                            {{ ucfirst(str_replace('_', ' ', $admision->estado)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($admision->fecha)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right text-sm space-x-3">
                        <a href="{{ route('admin.postulantes.edit', $admision) }}"
                           class="text-blue-600 hover:text-blue-800 font-medium">Editar</a>
                        <form method="POST" action="{{ route('admin.postulantes.destroy', $admision) }}"
                              class="inline"
                              onsubmit="return confirm('¿Eliminar a {{ $admision->estudiante->persona->nombre }} {{ $admision->estudiante->persona->apellido }}? Se eliminarán todos sus datos. Esta acción no se puede deshacer.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">No hay postulantes registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $admisiones->links() }}</div>
</div>
@endsection
