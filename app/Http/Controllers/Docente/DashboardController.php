<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\AsignacionAcademica;
use App\Models\Asistencia;
use App\Models\ClaseProgramada;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $docente = $user->docente;

        $proximasClases     = collect();
        $sinAsistencia      = collect();
        $misAsignaciones    = collect();
        $clasesHoy          = collect();
        $stats              = ['total_estudiantes' => 0, 'clases_realizadas' => 0, 'clases_pendientes' => 0, 'porcentaje_asistencia' => 0, 'total_clases' => 0, 'clases_canceladas' => 0];
        $asistenciaPorGrupo = [];

        if ($docente) {
            $hoy = now()->toDateString();

            $misAsignaciones = AsignacionAcademica::with([
                    'materiaGrupo.grupo.gestion',
                    'materiaGrupo.materia',
                    'bloquesHorario',
                ])
                ->where('id_docente', $docente->id)
                ->where('estado', 'activo')
                ->get();

            $asignacionIds = $misAsignaciones->pluck('id');

            if ($asignacionIds->isNotEmpty()) {
                $totalClases      = ClaseProgramada::whereIn('id_asignacion', $asignacionIds)->count();
                $clasesRealizadas = ClaseProgramada::whereIn('id_asignacion', $asignacionIds)->where('estado', 'realizada')->count();
                $clasesPendientes = ClaseProgramada::whereIn('id_asignacion', $asignacionIds)->where('estado', 'programada')->count();
                $clasesCanceladas = ClaseProgramada::whereIn('id_asignacion', $asignacionIds)->where('estado', 'cancelada')->count();

                // Estudiantes únicos a cargo
                $grupoIds = $misAsignaciones->pluck('materiaGrupo.id_grupo')->unique()->filter();
                $totalEstudiantes = DB::table('admision')
                    ->whereIn('id_grupo', $grupoIds)
                    ->where('estado', 'cursando')
                    ->count();

                // Porcentaje de asistencia
                $claseIds = ClaseProgramada::whereIn('id_asignacion', $asignacionIds)->where('estado', 'realizada')->pluck('id');
                $totalRegistros = Asistencia::whereIn('id_clase', $claseIds)->count();
                $presentes      = Asistencia::whereIn('id_clase', $claseIds)->where('estado', 'presente')->count();
                $porcentaje     = $totalRegistros > 0 ? round(($presentes / $totalRegistros) * 100, 1) : 0;

                $stats = [
                    'total_estudiantes'     => $totalEstudiantes,
                    'clases_realizadas'     => $clasesRealizadas,
                    'clases_pendientes'     => $clasesPendientes,
                    'porcentaje_asistencia' => $porcentaje,
                    'total_clases'          => $totalClases,
                    'clases_canceladas'     => $clasesCanceladas,
                ];

                // Datos para gráfico de asistencia por asignación
                foreach ($misAsignaciones as $asig) {
                    $label   = ($asig->materiaGrupo->grupo->nombre ?? 'G') . ' - ' . ($asig->materiaGrupo->materia->nombre ?? 'M');
                    $ids     = ClaseProgramada::where('id_asignacion', $asig->id)->where('estado', 'realizada')->pluck('id');
                    $p = Asistencia::whereIn('id_clase', $ids)->where('estado', 'presente')->count();
                    $a = Asistencia::whereIn('id_clase', $ids)->where('estado', 'ausente')->count();
                    $j = Asistencia::whereIn('id_clase', $ids)->where('estado', 'justificado')->count();
                    $asistenciaPorGrupo[] = ['label' => $label, 'presentes' => $p, 'ausentes' => $a, 'justificados' => $j];
                }

                // Clases de hoy
                $clasesHoy = ClaseProgramada::with([
                        'asignacion.materiaGrupo.materia',
                        'asignacion.materiaGrupo.grupo',
                        'aula', 'bloque',
                    ])
                    ->whereIn('id_asignacion', $asignacionIds)
                    ->where('fecha', $hoy)
                    ->orderBy('id_bloque')
                    ->get();

                // Próximas clases
                $proximasClases = ClaseProgramada::with([
                        'asignacion.materiaGrupo.grupo',
                        'asignacion.materiaGrupo.materia',
                        'bloque',
                    ])
                    ->whereIn('id_asignacion', $asignacionIds)
                    ->where('fecha', '>', $hoy)
                    ->where('estado', 'programada')
                    ->orderBy('fecha')
                    ->take(5)
                    ->get();

                // Clases realizadas sin asistencia registrada
                $sinAsistencia = ClaseProgramada::with([
                        'asignacion.materiaGrupo.grupo',
                        'asignacion.materiaGrupo.materia',
                        'bloque',
                    ])
                    ->whereIn('id_asignacion', $asignacionIds)
                    ->where('estado', 'realizada')
                    ->whereDoesntHave('asistencias')
                    ->orderByDesc('fecha')
                    ->take(5)
                    ->get();
            }
        }

        return view('docente.dashboard', compact(
            'misAsignaciones', 'proximasClases', 'sinAsistencia',
            'stats', 'asistenciaPorGrupo', 'clasesHoy'
        ));
    }
}
