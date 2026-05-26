@extends('layouts.coordinador')

@section('title', 'Dashboard - Coordinador')
@section('page-title', 'Dashboard Coordinador')

@section('content')
@if($gestionActiva)
<div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <span class="text-blue-800 font-medium">Gestión activa: {{ $gestionActiva->nombre }}</span>
</div>
@else
<div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <span class="text-yellow-800">No hay gestión activa.</span>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500 mb-1">Total Postulantes</p>
        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_postulantes'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500 mb-1">Grupos Habilitados</p>
        <p class="text-3xl font-bold text-gray-800">{{ $stats['grupos_habilitados'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <p class="text-sm text-gray-500 mb-1">Admitidos</p>
        <p class="text-3xl font-bold text-gray-800">{{ $stats['admitidos'] }}</p>
    </div>
</div>
@endsection
