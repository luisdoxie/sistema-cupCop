@extends('layouts.admin')

@section('title', 'Verificación de Documentos')
@section('page-title', 'Verificación de Documentos')

@section('content')
<div class="space-y-4">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    <h2 class="text-lg font-semibold text-gray-700">Postulantes con Documentos Pendientes</h2>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CI</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Admisión</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado Admisión</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docs. Pendientes</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($admisiones as $admision)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        {{ $admision->estudiante->persona->nombre ?? '—' }}
                        {{ $admision->estudiante->persona->apellido ?? '' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $admision->estudiante->persona->ci ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($admision->fecha)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full capitalize">
                            {{ str_replace('_', ' ', $admision->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $admision->documentos->where('estado_verificacion', 'pendiente')->count() }}
                        de {{ $admision->documentos->count() }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.documentos.show', $admision) }}"
                           class="text-blue-600 hover:underline">Revisar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No hay documentos pendientes de verificación.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $admisiones->links() }}</div>
</div>
@endsection
