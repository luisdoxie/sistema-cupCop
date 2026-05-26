@extends('layouts.inscripcion')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Encabezado exito -->
    <div class="bg-white rounded-2xl shadow-md p-8 text-center">
        <div class="flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mx-auto mb-4">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Inscripcion Completada</h1>
        <p class="text-gray-500 text-sm">
            Su proceso de inscripcion ha sido registrado exitosamente.
            Se ha enviado un correo de confirmacion a <strong>{{ Auth::user()->correo }}</strong>.
        </p>
    </div>

    <!-- Detalle admision -->
    <div class="bg-white rounded-2xl shadow-md p-8">
        <h2 class="text-lg font-bold text-gray-700 mb-4">Detalle de su Admision</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="bg-blue-50 rounded-xl p-4">
                <p class="text-xs text-blue-400 uppercase font-semibold mb-2">N de Admision</p>
                <p class="text-2xl font-bold text-blue-700">#{{ $admision->id }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 uppercase font-semibold mb-2">Estado</p>
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                    @if(in_array($admision->estado, ['pago_pendiente', 'documentos_pendientes'])) bg-yellow-100 text-yellow-700
                    @elseif($admision->estado === 'inscrito') bg-blue-100 text-blue-700
                    @elseif($admision->estado === 'cursando') bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-700 @endif">
                    {{ ucfirst(str_replace('_', ' ', $admision->estado)) }}
                </span>
            </div>
        </div>

        <div class="mt-5 space-y-3 text-sm">
            <div class="flex items-center space-x-2">
                <span class="text-gray-500 w-28">Estudiante:</span>
                <span class="font-medium">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-gray-500 w-28">CI:</span>
                <span class="font-medium">{{ Auth::user()->ci }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-gray-500 w-28">Correo:</span>
                <span class="font-medium">{{ Auth::user()->correo }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-gray-500 w-28">Gestion:</span>
                <span class="font-medium">{{ $admision->gestion->nombre ?? 'N/A' }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-gray-500 w-28">Fecha:</span>
                <span class="font-medium">{{ \Carbon\Carbon::parse($admision->fecha)->format('d/m/Y') }}</span>
            </div>
        </div>

        <!-- Carreras -->
        <div class="mt-5 bg-blue-50 rounded-xl p-4">
            <p class="text-xs text-blue-400 uppercase font-semibold mb-3">Carreras</p>
            <div class="space-y-2">
                <div class="flex items-center space-x-3">
                    <span class="bg-blue-600 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">1</span>
                    <span class="font-medium text-sm">{{ $admision->carrera1->nombre ?? 'N/A' }}</span>
                    <span class="text-xs text-blue-500">({{ $admision->carrera1->sigla ?? '' }})</span>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="bg-blue-400 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">2</span>
                    <span class="font-medium text-sm">{{ $admision->carrera2->nombre ?? 'N/A' }}</span>
                    <span class="text-xs text-blue-500">({{ $admision->carrera2->sigla ?? '' }})</span>
                </div>
            </div>
        </div>

        <!-- Documentos -->
        @if($admision->documentos->count() > 0)
        <div class="mt-5">
            <p class="text-xs text-gray-400 uppercase font-semibold mb-3">Documentos Enviados</p>
            <ul class="space-y-2">
                @foreach($admision->documentos as $doc)
                    <li class="flex items-center justify-between text-sm bg-gray-50 rounded-lg px-3 py-2">
                        <span class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $doc->tipo_documento)) }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            @if($doc->estado_verificacion === 'verificado') bg-green-100 text-green-700
                            @elseif($doc->estado_verificacion === 'rechazado') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ $doc->estado_verificacion }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Pago -->
        @if($admision->pago)
        <div class="mt-5 bg-green-50 rounded-xl p-4">
            <p class="text-xs text-green-400 uppercase font-semibold mb-3">Informacion de Pago</p>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Monto pagado:</span>
                    <span class="font-bold text-green-700">${{ number_format($admision->pago->monto, 2) }} USD</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Estado:</span>
                    <span class="font-medium">{{ ucfirst($admision->pago->estado_pago) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Referencia:</span>
                    <span class="font-mono text-xs text-gray-600 truncate max-w-xs">{{ $admision->pago->referencia_transaccion }}</span>
                </div>
                @if($admision->pago->fecha_pago)
                <div class="flex justify-between">
                    <span class="text-gray-500">Fecha de pago:</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($admision->pago->fecha_pago)->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Proximos pasos -->
    <div class="bg-white rounded-2xl shadow-md p-8">
        <h2 class="text-lg font-bold text-gray-700 mb-4">Proximos Pasos</h2>
        <ol class="space-y-3 text-sm">
            <li class="flex items-start space-x-3">
                <span class="bg-blue-100 text-blue-700 text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center mt-0.5 flex-shrink-0">1</span>
                <span class="text-gray-600">Espere la verificacion de sus documentos por parte de la oficina de admisiones.</span>
            </li>
            <li class="flex items-start space-x-3">
                <span class="bg-blue-100 text-blue-700 text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center mt-0.5 flex-shrink-0">2</span>
                <span class="text-gray-600">Se le notificara por correo el resultado del proceso de admision.</span>
            </li>
            <li class="flex items-start space-x-3">
                <span class="bg-blue-100 text-blue-700 text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center mt-0.5 flex-shrink-0">3</span>
                <span class="text-gray-600">Una vez admitido, podra acceder al portal estudiantil para ver su horario y materias.</span>
            </li>
        </ol>
    </div>

    <!-- Boton portal -->
    <div class="text-center pb-4">
        <a href="{{ route('estudiante.dashboard') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
            Ir al Portal Estudiantil
        </a>
    </div>
</div>
@endsection
