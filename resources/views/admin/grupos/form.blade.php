@extends('layouts.admin')

@section('title', 'Nuevo Grupo')
@section('page-title', 'Crear Grupo')

@section('content')
<div class="max-w-lg">

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if(isset($gestionActiva))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 text-sm px-4 py-2 rounded mb-4">
            Gestión activa: <strong>{{ $gestionActiva->nombre }}</strong>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.grupos.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Grupo <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                           placeholder="Ej: Grupo A" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paralelo <span class="text-red-500">*</span></label>
                    <input type="text" name="paralelo" value="{{ old('paralelo') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                           placeholder="Ej: A" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad <span class="text-red-500">*</span></label>
                    <select name="modalidad" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="">Seleccionar...</option>
                        <option value="presencial" {{ old('modalidad') === 'presencial' ? 'selected' : '' }}>Presencial</option>
                        <option value="virtual"    {{ old('modalidad') === 'virtual'    ? 'selected' : '' }}>Virtual</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cupo Máximo <span class="text-red-500">*</span></label>
                    <input type="number" name="cupo_maximo" value="{{ old('cupo_maximo', 30) }}"
                           min="1" max="80"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    <p class="text-xs text-gray-400 mt-1">Entre 1 y 80 estudiantes.</p>
                </div>

            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded">
                    Crear Grupo
                </button>
                <a href="{{ route('admin.grupos.index') }}" class="text-sm text-gray-500 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
