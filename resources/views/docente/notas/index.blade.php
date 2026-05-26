@extends('layouts.docente')
@section('title', 'Mis Grupos — Notas')
@section('page-title', 'Mis Grupos')

@section('content')
<div class="space-y-4">
    <p class="text-sm text-gray-500">Selecciona un grupo para ingresar o ver las notas.</p>

    @if($grupos->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
        No tienes grupos asignados actualmente.
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($grupos as $grupo)
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-bold text-gray-800 text-lg">{{ $grupo->nombre }}</p>
                    <p class="text-sm text-gray-500">{{ $grupo->gestion->nombre ?? '—' }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ ucfirst($grupo->modalidad ?? '') }}</p>
                </div>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded">
                    {{ $grupo->materiaGrupos->count() }} materia(s)
                </span>
            </div>

            <div class="mt-3 space-y-1">
                @foreach($grupo->materiaGrupos as $mg)
                <p class="text-xs text-gray-600">• {{ $mg->materia->nombre }}</p>
                @endforeach
            </div>

            <a href="{{ route('docente.notas.planilla', $grupo) }}"
               class="mt-4 block text-center bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded transition-colors">
                Ir a Planilla de Notas
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
