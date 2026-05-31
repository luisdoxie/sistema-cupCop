<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\ClaseProgramada;
use App\Models\Gestion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $estudiante = $user->estudiante;

        $admision    = null;
        $notas       = collect();
        $gestion     = null;

        if ($estudiante) {
            $gestion  = Gestion::where('estado', 'activo')->first();

            if ($gestion) {
                $admision = Admision::with(['carrera1', 'carrera2', 'grupo'])
                    ->where('id_estudiante', $estudiante->id)
                    ->where('id_gestion', $gestion->id)
                    ->first();

                if ($admision) {
                    $notas = $admision->notas()->with('examen')->get();
                }
            }
        }

        return view('estudiante.dashboard', compact('admision', 'notas', 'gestion'));
    }

    public function horario(Request $request)
    {
        $user       = Auth::user();
        $estudiante = $user->estudiante;

        $admision = null;
        $gestion  = null;
        $clases   = collect();
        $dias     = [];

        $semana = $request->input('semana')
            ? Carbon::parse($request->input('semana') . '-1')
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $inicio = $semana->copy()->startOfWeek(Carbon::MONDAY);
        $fin    = $semana->copy()->endOfWeek(Carbon::SATURDAY);

        for ($i = 0; $i < 6; $i++) {
            $dias[] = $inicio->copy()->addDays($i);
        }

        if ($estudiante) {
            $gestion  = Gestion::where('estado', 'activo')->first();

            if ($gestion) {
                $admision = Admision::with('grupo')
                    ->where('id_estudiante', $estudiante->id)
                    ->where('id_gestion', $gestion->id)
                    ->first();

                if ($admision && $admision->id_grupo) {
                    $clases = ClaseProgramada::with([
                        'asignacion.materiaGrupo.materia',
                        'asignacion.materiaGrupo.grupo',
                        'aula.piso',
                        'bloque',
                    ])
                    ->whereHas('asignacion.materiaGrupo', fn($q) => $q->where('id_grupo', $admision->id_grupo))
                    ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
                    ->orderBy('fecha')
                    ->get()
                    ->groupBy('fecha');
                }
            }
        }

        return view('estudiante.horario', compact('admision', 'gestion', 'clases', 'dias', 'inicio', 'fin', 'semana'));
    }

    public function resultados()
    {
        $user       = Auth::user();
        $estudiante = $user->estudiante;

        $admision   = null;
        $gestion    = null;
        $materias   = collect();

        if ($estudiante) {
            $gestion = Gestion::where('estado', 'activo')->first();

            if ($gestion) {
                $admision = Admision::with([
                    'carrera1',
                    'carrera2',
                    'grupo.materiaGrupos.materia',
                    'grupo.materiaGrupos.examenes',
                ])
                ->where('id_estudiante', $estudiante->id)
                ->where('id_gestion', $gestion->id)
                ->first();

                if ($admision && $admision->grupo) {
                    foreach ($admision->grupo->materiaGrupos as $mg) {
                        $notaMateria = DB::selectOne(
                            'SELECT calcular_nota_materia(?, ?) AS total',
                            [$admision->id, $mg->id]
                        );
                        $promedioMateria = DB::selectOne(
                            'SELECT calcular_promedio_materia(?, ?) AS promedio',
                            [$admision->id, $mg->id]
                        );

                        $notasPorTipo = [];
                        foreach ($mg->examenes as $examen) {
                            $nota = $admision->notas()
                                ->where('id_examen', $examen->id)
                                ->first();
                            $notasPorTipo[$examen->tipo] = $nota ? $nota->calificacion : null;
                        }

                        $materias->push([
                            'materia'  => $mg->materia,
                            'notas'    => $notasPorTipo,
                            'total'    => $notaMateria ? (float) $notaMateria->total : null,
                            'promedio' => $promedioMateria ? (float) $promedioMateria->promedio : null,
                        ]);
                    }
                }
            }
        }

        return view('estudiante.resultados', compact('admision', 'gestion', 'materias'));
    }
}
