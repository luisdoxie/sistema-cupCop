@extends('layouts.inscripcion')

@section('content')
<div class="bg-white rounded-2xl shadow-md p-8 max-w-2xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-800 mb-1">Carga de Documentos</h1>
    <p class="text-gray-500 text-sm mb-6">
        Suba los documentos requeridos. Formatos aceptados: PDF, JPG, PNG. Tamaño maximo: 5 MB por archivo.
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

    <form method="POST" action="{{ route('inscripcion.paso3.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @php
            $docsRequeridos = [
                'certificado_nacimiento' => 'Certificado de Nacimiento',
                'fotocopia_carnet'       => 'Fotocopia de Carnet de Identidad',
                'libreta_colegio'        => 'Libreta del Colegio',
            ];
            $docsOpcionales = [
                'titulo_bachiller' => 'Titulo de Bachiller (opcional)',
            ];
        @endphp

        <div class="space-y-4">
            <p class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Documentos obligatorios</p>

            @foreach($docsRequeridos as $campo => $etiqueta)
                <div x-data="{ fileName: '{{ isset($documentosSubidos[$campo]) ? basename($documentosSubidos[$campo]->ruta_archivo) : '' }}' }"
                     class="border rounded-xl p-4 @if(isset($documentosSubidos[$campo]) && $documentosSubidos[$campo]->estado_verificacion !== 'rechazado') border-green-300 bg-green-50 @else border-gray-200 @endif">

                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $etiqueta }} <span class="text-red-500">*</span>
                            </label>

                            @if(isset($documentosSubidos[$campo]))
                                <p class="text-xs text-green-700 mb-2">
                                    Ya subido &mdash; puede reemplazarlo subiendo uno nuevo.
                                    Estado: <span class="font-semibold">{{ $documentosSubidos[$campo]->estado_verificacion }}</span>
                                </p>
                                @if($documentosSubidos[$campo]->estado_verificacion === 'rechazado')
                                    <p class="text-xs text-red-600 mb-2">Motivo: {{ $documentosSubidos[$campo]->observacion }}</p>
                                @endif
                            @endif

                            <input type="file" name="{{ $campo }}"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                   class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer @error($campo) border border-red-400 rounded @enderror">

                            <p x-show="fileName" x-text="'Archivo seleccionado: ' + fileName"
                               class="text-xs text-gray-600 mt-1"></p>
                            @error($campo) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="space-y-4">
            <p class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Documentos opcionales</p>

            @foreach($docsOpcionales as $campo => $etiqueta)
                <div x-data="{ fileName: '{{ isset($documentosSubidos[$campo]) ? basename($documentosSubidos[$campo]->ruta_archivo) : '' }}' }"
                     class="border rounded-xl p-4 border-gray-200">

                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $etiqueta }}</label>

                    @if(isset($documentosSubidos[$campo]))
                        <p class="text-xs text-green-700 mb-2">
                            Ya subido. Estado: <span class="font-semibold">{{ $documentosSubidos[$campo]->estado_verificacion }}</span>
                        </p>
                    @endif

                    <input type="file" name="{{ $campo }}"
                           accept=".pdf,.jpg,.jpeg,.png"
                           @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                           class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 cursor-pointer">

                    <p x-show="fileName" x-text="'Archivo seleccionado: ' + fileName"
                       class="text-xs text-gray-600 mt-1"></p>
                    @error($campo) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">
                Continuar al Paso 4 &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
