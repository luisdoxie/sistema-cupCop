@extends(auth()->user()->esAdmin() ? 'layouts.admin' : 'layouts.coordinador')
@section('title', 'Reporte: Notas')
@section('page-title', 'Reporte 2 — Aprobados y Reprobados')

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-4 gap-3">
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
                <label class="block text-xs font-medium text-gray-600 mb-1">Materia</label>
                <select name="id_materia" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                    <option value="">Todas</option>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}" {{ request('id_materia') == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Resultado</label>
                <select name="resultado" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                    <option value="">Todos</option>
                    <option value="APROBADO" {{ request('resultado') === 'APROBADO' ? 'selected' : '' }}>APROBADO</option>
                    <option value="REPROBADO" {{ request('resultado') === 'REPROBADO' ? 'selected' : '' }}>REPROBADO</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1.5 rounded">Filtrar</button>
                <a href="{{ route('admin.reportes.notas') }}" class="text-sm text-gray-500 hover:underline py-1.5">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">{{ $registros->total() }} registros</span>
        <div class="flex gap-2">
            <a href="{{ route('admin.reportes.notas.pdf', request()->query()) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1.5 rounded">PDF</a>
            <a href="{{ route('admin.reportes.notas.excel', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded">Excel</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(['CI','Estudiante','Materia','Gestión','P1','P2','Final','Promedio Final','Resultado'] as $col)
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($registros as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-mono text-xs">{{ $r->ci }}</td>
                    <td class="px-4 py-2 font-medium">{{ $r->estudiante }}</td>
                    <td class="px-4 py-2 text-xs">{{ $r->materia }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $r->gestion }}</td>
                    <td class="px-4 py-2 text-center">{{ $r->p1 ?? '—' }}</td>
                    <td class="px-4 py-2 text-center">{{ $r->p2 ?? '—' }}</td>
                    <td class="px-4 py-2 text-center">{{ $r->final_nota ?? '—' }}</td>
                    <td class="px-4 py-2 text-center font-medium">{{ $r->promedio !== null ? number_format($r->promedio, 2) : '—' }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-0.5 rounded text-xs font-bold {{ $r->resultado === 'APROBADO' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $r->resultado }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">No hay registros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $registros->links() }}</div>
</div>
@endsection
