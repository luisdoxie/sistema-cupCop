@extends('layouts.admin')

@section('title', 'Carga Masiva')
@section('page-title', 'Carga Masiva de Usuarios')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    {{-- Formulario subida --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Subir Archivo</h3>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.carga-masiva.subir') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-5 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Usuario <span class="text-red-500">*</span></label>
                    <select name="tipo_usuario" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="">Seleccionar tipo...</option>
                        <option value="docente"     {{ old('tipo_usuario') === 'docente'     ? 'selected' : '' }}>Docentes</option>
                        <option value="estudiante"  {{ old('tipo_usuario') === 'estudiante'  ? 'selected' : '' }}>Estudiantes</option>
                        <option value="coordinador" {{ old('tipo_usuario') === 'coordinador' ? 'selected' : '' }}>Coordinadores</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Archivo (XLSX o CSV, máx. 5MB) <span class="text-red-500">*</span></label>
                    <input type="file" name="archivo" accept=".xlsx,.csv"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded">
                    Subir y Procesar
                </button>
            </div>
        </form>
    </div>

    {{-- Descargar plantillas --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Descargar Plantillas CSV</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.carga-masiva.plantilla', 'docente') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded border">
                Plantilla Docentes
            </a>
            <a href="{{ route('admin.carga-masiva.plantilla', 'estudiante') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded border">
                Plantilla Estudiantes
            </a>
            <a href="{{ route('admin.carga-masiva.plantilla', 'coordinador') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded border">
                Plantilla Coordinadores
            </a>
        </div>
        <p class="text-xs text-gray-400 mt-2">
            La contraseña generada será: <code class="bg-gray-100 px-1 rounded">CI + primeras 3 letras del apellido (minúsculas) + !</code>
            <br>Ejemplo: CI=12345, Apellido=García → contraseña: <code class="bg-gray-100 px-1 rounded">12345gar!</code>
        </p>
    </div>

    {{-- Historial de lotes --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-base font-semibold text-gray-700">Lotes Anteriores</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Archivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exitosos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fallidos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ver</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($lotes as $lote)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $lote->nombre_archivo }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $lote->tipo_usuario }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $lote->total_registros }}</td>
                    <td class="px-6 py-4 text-sm text-green-600 font-medium">{{ $lote->exitosos }}</td>
                    <td class="px-6 py-4 text-sm text-red-600 font-medium">{{ $lote->fallidos }}</td>
                    <td class="px-6 py-4">
                        @if($lote->estado === 'completado')
                            <span class="bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded-full">Completado</span>
                        @elseif($lote->estado === 'con_errores')
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-0.5 rounded-full">Con errores</span>
                        @elseif($lote->estado === 'procesando')
                            <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full">Procesando</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs px-2.5 py-0.5 rounded-full">Pendiente</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($lote->fecha_subida)->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.carga-masiva.resultado', $lote) }}"
                           class="text-blue-600 hover:underline">Ver</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">No hay importaciones anteriores.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $lotes->links() }}</div>
    </div>

</div>
@endsection
