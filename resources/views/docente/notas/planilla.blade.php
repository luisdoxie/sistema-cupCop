@extends('layouts.docente')

@section('page-title', 'Planilla de Notas')

@section('content')
<div class="space-y-6"
     x-data="planillaNotas()"
     x-init="init({{ json_encode($grupo->materiaGrupos->map(fn($mg) => [
         'id'       => $mg->id,
         'nombre'   => $mg->materia->nombre ?? 'Materia',
         'examenes' => $mg->examenes->map(fn($e) => [
             'id'             => $e->id,
             'tipo'           => $e->tipo,
             'puntaje_maximo' => $e->puntaje_maximo,
         ])->values()->toArray(),
     ])->values()->toArray()) }},
     {{ json_encode($admisiones->pluck('id')->toArray()) }},
     {{ json_encode($todasLasNotas->map(fn($notasExamen) => $notasExamen->keyBy('id_admision')->map(fn($n) => $n->calificacion)->toArray())->toArray()) }})">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Planilla de Notas — {{ $grupo->nombre }} (Paralelo {{ $grupo->paralelo }})
            </h2>
            <p class="text-sm text-gray-500 mt-1">Gestión: {{ $grupo->gestion->nombre ?? '—' }}</p>
        </div>
        <a href="{{ route('docente.dashboard') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">
            Volver
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($admisiones->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            No hay estudiantes con estado <strong>cursando</strong> en este grupo.
        </div>
    @elseif($grupo->materiaGrupos->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            Este grupo no tiene materias asignadas.
        </div>
    @else
    <form method="POST" action="{{ route('docente.notas.guardar', $grupo) }}">
        @csrf
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="px-4 py-3 text-left sticky left-0 bg-gray-800 z-10" rowspan="2">Estudiante</th>
                        @foreach($grupo->materiaGrupos as $mg)
                        <th class="px-2 py-2 text-center border-l border-gray-600" colspan="4">
                            {{ $mg->materia->nombre ?? 'Materia' }}
                        </th>
                        @endforeach
                        <th class="px-4 py-3 text-center border-l border-gray-600" rowspan="2">Promedio<br>General</th>
                    </tr>
                    <tr class="bg-gray-700 text-white text-xs">
                        @foreach($grupo->materiaGrupos as $mg)
                        <th class="px-2 py-2 text-center border-l border-gray-600">P1<br><span class="text-gray-400">/100</span></th>
                        <th class="px-2 py-2 text-center">P2<br><span class="text-gray-400">/100</span></th>
                        <th class="px-2 py-2 text-center">Final<br><span class="text-gray-400">/100</span></th>
                        <th class="px-2 py-2 text-center">Prom. Final<br><span class="text-gray-400">/100</span></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($admisiones as $admision)
                    @php
                        $persona = $admision->estudiante->persona ?? null;
                    @endphp
                    <tr class="border-b border-gray-200 hover:bg-gray-50"
                        x-bind:class="getRowClass({{ $admision->id }})">
                        <td class="px-4 py-3 font-medium text-gray-900 sticky left-0 bg-white z-10 min-w-[180px]">
                            {{ $persona->nombre ?? '' }} {{ $persona->apellido ?? '' }}
                            <div class="text-xs text-gray-500">CI: {{ $persona->ci ?? '—' }}</div>
                        </td>

                        @foreach($grupo->materiaGrupos as $mg)
                        @php
                            $examenesMg = $mg->examenes->keyBy('tipo');
                            $tipos = ['parcial1', 'parcial2', 'final'];
                        @endphp
                        @foreach($tipos as $tipoIdx => $tipo)
                        @php $examen = $examenesMg[$tipo] ?? null; @endphp
                        <td class="px-2 py-2 text-center {{ $tipoIdx === 0 ? 'border-l border-gray-200' : '' }}">
                            @if($examen)
                            <input
                                type="number"
                                name="notas[{{ $examen->id }}][{{ $admision->id }}]"
                                min="0"
                                max="100"
                                step="0.01"
                                class="w-16 border border-gray-300 rounded px-1 py-1 text-center text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                x-model.number="notas[{{ $mg->id }}][{{ $admision->id }}]['{{ $tipo }}']"
                                @input="calcular({{ $mg->id }}, {{ $admision->id }})"
                                value="{{ $todasLasNotas[$examen->id][$admision->id]->calificacion ?? '' }}"
                            >
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        @endforeach
                        {{-- Total por materia --}}
                        <td class="px-3 py-2 text-center font-semibold"
                            x-bind:class="getTotalClass({{ $mg->id }}, {{ $admision->id }})">
                            <span x-text="getTotal({{ $mg->id }}, {{ $admision->id }})">—</span>
                        </td>
                        @endforeach

                        {{-- Promedio general --}}
                        <td class="px-4 py-2 text-center font-bold border-l border-gray-200"
                            x-bind:class="getPromedioClass({{ $admision->id }})">
                            <span x-text="getPromedio({{ $admision->id }})">—</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-medium">
                Guardar Planilla
            </button>
        </div>
    </form>

    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
        <p><span class="inline-block w-3 h-3 bg-green-200 rounded mr-1"></span> Verde: Promedio Final >= 60 (Aprobado)</p>
        <p class="mt-1"><span class="inline-block w-3 h-3 bg-red-200 rounded mr-1"></span> Rojo: Promedio Final < 60 (Reprobado)</p>
    </div>
    @endif
</div>

<script>
function planillaNotas() {
    return {
        materias: [],
        admisionIds: [],
        notas: {},
        totales: {},

        init(materias, admisionIds, notasExistentes) {
            this.materias = materias;
            this.admisionIds = admisionIds;

            // Inicializar estructura de notas
            materias.forEach(mg => {
                this.notas[mg.id] = {};
                this.totales[mg.id] = {};
                admisionIds.forEach(adId => {
                    this.notas[mg.id][adId] = { parcial1: null, parcial2: null, final: null };
                    this.totales[mg.id][adId] = null;

                    // Cargar notas existentes
                    mg.examenes.forEach(ex => {
                        const tipo = ex.tipo;
                        if (notasExistentes[ex.id] && notasExistentes[ex.id][adId] !== undefined) {
                            this.notas[mg.id][adId][tipo] = parseFloat(notasExistentes[ex.id][adId]);
                        }
                    });

                    this.calcular(mg.id, adId);
                });
            });
        },

        calcular(mgId, adId) {
            const ns = this.notas[mgId][adId];
            const p1 = ns.parcial1 !== null && ns.parcial1 !== '' ? parseFloat(ns.parcial1) : null;
            const p2 = ns.parcial2 !== null && ns.parcial2 !== '' ? parseFloat(ns.parcial2) : null;
            const fi = ns.final !== null && ns.final !== '' ? parseFloat(ns.final) : null;

            if (p1 !== null && p2 !== null && fi !== null) {
                this.totales[mgId][adId] = Math.round((p1 * 0.30 + p2 * 0.30 + fi * 0.40) * 100) / 100;
            } else {
                this.totales[mgId][adId] = null;
            }
        },

        getTotal(mgId, adId) {
            const t = this.totales[mgId] ? this.totales[mgId][adId] : null;
            return t !== null ? t.toFixed(2) : '—';
        },

        getTotalClass(mgId, adId) {
            const t = this.totales[mgId] ? this.totales[mgId][adId] : null;
            if (t === null) return '';
            return t >= 60 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        },

        getPromedio(adId) {
            let suma = 0;
            let count = 0;
            this.materias.forEach(mg => {
                const t = this.totales[mg.id] ? this.totales[mg.id][adId] : null;
                if (t !== null) {
                    suma += t;
                    count++;
                }
            });
            if (count === 0) return '—';
            return (suma / count).toFixed(2);
        },

        getPromedioClass(adId) {
            let suma = 0;
            let count = 0;
            this.materias.forEach(mg => {
                const t = this.totales[mg.id] ? this.totales[mg.id][adId] : null;
                if (t !== null) {
                    suma += t;
                    count++;
                }
            });
            if (count === 0) return '';
            const avg = suma / count;
            return avg >= 60 ? 'text-green-700' : 'text-red-700';
        },

        getRowClass(adId) {
            return '';
        },
    };
}
</script>
@endsection
