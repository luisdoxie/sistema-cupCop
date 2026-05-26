@extends('layouts.admin')

@section('title', isset($aula) ? 'Editar Aula' : 'Nueva Aula')
@section('page-title', isset($aula) ? 'Editar Aula' : 'Nueva Aula')

@section('content')
<div class="max-w-lg">

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST"
              action="{{ isset($aula) ? route('admin.aulas.update', $aula) : route('admin.aulas.store') }}">
            @csrf
            @if(isset($aula)) @method('PUT') @endif

            <div class="grid grid-cols-1 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Piso <span class="text-red-500">*</span></label>
                    <select name="id_piso" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="">Seleccionar piso...</option>
                        @foreach($pisos as $piso)
                        <option value="{{ $piso->id }}"
                                {{ old('id_piso', $aula->id_piso ?? '') == $piso->id ? 'selected' : '' }}>
                            Piso {{ $piso->nombre ?? $piso->id }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número / Identificador <span class="text-red-500">*</span></label>
                    <input type="text" name="numero" value="{{ old('numero', $aula->numero ?? '') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                           placeholder="Ej: 101" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad <span class="text-red-500">*</span></label>
                    <input type="number" name="capacidad" value="{{ old('capacidad', $aula->capacidad ?? 30) }}"
                           min="1" max="500"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <input type="text" name="tipo" value="{{ old('tipo', $aula->tipo ?? '') }}"
                           placeholder="Ej: Laboratorio, Salón, Sala de cómputo"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad <span class="text-red-500">*</span></label>
                    <select name="modalidad" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="presencial" {{ old('modalidad', $aula->modalidad ?? '') === 'presencial' ? 'selected' : '' }}>Presencial</option>
                        <option value="virtual"    {{ old('modalidad', $aula->modalidad ?? '') === 'virtual'    ? 'selected' : '' }}>Virtual</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                    <select name="estado" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="disponible"    {{ old('estado', $aula->estado ?? 'disponible') === 'disponible'    ? 'selected' : '' }}>Disponible</option>
                        <option value="ocupada"       {{ old('estado', $aula->estado ?? '') === 'ocupada'       ? 'selected' : '' }}>Ocupada</option>
                        <option value="mantenimiento" {{ old('estado', $aula->estado ?? '') === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    </select>
                </div>

            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded">
                    {{ isset($aula) ? 'Actualizar' : 'Crear Aula' }}
                </button>
                <a href="{{ route('admin.aulas.index') }}" class="text-sm text-gray-500 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
