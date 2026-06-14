@extends(auth()->user()->esAdmin() ? 'layouts.admin' : 'layouts.coordinador')

@section('title', 'Documentos del Postulante')
@section('page-title', 'Documentos del Postulante')

@section('content')
<div class="max-w-3xl space-y-5">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    <a href="{{ route('admin.documentos.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Volver</a>

    {{-- Info del postulante --}}
    <div class="bg-white rounded-lg shadow p-5">
        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Postulante</h3>
        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-gray-500">Nombre</dt>
                <dd class="font-medium">{{ $admision->estudiante->persona->nombre ?? '—' }} {{ $admision->estudiante->persona->apellido ?? '' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">CI</dt>
                <dd>{{ $admision->estudiante->persona->ci ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Gestión</dt>
                <dd>{{ $admision->gestion->nombre ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Estado Admisión</dt>
                <dd>
                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full">
                        {{ str_replace('_', ' ', $admision->estado) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    {{-- Documentos --}}
    <div class="bg-white rounded-lg shadow p-5">
        <h3 class="font-semibold text-gray-700 mb-4 border-b pb-2">Documentos</h3>

        @forelse($admision->documentos as $doc)
        <div class="border rounded-lg p-4 mb-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-800 capitalize">
                        {{ str_replace('_', ' ', $doc->tipo_documento) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Entregado: {{ $doc->fecha_entrega ? \Carbon\Carbon::parse($doc->fecha_entrega)->format('d/m/Y') : '—' }}
                    </p>
                    @if($doc->observacion)
                        <p class="text-xs text-red-600 mt-1">Observación: {{ $doc->observacion }}</p>
                    @endif
                </div>
                <div class="text-right">
                    @if($doc->estado_verificacion === 'verificado')
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Verificado</span>
                    @elseif($doc->estado_verificacion === 'rechazado')
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Rechazado</span>
                    @else
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Pendiente</span>
                    @endif
                </div>
            </div>

            {{-- Link al archivo --}}
            @if($doc->ruta_archivo)
            <div class="mt-2">
                <a href="{{ Storage::url($doc->ruta_archivo) }}" target="_blank"
                   class="text-blue-600 text-xs hover:underline">Ver documento</a>
            </div>
            @endif

            {{-- Acciones --}}
            @if($doc->estado_verificacion === 'pendiente')
            <div class="mt-3 flex items-start gap-3" x-data="{ rechazando: false }">
                <form method="POST" action="{{ route('admin.documentos.verificar', $doc) }}">
                    @csrf
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded">
                        Verificar
                    </button>
                </form>
                <button @click="rechazando = !rechazando" type="button"
                        class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded">
                    Rechazar
                </button>
                <div x-show="rechazando" x-cloak class="flex-1">
                    <form method="POST" action="{{ route('admin.documentos.rechazar', $doc) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="observacion" placeholder="Motivo del rechazo..."
                               class="border border-gray-300 rounded px-2 py-1 text-xs flex-1" required>
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded">
                            Confirmar
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
        @empty
        <p class="text-sm text-gray-400">No hay documentos registrados para esta admisión.</p>
        @endforelse
    </div>
</div>
@endsection
