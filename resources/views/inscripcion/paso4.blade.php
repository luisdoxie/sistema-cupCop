@extends('layouts.inscripcion')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Resumen de inscripcion -->
    <div class="bg-white rounded-2xl shadow-md p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">Resumen y Pago</h1>
        <p class="text-gray-500 text-sm mb-6">Revise su informacion antes de proceder con el pago.</p>

        <div class="space-y-4">
            <!-- Datos personales -->
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Datos del Estudiante</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-500">Nombre:</span>
                        <span class="font-medium ml-1">
                            {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500">CI:</span>
                        <span class="font-medium ml-1">{{ Auth::user()->ci }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Correo:</span>
                        <span class="font-medium ml-1">{{ Auth::user()->correo }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">N Admision:</span>
                        <span class="font-medium ml-1 text-blue-700">#{{ $admision->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Carreras -->
            <div class="bg-blue-50 rounded-xl p-4">
                <p class="text-xs font-semibold text-blue-400 uppercase tracking-wide mb-3">Carreras Seleccionadas</p>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center space-x-2">
                        <span class="bg-blue-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">1</span>
                        <span class="font-medium">{{ $admision->carrera1->nombre ?? 'N/A' }}</span>
                        <span class="text-blue-500 text-xs">({{ $admision->carrera1->sigla ?? '' }})</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="bg-blue-400 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">2</span>
                        <span class="font-medium">{{ $admision->carrera2->nombre ?? 'N/A' }}</span>
                        <span class="text-blue-500 text-xs">({{ $admision->carrera2->sigla ?? '' }})</span>
                    </div>
                </div>
            </div>

            <!-- Documentos -->
            @if($admision->documentos->count() > 0)
            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-xs font-semibold text-green-500 uppercase tracking-wide mb-3">Documentos Subidos</p>
                <ul class="space-y-1">
                    @foreach($admision->documentos as $doc)
                        <li class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $doc->tipo_documento)) }}</span>
                            <span class="ml-auto text-xs text-gray-400">{{ $doc->estado_verificacion }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>

    <!-- Monto y boton de pago -->
    <div class="bg-white rounded-2xl shadow-md p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm text-gray-500">Monto de inscripcion</p>
                <p class="text-3xl font-bold text-gray-800">${{ number_format($monto, 2) }} <span class="text-base font-normal text-gray-400">USD</span></p>
            </div>
            <div class="bg-blue-50 rounded-xl p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>

        @if($admision->pago && $admision->pago->estado_pago === 'completado')
            <div class="bg-green-50 border border-green-300 rounded-lg p-4 mb-4 text-sm text-green-700">
                Pago ya registrado. Referencia: {{ $admision->pago->referencia_transaccion }}
            </div>
            <a href="{{ route('inscripcion.paso5') }}"
               class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition-colors">
                Ver Confirmacion Final &rarr;
            </a>
        @else
            <form method="POST" action="{{ route('inscripcion.paso4.pagar') }}">
                @csrf
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition-colors flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zm0 4v8h16V8H4zm2 5h4v2H6v-2zm6 0h6v2h-6v-2z"/>
                    </svg>
                    <span>Pagar con Stripe &mdash; ${{ number_format($monto, 2) }} USD</span>
                </button>
            </form>
            <p class="text-xs text-center text-gray-400 mt-3">
                Sera redirigido al portal seguro de Stripe para completar el pago.
            </p>
        @endif
    </div>
</div>
@endsection
