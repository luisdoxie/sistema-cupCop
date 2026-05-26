<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Gestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $estudiante = $user->estudiante;

        $admision = null;
        $materias = collect();
        $detalle  = collect();

        if ($estudiante) {
            $gestion  = Gestion::where('estado', 'activo')->first();
            if ($gestion) {
                $admision = Admision::where('id_estudiante', $estudiante->id)
                    ->where('id_gestion', $gestion->id)
                    ->first();

                if ($admision && $admision->id_grupo) {
                    // Resumen por materia
                    $materias = DB::select("
                        SELECT
                            m.nombre AS materia,
                            COUNT(DISTINCT cp.id)                                                         AS total_clases,
                            COUNT(CASE WHEN a.estado = 'presente'    THEN 1 END)                         AS presentes,
                            COUNT(CASE WHEN a.estado = 'ausente'     THEN 1 END)                         AS ausentes,
                            COUNT(CASE WHEN a.estado = 'justificado' THEN 1 END)                         AS justificados,
                            CASE WHEN COUNT(DISTINCT cp.id) > 0
                                 THEN ROUND(COUNT(CASE WHEN a.estado IN ('presente','justificado') THEN 1 END)::numeric
                                      / COUNT(DISTINCT cp.id) * 100, 1)
                                 ELSE 0
                            END AS porcentaje
                        FROM materia_grupo mg
                        INNER JOIN materia m ON m.id = mg.id_materia
                        INNER JOIN asignacion_academica aa ON aa.id_materia_grupo = mg.id
                        INNER JOIN clase_programada cp ON cp.id_asignacion = aa.id AND cp.estado = 'realizada'
                        LEFT  JOIN asistencia a ON a.id_clase = cp.id AND a.id_admision = ?
                        WHERE mg.id_grupo = ?
                        GROUP BY m.id, m.nombre
                        ORDER BY m.nombre
                    ", [$admision->id, $admision->id_grupo]);

                    // Detalle por clase
                    $detalle = DB::select("
                        SELECT
                            cp.fecha,
                            m.nombre AS materia,
                            COALESCE(a.estado, 'sin registro') AS estado
                        FROM materia_grupo mg
                        INNER JOIN materia m ON m.id = mg.id_materia
                        INNER JOIN asignacion_academica aa ON aa.id_materia_grupo = mg.id
                        INNER JOIN clase_programada cp ON cp.id_asignacion = aa.id AND cp.estado = 'realizada'
                        LEFT  JOIN asistencia a ON a.id_clase = cp.id AND a.id_admision = ?
                        WHERE mg.id_grupo = ?
                        ORDER BY cp.fecha DESC, m.nombre
                    ", [$admision->id, $admision->id_grupo]);
                }
            }
        }

        return view('estudiante.asistencia', compact('admision', 'materias', 'detalle'));
    }
}
