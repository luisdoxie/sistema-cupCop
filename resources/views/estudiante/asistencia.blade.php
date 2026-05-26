@extends('layouts.estudiante')
@section('title', 'Mi Asistencia')
@section('page-title', 'Mi Asistencia')

@section('content')
<div class="space-y-6">

    @if(!$admision || !$admision->id_grupo)
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
        No estás asignado a un grupo actualmente.
    </div>
    @else

    {{-- Resumen por materia --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($materias as $m)
        @php $color = $m->porcentaje >= 75 ? 'green' : ($m->porcentaje >= 50 ? 'yellow' : 'red'); @endphp
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-{{ $color }}-400">
            <p class="text-sm font-semibold text-gray-700 truncate mb-2">{{ $m->materia }}</p>
            <div class="flex items-end justify-between">
                <div class="text-xs text-gray-500 space-y-0.5">
                    <p>Clases: <strong>{{ $m->total_clases }}</strong></p>
                    <p class="text-green-700">Presentes: <strong>{{ $m->presentes }}</strong></p>
                    <p class="text-red-700">Ausentes: <strong>{{ $m->ausentes }}</strong></p>
                    <p class="text-yellow-700">Justif.: <strong>{{ $m->justificados }}</strong></p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-{{ $color }}-600">{{ $m->porcentaje }}%</p>
                    <p class="text-xs text-gray-400">asistencia</p>
                </div>
            </div>
            <div class="mt-2 bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full bg-{{ $color }}-500" style="width: {{ min($m->porcentaje, 100) }}%"></div>
            </div>
        </div>
        @empty
        <div class="col-span-4 text-center text-gray-400 py-8">No hay clases registradas aún.</div>
        @endforelse
    </div>

    {{-- Tabla detallada --}}
    @if(count($detalle) > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Detalle de Asistencia</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($detalle as $d)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">{{ \Carbon\Carbon::parse($d->fecha)->format('d/m/Y') }}</td>
                    <td class="px-6 py-3 font-medium">{{ $d->materia }}</td>
                    <td class="px-6 py-3">
                        @if($d->estado === 'presente')
                            <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-medium">Presente</span>
                        @elseif($d->estado === 'ausente')
                            <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-xs font-medium">Ausente</span>
                        @elseif($d->estado === 'justificado')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-xs font-medium">Justificado</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">Sin registro</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @endif
</div>
@endsection
