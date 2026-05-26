@extends('layouts.admin')

@section('title', 'Grupos')
@section('page-title', 'Grupos')

@section('content')
<div class="space-y-4" x-data="gruposPanel()">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    {{-- Gestión activa info --}}
    @if($gestionActiva)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
            <div>
                <span class="text-sm font-semibold text-blue-800">Gestión activa:</span>
                <span class="text-sm text-blue-700 ml-2">{{ $gestionActiva->nombre }}</span>
            </div>
            <div class="flex items-center gap-3">
                {{-- Calcular grupos necesarios --}}
                <button @click="calcular()" type="button"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1.5 rounded">
                    Calcular grupos necesarios
                </button>
                <span x-show="gruposNecesarios !== null" class="text-sm font-semibold text-yellow-800">
                    Necesarios: <span x-text="gruposNecesarios"></span>
                </span>

                {{-- Asignar postulantes --}}
                <form method="POST" action="{{ route('admin.grupos.asignar') }}"
                      onsubmit="return confirm('¿Asignar postulantes a grupos? Esta acción cambiará su estado a cursando.')">
                    @csrf
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded">
                        Asignar postulantes
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded">
            No hay una gestión activa. Active una gestión para gestionar grupos.
        </div>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-700">
            @if($gestionActiva) Grupos — {{ $gestionActiva->nombre }} @else Sin gestión activa @endif
        </h2>
        @if($gestionActiva)
        <a href="{{ route('admin.grupos.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
            + Nuevo Grupo
        </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paralelo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modalidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cupo Máx.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiantes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($grupos as $grupo)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $grupo->nombre }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $grupo->paralelo }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $grupo->modalidad }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $grupo->cupo_maximo }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $grupo->total_estudiantes }}</td>
                    <td class="px-6 py-4">
                        @if($grupo->estado === 'activo')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Activo</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Inactivo</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No hay grupos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($grupos instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div>{{ $grupos->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function gruposPanel() {
    return {
        gruposNecesarios: null,
        async calcular() {
            try {
                const resp = await fetch('{{ route('admin.grupos.calcular') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content
                            || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                });
                const data = await resp.json();
                if (data.grupos_necesarios !== undefined) {
                    this.gruposNecesarios = data.grupos_necesarios;
                } else {
                    alert(data.error || 'Error al calcular');
                }
            } catch (e) {
                alert('Error de conexión');
            }
        }
    };
}
</script>
@endpush
