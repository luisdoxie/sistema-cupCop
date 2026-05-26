@extends('layouts.admin')

@section('title', isset($gestion) ? 'Editar Gestión' : 'Nueva Gestión')
@section('page-title', isset($gestion) ? 'Editar Gestión' : 'Nueva Gestión')

@section('content')
<div class="max-w-2xl">

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST"
              action="{{ isset($gestion) ? route('admin.gestiones.update', $gestion) : route('admin.gestiones.store') }}">
            @csrf
            @if(isset($gestion)) @method('PUT') @endif

            <div class="grid grid-cols-1 gap-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $gestion->nombre ?? '') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ej: Gestión 2025-1" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Año <span class="text-red-500">*</span></label>
                        <input type="number" name="anio" value="{{ old('anio', $gestion->anio ?? date('Y')) }}"
                               min="2000" max="2100"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Semestre <span class="text-red-500">*</span></label>
                        <select name="semestre" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="1" {{ old('semestre', $gestion->semestre ?? '') == 1 ? 'selected' : '' }}>1° Semestre</option>
                            <option value="2" {{ old('semestre', $gestion->semestre ?? '') == 2 ? 'selected' : '' }}>2° Semestre</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio <span class="text-red-500">*</span></label>
                        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $gestion->fecha_inicio ?? '') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin <span class="text-red-500">*</span></label>
                        <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $gestion->fecha_fin ?? '') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                    <select name="estado" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="planificado" {{ old('estado', $gestion->estado ?? 'planificado') === 'planificado' ? 'selected' : '' }}>Planificado</option>
                        <option value="activo"      {{ old('estado', $gestion->estado ?? '') === 'activo'      ? 'selected' : '' }}>Activo</option>
                        <option value="cerrado"     {{ old('estado', $gestion->estado ?? '') === 'cerrado'     ? 'selected' : '' }}>Cerrado</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Solo puede existir una gestión activa a la vez.</p>
                </div>

            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded">
                    {{ isset($gestion) ? 'Actualizar' : 'Crear Gestión' }}
                </button>
                <a href="{{ route('admin.gestiones.index') }}" class="text-sm text-gray-500 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
