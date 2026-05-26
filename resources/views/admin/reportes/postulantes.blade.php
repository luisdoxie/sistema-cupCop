@extends('layouts.admin')
@section('title', 'Reporte: Lista de Postulantes')
@section('page-title', 'Reporte 1 — Lista de Postulantes')

@section('content')
<div class="space-y-4">

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Gestión</label>
                <select name="id_gestion" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                    <option value="">Todas</option>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id }}" {{ request('id_gestion') == $g->id ? 'selected' : '' }}>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
                <select name="estado" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                    <option value="">Todos</option>
                    @foreach(['inscrito','documentos_pendientes','pago_pendiente','cursando','admitido_carrera1','admitido_carrera2','reprobado','no_admitido'] as $est)
                        <option value="{{ $est }}" {{ request('estado') == $est ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$est)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Ciudad</label>
                <input type="text" name="ciudad" value="{{ request('ciudad') }}" placeholder="Filtrar ciudad..." class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Colegio</label>
                <input type="text" name="colegio" value="{{ request('colegio') }}" placeholder="Filtrar colegio..." class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1.5 rounded">Filtrar</button>
                <a href="{{ route('admin.reportes.postulantes') }}" class="text-sm text-gray-500 hover:underline py-1.5">Limpiar</a>
            </div>
        </form>
    </div>

    {{-- Botones exportar --}}
    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">{{ $registros->total() }} registros encontrados</span>
        <div class="flex gap-2">
            <a href="{{ route('admin.reportes.postulantes.pdf', request()->query()) }}" target="_blank"
               class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1.5 rounded">PDF</a>
            <a href="{{ route('admin.reportes.postulantes.excel', request()->query()) }}"
               class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded">Excel</a>
            <a href="{{ route('admin.reportes.postulantes.csv', request()->query()) }}"
               class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-3 py-1.5 rounded">CSV</a>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(['CI','Nombre','Apellido','Correo','Colegio','Ciudad','Carrera 1','Carrera 2','Estado','Carrera Asignada'] as $col)
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($registros as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-mono text-xs">{{ $r->ci }}</td>
                    <td class="px-4 py-2">{{ $r->nombre }}</td>
                    <td class="px-4 py-2">{{ $r->apellido }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $r->correo }}</td>
                    <td class="px-4 py-2 text-xs">{{ $r->colegio }}</td>
                    <td class="px-4 py-2 text-xs">{{ $r->ciudad }}</td>
                    <td class="px-4 py-2 text-xs">{{ $r->carrera1 }}</td>
                    <td class="px-4 py-2 text-xs">{{ $r->carrera2 }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            {{ in_array($r->estado, ['admitido_carrera1','admitido_carrera2']) ? 'bg-green-100 text-green-800' :
                               ($r->estado === 'reprobado' ? 'bg-red-100 text-red-800' :
                               ($r->estado === 'cursando' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700')) }}">
                            {{ ucfirst(str_replace('_',' ',$r->estado)) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-xs font-medium text-green-700">{{ $r->carrera_asignada ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="10" class="px-4 py-8 text-center text-gray-400">No hay registros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $registros->links() }}</div>
</div>
@endsection
