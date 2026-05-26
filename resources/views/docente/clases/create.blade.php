@extends('layouts.docente')
@section('title', 'Programar Clase')
@section('page-title', 'Programar Nueva Clase')

@section('content')
<div class="max-w-2xl" x-data="claseForm()">

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('docente.clases.store') }}">
            @csrf

            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asignación (Materia - Grupo) <span class="text-red-500">*</span></label>
                    <select name="id_asignacion" x-model="idAsignacion" @change="cargarBloques()"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="">Seleccionar...</option>
                        @foreach($asignaciones as $a)
                        <option value="{{ $a->id }}"
                                data-bloques="{{ $a->bloquesHorario->toJson() }}"
                                {{ old('id_asignacion') == $a->id ? 'selected' : '' }}>
                            {{ $a->materiaGrupo->materia->nombre }} — Grupo {{ $a->materiaGrupo->grupo->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bloque Horario <span class="text-red-500">*</span></label>
                    <select name="id_bloque" x-model="idBloque"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="">Seleccione una asignación primero...</option>
                        <template x-for="b in bloques" :key="b.id">
                            <option :value="b.id" x-text="`${b.dia.charAt(0).toUpperCase()+b.dia.slice(1)} ${b.hora_inicio} - ${b.hora_fin}`"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha" x-model="fecha" @change="verificarAula()"
                           value="{{ old('fecha', date('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aula <span class="text-red-500">*</span></label>
                    <select name="id_aula" x-model="idAula" @change="verificarAula()"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="">Seleccionar aula...</option>
                        @foreach($aulas as $aula)
                        <option value="{{ $aula->id }}" {{ old('id_aula') == $aula->id ? 'selected' : '' }}>
                            Aula {{ $aula->numero }} — Piso {{ $aula->piso->numero ?? '?' }}
                            (Cap. {{ $aula->capacidad }}, {{ ucfirst($aula->modalidad) }})
                        </option>
                        @endforeach
                    </select>
                    <p x-show="mensajeAula" :class="aulaDisponible ? 'text-green-600' : 'text-red-600'"
                       class="text-xs mt-1" x-text="mensajeAula"></p>
                </div>

            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded">
                    Programar Clase
                </button>
                <a href="{{ route('docente.clases.index') }}" class="text-sm text-gray-500 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function claseForm() {
    return {
        idAsignacion: '{{ old('id_asignacion', '') }}',
        idBloque: '{{ old('id_bloque', '') }}',
        idAula: '{{ old('id_aula', '') }}',
        fecha: '{{ old('fecha', date('Y-m-d')) }}',
        bloques: [],
        mensajeAula: '',
        aulaDisponible: true,

        cargarBloques() {
            this.bloques = [];
            this.idBloque = '';
            const sel = document.querySelector(`option[value="${this.idAsignacion}"]`);
            if (sel) {
                this.bloques = JSON.parse(sel.dataset.bloques || '[]');
            }
        },

        async verificarAula() {
            if (!this.idAula || !this.idBloque || !this.fecha) { this.mensajeAula = ''; return; }
            const params = new URLSearchParams({ id_aula: this.idAula, id_bloque: this.idBloque, fecha: this.fecha });
            const res  = await fetch(`{{ route('docente.clases.verificar-aula') }}?${params}`, { headers: { Accept: 'application/json' } });
            const data = await res.json();
            this.aulaDisponible = data.disponible;
            this.mensajeAula    = data.mensaje;
        },
    };
}
</script>
@endpush
