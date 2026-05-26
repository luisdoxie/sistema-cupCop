@extends('layouts.admin')

@section('title', 'Resultado de Importación')
@section('page-title', 'Resultado de Importación')

@section('content')
<div class="max-w-3xl space-y-5">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.carga-masiva.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Volver a Carga Masiva</a>

    {{-- Resumen --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-700 mb-4 border-b pb-2">Resumen del Lote</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Archivo</dt><dd class="font-medium">{{ $lote->nombre_archivo }}</dd></div>
            <div><dt class="text-gray-500">Tipo</dt><dd class="capitalize">{{ $lote->tipo_usuario }}</dd></div>
            <div><dt class="text-gray-500">Fecha de subida</dt>
                <dd>{{ \Carbon\Carbon::parse($lote->fecha_subida)->format('d/m/Y H:i') }}</dd>
            </div>
            <div><dt class="text-gray-500">Fecha de proceso</dt>
                <dd>{{ $lote->fecha_proceso ? \Carbon\Carbon::parse($lote->fecha_proceso)->format('d/m/Y H:i') : '—' }}</dd>
            </div>
            <div><dt class="text-gray-500">Estado</dt>
                <dd>
                    @if($lote->estado === 'completado')
                        <span class="bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded-full">Completado</span>
                    @elseif($lote->estado === 'con_errores')
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-0.5 rounded-full">Con errores</span>
                    @elseif($lote->estado === 'procesando')
                        <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full">Procesando...</span>
                    @else
                        <span class="bg-gray-100 text-gray-800 text-xs px-2.5 py-0.5 rounded-full">Pendiente</span>
                    @endif
                </dd>
            </div>
        </dl>

        {{-- Barra de progreso --}}
        @if($lote->total_registros > 0)
        <div class="mt-5">
            @php $pct = round($lote->exitosos / $lote->total_registros * 100); @endphp
            <div class="flex justify-between text-xs text-gray-600 mb-1">
                <span>{{ $lote->exitosos }} exitosos de {{ $lote->total_registros }}</span>
                <span>{{ $pct }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-green-500 h-3 rounded-full" style="width: {{ $pct }}%"></div>
            </div>
        </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4 mt-5">
            <div class="text-center bg-gray-50 rounded p-3">
                <p class="text-2xl font-bold text-gray-700">{{ $lote->total_registros }}</p>
                <p class="text-xs text-gray-500 mt-1">Total registros</p>
            </div>
            <div class="text-center bg-green-50 rounded p-3">
                <p class="text-2xl font-bold text-green-600">{{ $lote->exitosos }}</p>
                <p class="text-xs text-gray-500 mt-1">Exitosos</p>
            </div>
            <div class="text-center bg-red-50 rounded p-3">
                <p class="text-2xl font-bold text-red-600">{{ $lote->fallidos }}</p>
                <p class="text-xs text-gray-500 mt-1">Fallidos</p>
            </div>
        </div>
    </div>

    {{-- Errores --}}
    @if(!empty($errores))
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-red-700 mb-4 border-b pb-2">Errores Encontrados ({{ count($errores) }})</h3>
        <div class="overflow-y-auto max-h-96">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Fila</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($errores as $e)
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">
                            {{ $e['fila'] > 0 ? "Fila {$e['fila']}" : 'General' }}
                        </td>
                        <td class="px-4 py-2 text-red-600">{{ $e['error'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Refresh si está procesando --}}
    @if(in_array($lote->estado, ['pendiente', 'procesando']))
    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded flex items-center gap-3">
        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span class="text-sm">El archivo está siendo procesado. Esta página se actualizará automáticamente.</span>
    </div>
    <script>setTimeout(() => location.reload(), 5000);</script>
    @endif

</div>
@endsection
