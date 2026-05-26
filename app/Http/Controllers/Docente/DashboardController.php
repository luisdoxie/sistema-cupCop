<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\AsignacionAcademica;
use App\Models\ClaseProgramada;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $docente = $user->docente;

        $proximasClases  = collect();
        $sinAsistencia   = collect();
        $misAsignaciones = collect();

        if ($docente) {
            $hoy = now()->toDateString();

            $misAsignaciones = AsignacionAcademica::with([
                    'materiaGrupo.grupo',
                    'materiaGrupo.materia',
                    'bloquesHorario',
                ])
                ->where('id_docente', $docente->id)
                ->where('estado', 'activo')
                ->get();

            $proximasClases = ClaseProgramada::with([
                    'asignacion.materiaGrupo.grupo',
                    'asignacion.materiaGrupo.materia',
                    'bloque',
                ])
                ->whereHas('asignacion', fn($q) => $q->where('id_docente', $docente->id))
                ->where('fecha', '>=', $hoy)
                ->where('estado', 'programada')
                ->orderBy('fecha')
                ->take(5)
                ->get();

            $sinAsistencia = ClaseProgramada::with([
                    'asignacion.materiaGrupo.grupo',
                    'asignacion.materiaGrupo.materia',
                    'bloque',
                ])
                ->whereHas('asignacion', fn($q) => $q->where('id_docente', $docente->id))
                ->where('estado', 'realizada')
                ->whereDoesntHave('asistencias')
                ->orderByDesc('fecha')
                ->take(5)
                ->get();
        }

        return view('docente.dashboard', compact('misAsignaciones', 'proximasClases', 'sinAsistencia'));
    }
}
