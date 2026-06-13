@extends('layouts.estudiante')

@section('title', 'Dashboard - Estudiante')
@section('page-title', 'Mi Portal')

@section('content')
{{-- Estado de admisión --}}
<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Estado de Admisión</h2>
    @if($admision)
        @php
            $colores = [
                'inscrito'           => 'blue',
                'documentos_pendientes'=> 'yellow',
                'pago_pendiente'     => 'orange',
                'cursando'           => 'indigo',
                'aprobado'           => 'green',
                'admitido_carrera1'  => 'green',
                'admitido_carrera2'  => 'green',
                'no_admitido'        => 'red',
                'reprobado'          => 'red',
            ];
            $color = $colores[$admision->estado] ?? 'gray';
        @endphp
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1">
                    <p class="text-sm text-gray-500">Gestión</p>
                    <p class="font-semibold text-gray-800">{{ $gestion->nombre }}</p>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500">Carrera 1</p>
                    <p class="font-semibold text-gray-800">{{ $admision->carrera1->nombre ?? '-' }}</p>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500">Carrera 2</p>
                    <p class="font-semibold text-gray-800">{{ $admision->carrera2->nombre ?? '-' }}</p>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500">Grupo</p>
                    <p class="font-semibold text-gray-800">{{ $admision->grupo->nombre ?? 'Sin asignar' }}</p>
                </div>
                <div>
                    <span class="inline-block bg-{{ $color }}-100 text-{{ $color }}-800 text-sm font-semibold px-3 py-1 rounded-full capitalize">
                        {{ str_replace('_', ' ', $admision->estado) }}
                    </span>
                </div>
            </div>
            @if($admision->promedio_final)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-500">Promedio Final</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($admision->promedio_final, 2) }}</p>
            </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            @if($gestion)
                <p class="text-gray-500 mb-4">No tienes inscripción en la gestión activa <span class="font-semibold text-gray-700">{{ $gestion->nombre }}</span>.</p>
                <a href="{{ route('inscripcion.paso2.create') }}"
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Iniciar inscripción &rarr;
                </a>
            @else
                <p class="text-gray-500">No hay gestión activa en este momento.</p>
            @endif
        </div>
    @endif
</div>

{{-- Mis notas --}}
<div>
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Mis Notas</h2>
    @if($notas->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-6 text-center text-gray-500">
            No hay notas registradas aún.
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Examen</th>
                        <th class="px-4 py-3 text-left">Calificación</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($notas as $nota)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $nota->examen->nombre ?? 'Examen' }}</td>
                        <td class="px-4 py-3 font-semibold {{ $nota->calificacion >= 51 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($nota->calificacion, 2) }}
                        </td>
                        <td class="px-4 py-3 capitalize">{{ $nota->estado }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
