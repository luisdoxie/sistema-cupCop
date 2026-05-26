@extends('layouts.admin')

@section('page-title', 'Admisión Final')

@section('content')
<div class="space-y-6" x-data="{ modalProcesar: false }">

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Resultados de Admisión</h2>
        <div class="flex gap-3">
            <a href="{{ route('admin.resultados.pdf') }}"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                Exportar PDF Admitidos
            </a>
            <button @click="modalProcesar = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                Procesar Admisión Final
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Modal de confirmación --}}
    <div x-show="modalProcesar"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Confirmar Procesamiento</h3>
            <p class="text-gray-600 mb-4">
                Este proceso calculará el promedio final de cada estudiante con estado
                <strong>cursando</strong> y asignará su carrera según disponibilidad de cupos.
                Esta acción <strong>no se puede deshacer fácilmente</strong>.
            </p>
            <div class="flex gap-3 justify-end">
                <button @click="modalProcesar = false"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">
                    Cancelar
                </button>
                <form method="POST" action="{{ route('admin.resultados.procesar') }}">
                    @csrf
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Confirmar Proceso
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Resumen estadístico --}}
    @if($resumen)
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $resumen['total'] }}</p>
            <p class="text-sm text-gray-500">Total</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $resumen['admitidos_c1'] }}</p>
            <p class="text-sm text-gray-500">Admitidos C1</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-teal-600">{{ $resumen['admitidos_c2'] }}</p>
            <p class="text-sm text-gray-500">Admitidos C2</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $resumen['reprobados'] }}</p>
            <p class="text-sm text-gray-500">Reprobados</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-orange-600">{{ $resumen['no_admitido'] }}</p>
            <p class="text-sm text-gray-500">Sin Cupo</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $resumen['cursando'] }}</p>
            <p class="text-sm text-gray-500">Cursando</p>
        </div>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">Todos los estados</option>
                    <option value="cursando" {{ request('estado') === 'cursando' ? 'selected' : '' }}>Cursando</option>
                    <option value="admitido_carrera1" {{ request('estado') === 'admitido_carrera1' ? 'selected' : '' }}>Admitido Carrera 1</option>
                    <option value="admitido_carrera2" {{ request('estado') === 'admitido_carrera2' ? 'selected' : '' }}>Admitido Carrera 2</option>
                    <option value="reprobado" {{ request('estado') === 'reprobado' ? 'selected' : '' }}>Reprobado</option>
                    <option value="no_admitido" {{ request('estado') === 'no_admitido' ? 'selected' : '' }}>Sin Cupo</option>
                </select>
            </div>
            <div>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    Filtrar
                </button>
                <a href="{{ route('admin.resultados.index') }}"
                   class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($admisiones->isEmpty())
            <div class="p-8 text-center text-gray-500">No hay registros que mostrar.</div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CI</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carrera 1</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carrera 2</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Promedio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carrera Asignada</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($admisiones as $admision)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $admision->estudiante->persona->ci ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $admision->estudiante->persona->nombre ?? '' }}
                            {{ $admision->estudiante->persona->apellido ?? '' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $admision->carrera1->sigla ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $admision->carrera2->sigla ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            {{ $admision->promedio_final !== null ? number_format($admision->promedio_final, 2) : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badgeMap = [
                                    'admitido_carrera1' => 'bg-green-100 text-green-800',
                                    'admitido_carrera2' => 'bg-teal-100 text-teal-800',
                                    'reprobado'         => 'bg-red-100 text-red-800',
                                    'no_admitido'       => 'bg-orange-100 text-orange-800',
                                    'cursando'          => 'bg-blue-100 text-blue-800',
                                ];
                                $labelMap = [
                                    'admitido_carrera1' => 'Admitido C1',
                                    'admitido_carrera2' => 'Admitido C2',
                                    'reprobado'         => 'Reprobado',
                                    'no_admitido'       => 'Sin Cupo',
                                    'cursando'          => 'Cursando',
                                ];
                                $badge = $badgeMap[$admision->estado] ?? 'bg-gray-100 text-gray-700';
                                $label = $labelMap[$admision->estado] ?? ucfirst($admision->estado);
                            @endphp
                            <span class="{{ $badge }} text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($admision->estado === 'admitido_carrera1')
                                {{ $admision->carrera1->nombre ?? '—' }}
                            @elseif($admision->estado === 'admitido_carrera2')
                                {{ $admision->carrera2->nombre ?? '—' }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $admisiones->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
