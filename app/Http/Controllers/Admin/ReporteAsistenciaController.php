<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\ReporteExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteAsistenciaController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = DB::table('admision as a')
            ->join('estudiante as e',    'e.id',  '=', 'a.id_estudiante')
            ->join('persona as p',       'p.id',  '=', 'e.id_persona')
            ->join('grupo as gr',        'gr.id', '=', 'a.id_grupo')
            ->join('gestion as g',       'g.id',  '=', 'a.id_gestion')
            ->join('materia_grupo as mg','mg.id_grupo', '=', 'gr.id')
            ->join('materia as m',       'm.id',  '=', 'mg.id_materia')
            ->join('asignacion_academica as aa', 'aa.id_materia_grupo', '=', 'mg.id')
            ->join('clase_programada as cp', function ($j) {
                $j->on('cp.id_asignacion', '=', 'aa.id')
                  ->where('cp.estado', '=', 'realizada');
            })
            ->leftJoin('asistencia as ast', function ($j) {
                $j->on('ast.id_clase',    '=', 'cp.id')
                  ->on('ast.id_admision', '=', 'a.id');
            })
            ->whereNotNull('a.id_grupo')
            ->select([
                'p.ci',
                DB::raw("p.nombre || ' ' || p.apellido AS estudiante"),
                'm.nombre AS materia',
                'g.nombre AS gestion',
                'gr.nombre AS grupo',
                DB::raw("COUNT(DISTINCT cp.id) AS total_clases"),
                DB::raw("COUNT(CASE WHEN ast.estado = 'presente'    THEN 1 END) AS presentes"),
                DB::raw("COUNT(CASE WHEN ast.estado = 'ausente'     THEN 1 END) AS ausentes"),
                DB::raw("COUNT(CASE WHEN ast.estado = 'justificado' THEN 1 END) AS justificados"),
                DB::raw("CASE WHEN COUNT(DISTINCT cp.id) > 0
                    THEN ROUND(COUNT(CASE WHEN ast.estado IN ('presente','justificado') THEN 1 END)::numeric
                         / COUNT(DISTINCT cp.id) * 100, 1)
                    ELSE 0 END AS porcentaje"),
            ])
            ->groupBy('p.ci','p.nombre','p.apellido','m.nombre','g.nombre','gr.nombre')
            ->orderBy('p.apellido')
            ->orderBy('m.nombre');

        if ($request->filled('id_gestion')) $query->where('a.id_gestion', $request->id_gestion);
        if ($request->filled('id_grupo'))   $query->where('a.id_grupo',   $request->id_grupo);
        if ($request->filled('id_materia')) $query->where('m.id',         $request->id_materia);
        if ($request->filled('fecha_desde')) $query->where('cp.fecha', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta')) $query->where('cp.fecha', '<=', $request->fecha_hasta);

        return $query;
    }

    public function index(Request $request)
    {
        $gestiones = DB::table('gestion')->orderByDesc('anio')->get();
        $grupos    = DB::table('grupo')->orderBy('nombre')->get();
        $materias  = DB::table('materia')->orderBy('nombre')->get();

        $registros = $this->buildQuery($request)->paginate(20)->withQueryString();

        return view('admin.reportes.asistencia', compact('registros', 'gestiones', 'grupos', 'materias'));
    }

    public function pdf(Request $request)
    {
        $registros = $this->buildQuery($request)->get();
        $filtros   = $request->only(['id_gestion','id_grupo','id_materia','fecha_desde','fecha_hasta']);
        $pdf = Pdf::loadView('admin.reportes.pdf.asistencia', compact('registros','filtros'))
                  ->setPaper('a4','landscape');
        return $pdf->stream('asistencia.pdf');
    }

    public function excel(Request $request)
    {
        $rows    = $this->buildQuery($request)->get();
        $headers = ['CI','Estudiante','Materia','Gestión','Grupo','Total Clases','Presentes','Ausentes','Justificados','% Asistencia'];
        $data    = $rows->map(fn($r) => [
            $r->ci, $r->estudiante, $r->materia, $r->gestion, $r->grupo,
            $r->total_clases, $r->presentes, $r->ausentes, $r->justificados, $r->porcentaje.'%',
        ])->toArray();
        return Excel::download(new ReporteExport($headers, $data), 'asistencia.xlsx');
    }
}
