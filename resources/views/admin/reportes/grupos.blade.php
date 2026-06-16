@extends(auth()->user()->esAdmin() ? 'layouts.admin' : 'layouts.coordinador')
@section('title', 'Reporte: Grupos Habilitados')
@section('page-title', 'Reporte 4 — Grupos Habilitados')

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Gestión</label>
                <select name="id_gestion" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                    <option value="">Todas</option>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id }}" {{ request('id_gestion') == $g->id ? 'selected' : '' }}>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1.5 rounded">Filtrar</button>
                <a href="{{ route('admin.reportes.grupos') }}" class="text-sm text-gray-500 hover:underline py-1.5">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-800">
        Divisor configurado: <strong>{{ $divisor }}</strong> estudiantes por grupo
    </div>

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">{{ $registros->total() }} registros</span>
        <div class="flex gap-2">
            <a href="{{ route('admin.reportes.grupos.pdf', request()->query()) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1.5 rounded">PDF</a>
            <a href="{{ route('admin.reportes.grupos.excel', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded">Excel</a>
            <a href="{{ route('admin.reportes.grupos.csv', request()->query()) }}" class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-3 py-1.5 rounded">CSV</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(['Gestión','Año','Total Grupos','Capacidad Total','Est. Asignados','Docentes','Grupos Necesarios'] as $col)
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($registros as $r)
                @php $necesarios = ceil($r->estudiantes_asignados / $divisor) @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium">{{ $r->gestion }}</td>
                    <td class="px-4 py-2 text-center">{{ $r->anio }}</td>
                    <td class="px-4 py-2 text-center">{{ $r->total_grupos }}</td>
                    <td class="px-4 py-2 text-center">{{ $r->capacidad_total }}</td>
                    <td class="px-4 py-2 text-center font-medium">{{ $r->estudiantes_asignados }}</td>
                    <td class="px-4 py-2 text-center">{{ $r->total_docentes }}</td>
                    <td class="px-4 py-2 text-center">
                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $r->total_grupos >= $necesarios ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $necesarios }} (CEIL({{ $r->estudiantes_asignados }}/{{ $divisor }}))
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay registros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $registros->links() }}</div>
</div>
@endsection
