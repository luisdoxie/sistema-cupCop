@extends('layouts.admin')

@section('title', 'Editar Postulante')
@section('page-title', 'Editar Postulante')

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
        <form method="POST" action="{{ route('admin.postulantes.update', $admision) }}">
            @csrf @method('PUT')

            {{-- Datos Personales --}}
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4 border-b pb-2">Datos Personales</h3>

            <div class="grid grid-cols-1 gap-4 mb-6">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CI</label>
                        <input type="text" value="{{ $admision->estudiante->persona->ci }}"
                               readonly class="w-full border border-gray-200 bg-gray-50 rounded px-3 py-2 text-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo <span class="text-red-500">*</span></label>
                        <select name="sexo" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                            <option value="M" {{ old('sexo', $admision->estudiante->persona->sexo) === 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', $admision->estudiante->persona->sexo) === 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre"
                               value="{{ old('nombre', $admision->estudiante->persona->nombre) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellido <span class="text-red-500">*</span></label>
                        <input type="text" name="apellido"
                               value="{{ old('apellido', $admision->estudiante->persona->apellido) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                        <input type="email" name="correo"
                               value="{{ old('correo', $admision->estudiante->persona->correo) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="telefono"
                               value="{{ old('telefono', $admision->estudiante->persona->telefono) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            {{-- Datos del Estudiante --}}
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4 border-b pb-2">Datos Académicos Previos</h3>

            <div class="grid grid-cols-1 gap-4 mb-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento"
                               value="{{ old('fecha_nacimiento', $admision->estudiante->fecha_nacimiento) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                        <input type="text" name="ciudad"
                               value="{{ old('ciudad', $admision->estudiante->ciudad) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Colegio de Procedencia</label>
                    <input type="text" name="colegio_procedencia"
                           value="{{ old('colegio_procedencia', $admision->estudiante->colegio_procedencia) }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="titulo_bachiller" value="0">
                    <input type="checkbox" id="titulo" name="titulo_bachiller" value="1"
                           {{ old('titulo_bachiller', $admision->estudiante->titulo_bachiller) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600">
                    <label for="titulo" class="text-sm text-gray-700">Tiene título de bachiller</label>
                </div>
            </div>

            {{-- Datos de Admisión --}}
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4 border-b pb-2">Datos de Admisión</h3>

            <div class="grid grid-cols-1 gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carrera 1 (primera opción) <span class="text-red-500">*</span></label>
                        <select name="id_carrera1" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}"
                                    {{ old('id_carrera1', $admision->id_carrera1) == $carrera->id ? 'selected' : '' }}>
                                    {{ $carrera->sigla }} — {{ $carrera->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carrera 2 (segunda opción) <span class="text-red-500">*</span></label>
                        <select name="id_carrera2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}"
                                    {{ old('id_carrera2', $admision->id_carrera2) == $carrera->id ? 'selected' : '' }}>
                                    {{ $carrera->sigla }} — {{ $carrera->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                    <select name="estado" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        @foreach($estados as $estado)
                            <option value="{{ $estado }}"
                                {{ old('estado', $admision->estado) === $estado ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $estado)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded">
                    Guardar Cambios
                </button>
                <a href="{{ route('admin.postulantes.index') }}" class="text-sm text-gray-500 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
