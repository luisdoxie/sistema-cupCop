@extends('layouts.inscripcion')

@section('content')
<div class="bg-white rounded-2xl shadow-md p-10 max-w-lg mx-auto text-center">

    <!-- Icono exito -->
    <div class="flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mx-auto mb-6">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Pago Recibido</h1>
    <p class="text-gray-500 text-sm mb-6">
        Su pago fue procesado correctamente. Estamos verificando la transaccion con Stripe.
        Recibird un correo de confirmacion en breve.
    </p>

    @if($admision)
        <div class="bg-gray-50 rounded-xl p-4 text-left text-sm mb-6">
            <p class="text-gray-500">N de admision: <span class="font-bold text-gray-800">#{{ $admision->id }}</span></p>
            <p class="text-gray-500 mt-1">Estado: <span class="font-bold text-yellow-600">{{ ucfirst(str_replace('_', ' ', $admision->estado)) }}</span></p>
        </div>
    @endif

    <a href="{{ route('inscripcion.paso5') }}"
       class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors mb-3">
        Ver Confirmacion Final &rarr;
    </a>

    <p class="text-xs text-gray-400">
        Si el pago no se refleja inmediatamente, espere unos minutos y recargue la pagina.
    </p>
</div>
@endsection
