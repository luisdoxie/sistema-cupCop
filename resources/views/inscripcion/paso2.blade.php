@extends('layouts.inscripcion')

@section('content')
<div class="bg-white rounded-2xl shadow-md p-8 max-w-2xl mx-auto"
     x-data="{
         carrera1: '{{ old('id_carrera1', '') }}',
         carrera2: '{{ old('id_carrera2', '') }}',
         get carrerasParaOpcion2() {
             return this.carrera1 !== '';
         }
     }">

    <h1 class="text-2xl font-bold text-gray-800 mb-1">Seleccion de Carreras</h1>
    <p class="text-gray-500 text-sm mb-6">
        Elija dos opciones de carrera en orden de preferencia.
        @if($gestion)
            Gestion: <span class="font-semibold text-blue-700">{{ $gestion->nombre }}</span>
        @endif
    </p>

    @if($errors->any())
        <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
            <p class="font-semibold text-red-700 text-sm mb-2">Corrija los siguientes errores:</p>
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($carreras->isEmpty())
        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-6 text-center">
            <svg class="w-12 h-12 mx-auto text-yellow-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-yellow-700 font-medium">No hay carreras disponibles con cupos en este momento.</p>
            <p class="text-yellow-600 text-sm mt-1">Consulte con la oficina de admisiones.</p>
        </div>
    @else
        <form method="POST" action="{{ route('inscripcion.paso2.store') }}" class="space-y-6">
            @csrf

            <!-- Primera opcion -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Primera opcion de carrera <span class="text-red-500">*</span>
                </label>
                <select name="id_carrera1" required
                        x-model="carrera1"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('id_carrera1') border-red-400 @enderror">
                    <option value="">-- Seleccione primera opcion --</option>
                    @foreach($carreras as $carrera)
                        <option value="{{ $carrera->id }}" {{ old('id_carrera1') == $carrera->id ? 'selected' : '' }}>
                            {{ $carrera->nombre }} ({{ $carrera->sigla }})
                        </option>
                    @endforeach
                </select>
                @error('id_carrera1') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Segunda opcion -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Segunda opcion de carrera <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal">(debe ser diferente a la primera)</span>
                </label>
                <select name="id_carrera2" required
                        x-model="carrera2"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('id_carrera2') border-red-400 @enderror">
                    <option value="">-- Seleccione segunda opcion --</option>
                    @foreach($carreras as $carrera)
                        <option value="{{ $carrera->id }}"
                                x-show="String(carrera1) !== '{{ $carrera->id }}'"
                                {{ old('id_carrera2') == $carrera->id ? 'selected' : '' }}>
                            {{ $carrera->nombre }} ({{ $carrera->sigla }})
                        </option>
                    @endforeach
                </select>
                @error('id_carrera2') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Info carreras disponibles -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
                <p class="font-semibold mb-1">Carreras disponibles con cupos:</p>
                <ul class="space-y-1">
                    @foreach($carreras as $carrera)
                        @php
                            $cg = \App\Models\CarreraGestion::where('id_gestion', $gestion->id)
                                    ->where('id_carrera', $carrera->id)
                                    ->first();
                        @endphp
                        <li class="flex justify-between">
                            <span>{{ $carrera->nombre }}</span>
                            <span class="bg-blue-100 px-2 rounded text-xs">{{ $cg->cupo_disponible ?? '?' }} cupos</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">
                Continuar al Paso 3 &rarr;
            </button>
        </form>
    @endif
</div>
@endsection
