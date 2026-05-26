@extends('layouts.admin')

@section('title', 'Detalle Docente')
@section('page-title', 'Detalle del Docente')

@section('content')
<div class="space-y-6 max-w-4xl">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.docentes.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Volver al listado</a>
        <a href="{{ route('admin.docentes.edit', $docente) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">Editar</a>
    </div>

    {{-- Info personal --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Información Personal</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">CI</dt><dd class="font-medium">{{ $docente->persona->ci }}</dd></div>
            <div><dt class="text-gray-500">Nombre</dt><dd class="font-medium">{{ $docente->persona->nombre }} {{ $docente->persona->apellido }}</dd></div>
            <div><dt class="text-gray-500">Sexo</dt><dd>{{ $docente->persona->sexo === 'M' ? 'Masculino' : 'Femenino' }}</dd></div>
            <div><dt class="text-gray-500">Correo</dt><dd>{{ $docente->persona->correo ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Teléfono</dt><dd>{{ $docente->persona->telefono ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Dirección</dt><dd>{{ $docente->persona->direccion ?? '—' }}</dd></div>
        </dl>
    </div>

    {{-- Info académica --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Información Académica</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Especialidad</dt><dd class="font-medium">{{ $docente->especialidad }}</dd></div>
            <div><dt class="text-gray-500">Grado Académico</dt><dd>{{ $docente->grado_academico }}</dd></div>
            <div><dt class="text-gray-500">Diplomado Educación</dt>
                <dd>
                    @if($docente->diplomado_educacion)
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">Sí</span>
                    @else
                        <span class="bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full">No</span>
                    @endif
                </dd>
            </div>
            <div><dt class="text-gray-500">Años de Experiencia</dt><dd>{{ $docente->anios_experiencia }}</dd></div>
            <div><dt class="text-gray-500">Máx. Grupos</dt><dd>{{ $docente->max_grupos }}</dd></div>
            <div><dt class="text-gray-500">Estado</dt>
                <dd>
                    @if($docente->estado === 'activo')
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">Activo</span>
                    @else
                        <span class="bg-gray-100 text-gray-800 text-xs px-2 py-0.5 rounded-full">Inactivo</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    {{-- Asignaciones --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Asignaciones Académicas</h3>
        @forelse($asignaciones as $asig)
        <div class="mb-4 border rounded p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="font-medium text-sm">
                    {{ $asig->materiaGrupo->materia->nombre ?? '—' }}
                    — Grupo: {{ $asig->materiaGrupo->grupo->nombre ?? '—' }}
                    ({{ $asig->materiaGrupo->grupo->paralelo ?? '' }})
                </span>
                <span class="text-xs text-gray-500">Turno: {{ $asig->materiaGrupo->turno->nombre ?? '—' }}</span>
            </div>
            @if($asig->bloquesHorario->isNotEmpty())
            <div class="mt-2">
                <p class="text-xs text-gray-500 mb-1">Bloques horarios:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($asig->bloquesHorario as $bloque)
                    <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded">
                        {{ ucfirst($bloque->dia) }}: {{ $bloque->hora_inicio }} - {{ $bloque->hora_fin }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @empty
        <p class="text-sm text-gray-400">Sin asignaciones registradas.</p>
        @endforelse
    </div>

</div>
@endsection
