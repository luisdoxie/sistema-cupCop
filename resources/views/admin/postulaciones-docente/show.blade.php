@extends('layouts.admin')
@section('title', 'Postulación — ' . $postulacion->nombre . ' ' . $postulacion->apellido)
@section('page-title', 'Detalle de Postulación Docente')

@section('content')
<div class="max-w-3xl space-y-6">

    <a href="{{ route('admin.postulaciones-docente.index') }}"
       class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver a postulaciones
    </a>

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
    @endif

    {{-- Cabecera con estado --}}
    <div class="flex flex-wrap items-center gap-3">
        <h2 class="text-xl font-bold text-gray-800">{{ $postulacion->nombre }} {{ $postulacion->apellido }}</h2>
        @if($postulacion->estado === 'pendiente')
            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full font-medium">Pendiente</span>
        @elseif($postulacion->estado === 'aprobada')
            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">Aprobada</span>
        @else
            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-medium">Rechazada</span>
        @endif
    </div>

    {{-- Datos personales --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4 pb-2 border-b">Datos Personales</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-400 text-xs mb-0.5">CI</p>
                <p class="font-medium text-gray-800">{{ $postulacion->ci }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-0.5">Sexo</p>
                <p class="font-medium text-gray-800">{{ $postulacion->sexo === 'M' ? 'Masculino' : 'Femenino' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-0.5">Correo</p>
                <p class="font-medium text-gray-800">{{ $postulacion->correo ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-0.5">Teléfono</p>
                <p class="font-medium text-gray-800">{{ $postulacion->telefono ?? '—' }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-gray-400 text-xs mb-0.5">Dirección</p>
                <p class="font-medium text-gray-800">{{ $postulacion->direccion ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Perfil profesional --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4 pb-2 border-b">Perfil Profesional</h3>
        <div class="grid grid-cols-2 gap-4 text-sm mb-5">
            <div>
                <p class="text-gray-400 text-xs mb-0.5">Grado Académico</p>
                <p class="font-medium text-gray-800">{{ $postulacion->grado_academico ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-0.5">Años de Experiencia</p>
                <p class="font-medium text-gray-800">{{ $postulacion->anios_experiencia }} años</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-0.5">Diplomado en Educación</p>
                <p class="font-medium text-gray-800">{{ $postulacion->diplomado_educacion ? 'Sí' : 'No' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-0.5">CV</p>
                @if($postulacion->cv_path)
                    <span class="inline-flex items-center gap-1 text-green-600 font-medium text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Adjunto
                    </span>
                @else
                    <p class="text-gray-400 text-sm">No adjunto</p>
                @endif
            </div>
        </div>

        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Materias Solicitadas</h4>
        <div class="flex flex-wrap gap-2">
            @foreach($postulacion->materias as $materia)
            <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full font-medium">
                {{ $materia->nombre }}
            </span>
            @endforeach
        </div>
    </div>

    {{-- Resolución (si ya fue procesada) --}}
    @if($postulacion->estado !== 'pendiente')
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4 pb-2 border-b">Resolución</h3>
        <div class="text-sm space-y-2">
            <p>
                <span class="text-gray-400">Procesada por:</span>
                <span class="font-medium text-gray-800 ml-1">
                    {{ $postulacion->aprobadoPor?->nombre }} {{ $postulacion->aprobadoPor?->apellido }}
                </span>
            </p>
            <p>
                <span class="text-gray-400">Fecha:</span>
                <span class="font-medium text-gray-800 ml-1">
                    {{ $postulacion->aprobado_en?->format('d/m/Y H:i') }}
                </span>
            </p>
            @if($postulacion->observacion)
            <p>
                <span class="text-gray-400">Observación:</span>
                <span class="font-medium text-gray-800 ml-1">{{ $postulacion->observacion }}</span>
            </p>
            @endif
        </div>
    </div>
    @endif

    {{-- Acciones (solo si pendiente) --}}
    @if($postulacion->estado === 'pendiente')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Aprobar --}}
        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
            <h3 class="text-base font-semibold text-gray-800 mb-1">Aprobar Postulación</h3>
            <p class="text-xs text-gray-500 mb-4">Se creará automáticamente la cuenta del docente.</p>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded p-3 mb-4">
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.postulaciones-docente.aprobar', $postulacion) }}">
                @csrf

                <p class="text-xs font-medium text-gray-600 mb-2">Materias a habilitar:</p>
                <div class="space-y-2 mb-4">
                    @foreach($postulacion->materias as $materia)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="materias_aprobadas[]" value="{{ $materia->id }}" checked
                               class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">{{ $materia->nombre }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Máx. grupos asignables</label>
                    <select name="max_grupos"
                            class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                        @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ $i === 4 ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Observación (opcional)</label>
                    <textarea name="observacion" rows="2" placeholder="Notas internas..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm resize-none focus:ring-2 focus:ring-green-400 focus:outline-none"></textarea>
                </div>

                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                    Aprobar y Crear Cuenta
                </button>
            </form>
        </div>

        {{-- Rechazar --}}
        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-red-400">
            <h3 class="text-base font-semibold text-gray-800 mb-1">Rechazar Postulación</h3>
            <p class="text-xs text-gray-500 mb-4">El motivo quedará registrado en el sistema.</p>

            <form method="POST" action="{{ route('admin.postulaciones-docente.rechazar', $postulacion) }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Motivo del rechazo <span class="text-red-500">*</span>
                    </label>
                    <textarea name="observacion" rows="5" required
                              placeholder="Explica el motivo del rechazo..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm resize-none focus:ring-2 focus:ring-red-400 focus:outline-none"></textarea>
                </div>

                <button type="submit"
                        onclick="return confirm('¿Confirmas el rechazo de esta postulación?')"
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                    Rechazar Postulación
                </button>
            </form>
        </div>

    </div>
    @endif

</div>
@endsection
