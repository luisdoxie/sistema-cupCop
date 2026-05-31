@extends('layouts.admin')

@section('title', 'Clases Programadas')
@section('page-title', 'Clases Programadas')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    @if(!$gestion)
        <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded">
            No hay gestión activa. Active una gestión primero.
        </div>
    @else
    {{-- Info gestión --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-800">
            <strong>Gestión activa:</strong> {{ $gestion->nombre }} &nbsp;|&nbsp;
            {{ \Carbon\Carbon::parse($gestion->fecha_inicio)->format('d/m/Y') }} →
            {{ \Carbon\Carbon::parse($gestion->fecha_fin)->format('d/m/Y') }}
        </p>
    </div>

    {{-- Estadísticas --}}
    @if($stats)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Total generadas</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $stats['programadas'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Programadas</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stats['realizadas'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Realizadas</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-red-500">{{ $stats['canceladas'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Canceladas</p>
        </div>
    </div>
    @endif

    {{-- Acciones --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Generar clases del semestre</h3>
        <p class="text-sm text-gray-600 mb-4">
            Genera automáticamente todas las sesiones semanales para cada bloque horario asignado,
            desde <strong>{{ \Carbon\Carbon::parse($gestion->fecha_inicio)->format('d/m/Y') }}</strong>
            hasta <strong>{{ \Carbon\Carbon::parse($gestion->fecha_fin)->format('d/m/Y') }}</strong>.
            Las clases ya existentes no se duplican.
        </p>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('admin.clases.generar') }}">
                @csrf
                <button type="submit"
                        onclick="return confirm('¿Generar todas las clases programadas del semestre? Las ya existentes se omitirán.')"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded">
                    Generar clases del semestre
                </button>
            </form>

            @if($stats && $stats['total'] > 0)
            <form method="POST" action="{{ route('admin.clases.limpiar') }}">
                @csrf @method('DELETE')
                <button type="submit"
                        onclick="return confirm('¿Eliminar TODAS las clases programadas de esta gestión? También se eliminará la asistencia registrada.')"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2 rounded">
                    Limpiar y regenerar
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Resumen por grupo/materia --}}
    @if(count($porGrupo) > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Clases por grupo y materia</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clases generadas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($porGrupo as $fila)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $fila->grupo }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $fila->materia }}</td>
                    <td class="px-6 py-3 text-sm text-gray-600">{{ $fila->total_clases }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @endif
</div>
@endsection
