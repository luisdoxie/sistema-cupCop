<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Gestion;
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
