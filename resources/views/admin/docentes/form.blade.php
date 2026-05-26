@extends('layouts.admin')

@section('title', isset($docente) ? 'Editar Docente' : 'Nuevo Docente')
@section('page-title', isset($docente) ? 'Editar Docente' : 'Registrar Docente')

@section('content')
<div class="max-w-2xl">

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST"
              action="{{ isset($docente) ? route('admin.docentes.update', $docente) : route('admin.docentes.store') }}">
            @csrf
            @if(isset($docente)) @method('PUT') @endif

            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4 border-b pb-2">Datos Personales</h3>

            <div class="grid grid-cols-1 gap-4 mb-6">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CI <span class="text-red-500">*</span></label>
                        <input type="text" name="ci"
                               value="{{ old('ci', isset($docente) ? $docente->persona->ci : '') }}"
                               {{ isset($docente) ? 'readonly' : '' }}
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm {{ isset($docente) ? 'bg-gray-50 cursor-not-allowed' : '' }}"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo <span class="text-red-500">*</span></label>
                        <select name="sexo" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                            <option value="M" {{ old('sexo', isset($docente) ? $docente->persona->sexo : '') === 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', isset($docente) ? $docente->persona->sexo : '') === 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre"
                               value="{{ old('nombre', isset($docente) ? $docente->persona->nombre : '') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellido <span class="text-red-500">*</span></label>
                        <input type="text" name="apellido"
                               value="{{ old('apellido', isset($docente) ? $docente->persona->apellido : '') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                        <input type="email" name="correo"
                               value="{{ old('correo', isset($docente) ? $docente->persona->correo : '') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="telefono"
                               value="{{ old('telefono', isset($docente) ? $docente->persona->telefono : '') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="direccion"
                           value="{{ old('direccion', isset($docente) ? $docente->persona->direccion : '') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
            </div>

            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4 border-b pb-2">Datos Académicos</h3>

            <div class="grid grid-cols-1 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad <span class="text-red-500">*</span></label>
                    <input type="text" name="especialidad"
                           value="{{ old('especialidad', $docente->especialidad ?? '') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grado Académico <span class="text-red-500">*</span></label>
                    <input type="text" name="grado_academico"
                           value="{{ old('grado_academico', $docente->grado_academico ?? '') }}"
                           placeholder="Ej: Licenciado, Magíster, Doctor"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="diplomado_educacion" value="0">
                    <input type="checkbox" id="diplomado" name="diplomado_educacion" value="1"
                           {{ old('diplomado_educacion', isset($docente) ? ($docente->diplomado_educacion ? '1' : '0') : '0') == '1' ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600">
                    <label for="diplomado" class="text-sm text-gray-700">
                        Tiene diplomado en educación <span class="text-red-500">*</span>
                    </label>
                </div>
                <p class="text-xs text-red-500 -mt-3">El docente debe tener diplomado en educación para ser registrado.</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Años de Experiencia <span class="text-red-500">*</span></label>
                        <input type="number" name="anios_experiencia"
                               value="{{ old('anios_experiencia', $docente->anios_experiencia ?? 4) }}"
                               min="4" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <p class="text-xs text-gray-400 mt-1">Mínimo 4 años.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Máx. Grupos <span class="text-red-500">*</span></label>
                        <input type="number" name="max_grupos"
                               value="{{ old('max_grupos', $docente->max_grupos ?? 3) }}"
                               min="1" max="10"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="activo"   {{ old('estado', $docente->estado ?? 'activo') === 'activo'   ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $docente->estado ?? '') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded">
                    {{ isset($docente) ? 'Actualizar' : 'Registrar Docente' }}
                </button>
                <a href="{{ route('admin.docentes.index') }}" class="text-sm text-gray-500 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
