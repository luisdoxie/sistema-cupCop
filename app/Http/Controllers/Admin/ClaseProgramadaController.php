<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\ClaseProgramada;
use App\Models\Gestion;
use App\Models\BloqueHorario;
use App\Models\AsignacionAcademica;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaseProgramadaController extends Controller
{
    public function index()
    {
        $gestion = Gestion::where('estado', 'activo')->first();

        $stats = null;
        $porGrupo = collect();

        if ($gestion) {
            $stats = [
                'total'       => ClaseProgramada::whereHas('asignacion.materiaGrupo.grupo', fn($q) => $q->where('id_gestion', $gestion->id))->count(),
                'programadas' => ClaseProgramada::whereHas('asignacion.materiaGrupo.grupo', fn($q) => $q->where('id_gestion', $gestion->id))->where('estado', 'programada')->count(),
                'realizadas'  => ClaseProgramada::whereHas('asignacion.materiaGrupo.grupo', fn($q) => $q->where('id_gestion', $gestion->id))->where('estado', 'realizada')->count(),
                'canceladas'  => ClaseProgramada::whereHas('asignacion.materiaGrupo.grupo', fn($q) => $q->where('id_gestion', $gestion->id))->where('estado', 'cancelada')->count(),
            ];

            $porGrupo = DB::select("
                SELECT g.nombre AS grupo, m.nombre AS materia,
                       COUNT(cp.id) AS total_clases
                FROM clase_programada cp
                JOIN asignacion_academica aa ON aa.id = cp.id_asignacion
                JOIN materia_grupo mg ON mg.id = aa.id_materia_grupo
                JOIN grupo g ON g.id = mg.id_grupo
                JOIN materia m ON m.id = mg.id_materia
                WHERE g.id_gestion = ?
                GROUP BY g.nombre, m.nombre
                ORDER BY g.nombre, m.nombre
            ", [$gestion->id]);
        }

        return view('admin.clases.index', compact('gestion', 'stats', 'porGrupo'));
    }

    public function generar(Request $request)
    {
        $gestion = Gestion::where('estado', 'activo')->first();

        if (!$gestion || !$gestion->fecha_inicio || !$gestion->fecha_fin) {
            return back()->with('error', 'La gestión activa no tiene fechas definidas.');
        }

        $aulas = Aula::where('estado', 'disponible')->orderBy('capacidad', 'desc')->get();

        if ($aulas->isEmpty()) {
            return back()->with('error', 'No hay aulas disponibles. Cree aulas primero.');
        }

        $diasMap = [
            'lunes'     => Carbon::MONDAY,
            'martes'    => Carbon::TUESDAY,
            'miercoles' => Carbon::WEDNESDAY,
            'jueves'    => Carbon::THURSDAY,
            'viernes'   => Carbon::FRIDAY,
            'sabado'    => Carbon::SATURDAY,
        ];

        $inicio = Carbon::parse($gestion->fecha_inicio);
        $fin    = Carbon::parse($gestion->fecha_fin);

        $generadas = 0;
        $saltadas  = 0;

        DB::transaction(function () use ($gestion, $aulas, $diasMap, $inicio, $fin, &$generadas, &$saltadas) {
            // Obtener todos los bloques de asignaciones activas de esta gestión
            $bloques = BloqueHorario::whereHas('asignacion.materiaGrupo.grupo', function ($q) use ($gestion) {
                $q->where('id_gestion', $gestion->id)->where('estado', 'activo');
            })->with('asignacion')->get();

            $aulaIdx = 0;

            foreach ($bloques as $bloque) {
                if (!isset($diasMap[$bloque->dia])) continue;

                $diaSemana = $diasMap[$bloque->dia];

                // Primer día de ese tipo de semana dentro del rango
                $fecha = $inicio->copy()->next($diaSemana);
                if ($inicio->dayOfWeek === $diaSemana) {
                    $fecha = $inicio->copy();
                }

                while ($fecha->lte($fin)) {
                    // Verificar si ya existe
                    $existe = ClaseProgramada::where('id_asignacion', $bloque->id_asignacion)
                        ->where('id_bloque', $bloque->id)
                        ->where('fecha', $fecha->toDateString())
                        ->exists();

                    if ($existe) {
                        $saltadas++;
                        $fecha->addWeek();
                        continue;
                    }

                    // Buscar aula disponible en esa fecha/hora
                    $aulaAsignada = null;
                    $totalAulas   = $aulas->count();

                    for ($i = 0; $i < $totalAulas; $i++) {
                        $candidata = $aulas[($aulaIdx + $i) % $totalAulas];

                        $conflicto = ClaseProgramada::where('id_aula', $candidata->id)
                            ->where('fecha', $fecha->toDateString())
                            ->where('estado', '!=', 'cancelada')
                            ->whereHas('bloque', function ($q) use ($bloque) {
                                $q->where('hora_inicio', '<', $bloque->hora_fin)
                                  ->where('hora_fin', '>', $bloque->hora_inicio);
                            })->exists();

                        if (!$conflicto) {
                            $aulaAsignada = $candidata;
                            $aulaIdx = ($aulaIdx + $i + 1) % $totalAulas;
                            break;
                        }
                    }

                    // Si no hay aula libre, usar la primera de todas (sin bloquear)
                    if (!$aulaAsignada) {
                        $aulaAsignada = $aulas->first();
                    }

                    ClaseProgramada::create([
                        'id_asignacion' => $bloque->id_asignacion,
                        'id_aula'       => $aulaAsignada->id,
                        'id_bloque'     => $bloque->id,
                        'fecha'         => $fecha->toDateString(),
                        'estado'        => 'programada',
                    ]);

                    $generadas++;
                    $fecha->addWeek();
                }
            }
        });

        return back()->with('success', "Se generaron {$generadas} clases programadas. ({$saltadas} ya existían y se omitieron)");
    }

    public function limpiar()
    {
        $gestion = Gestion::where('estado', 'activo')->first();

        if (!$gestion) {
            return back()->with('error', 'No hay gestión activa.');
        }

        DB::transaction(function () use ($gestion) {
            $ids = DB::table('clase_programada')
                ->join('asignacion_academica', 'asignacion_academica.id', '=', 'clase_programada.id_asignacion')
                ->join('materia_grupo', 'materia_grupo.id', '=', 'asignacion_academica.id_materia_grupo')
                ->join('grupo', 'grupo.id', '=', 'materia_grupo.id_grupo')
                ->where('grupo.id_gestion', $gestion->id)
                ->pluck('clase_programada.id');

            DB::table('asistencia')->whereIn('id_clase', $ids)->delete();
            DB::table('clase_programada')->whereIn('id', $ids)->delete();
        });

        return back()->with('success', 'Todas las clases programadas de la gestión activa fueron eliminadas.');
    }
}
