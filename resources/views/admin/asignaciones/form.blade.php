@extends('layouts.admin')

@section('title', 'Nueva Asignación')
@section('page-title', 'Nueva Asignación Académica')

@section('content')
<div class="max-w-3xl" x-data="asignacionForm()">

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.asignaciones.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-5 mb-6">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Docente <span class="text-red-500">*</span></label>
                    <select name="id_docente" x-model="idDocente" @change="limpiarBloques()"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="">Seleccionar docente...</option>
                        @foreach($docentes as $d)
                        <option value="{{ $d->id }}"
                                {{ old('id_docente') == $d->id ? 'selected' : '' }}
                                {{ !$d->disponible ? 'class=text-gray-400' : '' }}>
                            {{ $d->persona->nombre }} {{ $d->persona->apellido }}
                            ({{ $d->grupos_asignados }}/{{ $d->max_grupos }} grupos)
                            {{ !$d->disponible ? '[LLENO]' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grupo <span class="text-red-500">*</span></label>
                    <select name="id_grupo" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="">Seleccionar grupo...</option>
                        @foreach($grupos as $g)
                        <option value="{{ $g->id }}" {{ old('id_grupo') == $g->id ? 'selected' : '' }}>
                            {{ $g->nombre }} - {{ $g->paralelo }} ({{ ucfirst($g->modalidad) }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Materia <span class="text-red-500">*</span></label>
                    <select name="id_materia" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="">Seleccionar materia...</option>
                        @foreach($materias as $m)
                        <option value="{{ $m->id }}" {{ old('id_materia') == $m->id ? 'selected' : '' }}>
                            {{ $m->nombre }} ({{ $m->sigla }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Turno <span class="text-red-500">*</span></label>
                    <select name="id_turno" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="">Seleccionar turno...</option>
                        @foreach($turnos as $t)
                        <option value="{{ $t->id }}" {{ old('id_turno') == $t->id ? 'selected' : '' }}>
                            {{ $t->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carga Horaria (horas/semana) <span class="text-red-500">*</span></label>
                    <input type="number" name="carga_horaria" value="{{ old('carga_horaria', 4) }}"
                           min="1" max="40"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observación</label>
                    <input type="text" name="observacion" value="{{ old('observacion') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>

            </div>

            {{-- Bloques horarios --}}
            <div class="border-t pt-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Bloques Horarios</h3>
                    <button type="button" @click="agregarBloque()"
                            x-show="bloques.length < 6"
                            class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded">
                        + Agregar Bloque
                    </button>
                </div>

                <template x-for="(bloque, i) in bloques" :key="i">
                    <div class="grid grid-cols-4 gap-3 mb-3 items-end">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Día</label>
                            <select :name="`bloques[${i}][dia]`" x-model="bloque.dia"
                                    class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" required>
                                <option value="">Día...</option>
                                @foreach($dias as $dia)
                                <option value="{{ $dia }}">{{ ucfirst($dia) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Hora Inicio</label>
                            <input type="time" :name="`bloques[${i}][hora_inicio]`" x-model="bloque.hora_inicio"
                                   class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Hora Fin</label>
                            <input type="time" :name="`bloques[${i}][hora_fin]`" x-model="bloque.hora_fin"
                                   class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" required>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="button"
                                    @click="verificarBloque(i)"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-2 py-1.5 rounded">
                                Verificar
                            </button>
                            <button type="button" @click="eliminarBloque(i)"
                                    x-show="bloques.length > 1"
                                    class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1.5 rounded">
                                &times;
                            </button>
                        </div>
                        <div class="col-span-4" x-show="bloque.mensaje">
                            <p :class="bloque.conflicto ? 'text-red-600' : 'text-green-600'"
                               class="text-xs" x-text="bloque.mensaje"></p>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded">
                    Crear Asignación
                </button>
                <a href="{{ route('admin.asignaciones.index') }}" class="text-sm text-gray-500 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function asignacionForm() {
    return {
        idDocente: '{{ old('id_docente', '') }}',
        bloques: [{ dia: '', hora_inicio: '', hora_fin: '', mensaje: '', conflicto: false }],
        agregarBloque() {
            if (this.bloques.length < 6) {
                this.bloques.push({ dia: '', hora_inicio: '', hora_fin: '', mensaje: '', conflicto: false });
            }
        },
        eliminarBloque(i) {
            this.bloques.splice(i, 1);
        },
        limpiarBloques() {
            this.bloques = this.bloques.map(b => ({ ...b, mensaje: '', conflicto: false }));
        },
        async verificarBloque(i) {
            const b = this.bloques[i];
            if (!this.idDocente || !b.dia || !b.hora_inicio || !b.hora_fin) {
                b.mensaje = 'Complete el docente, día y horario para verificar.';
                b.conflicto = true;
                return;
            }
            try {
                const params = new URLSearchParams({
                    id_docente: this.idDocente,
                    dia: b.dia,
                    hora_inicio: b.hora_inicio,
                    hora_fin: b.hora_fin,
                });
                const resp = await fetch(`{{ route('admin.asignaciones.verificar-horario') }}?${params}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await resp.json();
                b.conflicto = data.conflicto;
                b.mensaje = data.conflicto ? data.detalle : 'Sin conflictos de horario.';
            } catch (e) {
                b.mensaje = 'Error al verificar.';
                b.conflicto = true;
            }
        },
    };
}
</script>
@endpush
