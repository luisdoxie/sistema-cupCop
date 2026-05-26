@extends('layouts.admin')

@section('title', 'Configuración del Sistema')
@section('page-title', 'Configuración del Sistema')

@section('content')
<div class="max-w-2xl">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.configuracion.update') }}">
            @csrf

            <div class="space-y-4">
                @forelse($configs as $config)
                <div class="border-b pb-4 last:border-b-0 last:pb-0">
                    <label class="block text-sm font-medium text-gray-800 mb-1">
                        {{ $config->descripcion ?? $config->clave }}
                    </label>
                    <p class="text-xs text-gray-400 mb-1">Clave: <code class="bg-gray-100 px-1 rounded">{{ $config->clave }}</code></p>
                    <input type="text" name="configs[{{ $config->clave }}]" value="{{ old("configs.{$config->clave}", $config->valor) }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @empty
                <p class="text-sm text-gray-400">No hay configuraciones registradas.</p>
                @endforelse
            </div>

            @if($configs->isNotEmpty())
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded">
                    Guardar Configuración
                </button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
