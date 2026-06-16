@extends('layouts.admin')
@section('title', 'Postulaciones Docente')
@section('page-title', 'Postulaciones Docente')

@section('content')
<div class="space-y-4">

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.postulaciones-docente.index') }}"
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                  {{ !request('estado') ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:border-blue-400' }}">
            Todas
        </a>
        <a href="{{ route('admin.postulaciones-docente.index', ['estado' => 'pendiente']) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition inline-flex items-center gap-1
                  {{ request('estado') === 'pendiente' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:border-yellow-400' }}">
            Pendientes
            @if($pendientes > 0)
            <span class="bg-yellow-600 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $pendientes }}</span>
            @endif
        </a>
        <a href="{{ route('admin.postulaciones-docente.index', ['estado' => 'aprobada']) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                  {{ request('estado') === 'aprobada' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:border-green-400' }}">
            Aprobadas
        </a>
        <a href="{{ route('admin.postulaciones-docente.index', ['estado' => 'rechazada']) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                  {{ request('estado') === 'rechazada' ? 'bg-red-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:border-red-400' }}">
            Rechazadas
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3 text-left">Postulante</th>
                    <th class="px-4 py-3 text-left hidden sm:table-cell">CI</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Materias</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Exp.</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Fecha</th>
                    <th class="px-4 py-3 text-left">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($postulaciones as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800">{{ $p->nombre }} {{ $p->apellido }}</p>
                        <p class="text-xs text-gray-400">{{ $p->grado_academico ?? 'Sin grado indicado' }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $p->ci }}</td>
                    <td class="px-4 py-3 text-gray-600 hidden md:table-cell">
                        {{ $p->materias_count }} materia(s)
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs hidden lg:table-cell">
                        {{ $p->anios_experiencia }} años
                    </td>
                    <td class="px-4 py-3">
                        @if($p->estado === 'pendiente')
                            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full font-medium">Pendiente</span>
                        @elseif($p->estado === 'aprobada')
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">Aprobada</span>
                        @else
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-medium">Rechazada</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs hidden md:table-cell">
                        {{ $p->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.postulaciones-docente.show', $p) }}"
                           class="text-blue-600 hover:text-blue-800 text-xs font-medium underline whitespace-nowrap">
                            Ver detalle
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">
                        No hay postulaciones
                        @if(request('estado')) con estado "{{ request('estado') }}" @endif.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($postulaciones->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $postulaciones->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
