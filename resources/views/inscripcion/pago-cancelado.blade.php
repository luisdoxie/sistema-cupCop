@extends('layouts.inscripcion')

@section('content')
<div class="bg-white rounded-2xl shadow-md p-10 max-w-lg mx-auto text-center">

    <!-- Icono error -->
    <div class="flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mx-auto mb-6">
        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Pago Cancelado</h1>
    <p class="text-gray-500 text-sm mb-6">
        El proceso de pago fue cancelado o interrumpido. No se realizo ningun cargo a su tarjeta.
        Puede intentarlo nuevamente cuando desee.
    </p>

    <div class="space-y-3">
        <a href="{{ route('inscripcion.paso4.create') }}"
           class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">
            Reintentar Pago
        </a>
        <a href="{{ route('inscripcion.paso3.create') }}"
           class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 rounded-lg transition-colors">
            Volver a Documentos
        </a>
    </div>

    <p class="text-xs text-gray-400 mt-6">
        Si tiene problemas con el pago, contacte a la oficina de admisiones.
    </p>
</div>
@endsection
