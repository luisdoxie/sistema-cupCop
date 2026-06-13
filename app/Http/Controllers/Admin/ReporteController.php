<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\ReporteExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    // ── Reporte 1: Lista de postulantes ─────────────────────────────────────────

    public function postulantes(Request $request)
    {
        $gestiones = DB::table('gestion')->orderByDesc('anio')->get();
        $carreras  = DB::table('carrera')->orderBy('nombre')->get();

        $query = DB::table('vw_lista_postulantes')->orderBy('apellido');

        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('estado'))     $query->where('estado', $request->estado);
        if ($request->filled('id_carrera')) {
            $query->where(function ($q) use ($request) {
                $q->where('carrera1', DB::table('carrera')->where('id', $request->id_carrera)->value('nombre'))
                  ->orWhere('carrera2', DB::table('carrera')->where('id', $request->id_carrera)->value('nombre'));
            });
        }
        if ($request->filled('ciudad'))   $query->where('ciudad', 'ilike', '%'.$request->ciudad.'%');
        if ($request->filled('colegio'))  $query->where('colegio', 'ilike', '%'.$request->colegio.'%');

        $registros = $query->paginate(20)->withQueryString();

        return view('admin.reportes.postulantes', compact('registros', 'gestiones', 'carreras'));
    }

    public function postulantePdf(Request $request)
    {
        $query = DB::table('vw_lista_postulantes')->orderBy('apellido');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('estado'))     $query->where('estado', $request->estado);
        if ($request->filled('ciudad'))     $query->where('ciudad', 'ilike', '%'.$request->ciudad.'%');
        if ($request->filled('colegio'))    $query->where('colegio', 'ilike', '%'.$request->colegio.'%');
        $registros = $query->get();
        $filtros   = $request->only(['id_gestion','estado','ciudad','colegio']);
        $pdf = Pdf::loadView('admin.reportes.pdf.postulantes', compact('registros','filtros'))
                  ->setPaper('a4','landscape');
        return $pdf->stream('postulantes.pdf');
    }

    public function postulantesExcel(Request $request)
    {
        $query = DB::table('vw_lista_postulantes')->orderBy('apellido');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('estado'))     $query->where('estado', $request->estado);
        if ($request->filled('ciudad'))     $query->where('ciudad', 'ilike', '%'.$request->ciudad.'%');
        if ($request->filled('colegio'))    $query->where('colegio', 'ilike', '%'.$request->colegio.'%');
        $rows    = $query->get();
        $headers = ['CI','Nombre','Apellido','Correo','Colegio','Ciudad','Carrera 1','Carrera 2','Estado','Carrera Asignada'];
        $data    = $rows->map(fn($r) => [
            $r->ci, $r->nombre, $r->apellido, $r->correo,
            $r->colegio, $r->ciudad, $r->carrera1, $r->carrera2,
            $r->estado, $r->carrera_asignada,
        ])->toArray();
        return Excel::download(new ReporteExport($headers, $data), 'postulantes.xlsx');
    }

    public function postulantesCSV(Request $request)
    {
        $query = DB::table('vw_lista_postulantes')->orderBy('apellido');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('estado'))     $query->where('estado', $request->estado);
        $rows    = $query->get();
        $headers = ['CI','Nombre','Apellido','Correo','Colegio','Ciudad','Carrera 1','Carrera 2','Estado','Carrera Asignada'];
        $data    = $rows->map(fn($r) => [
            $r->ci,$r->nombre,$r->apellido,$r->correo,
            $r->colegio,$r->ciudad,$r->carrera1,$r->carrera2,
            $r->estado,$r->carrera_asignada,
        ])->toArray();
        return Excel::download(new ReporteExport($headers, $data), 'postulantes.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    // ── Reporte 2: Notas ─────────────────────────────────────────────────────────

    public function notas(Request $request)
    {
        $gestiones = DB::table('gestion')->orderByDesc('anio')->get();
        $materias  = DB::table('materia')->orderBy('nombre')->get();

        $query = DB::table('vw_notas_estudiante')->orderBy('estudiante');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('id_materia')) $query->where('id_materia', $request->id_materia);
        if ($request->filled('resultado'))  $query->where('resultado', $request->resultado);

        $registros = $query->paginate(20)->withQueryString();

        return view('admin.reportes.notas', compact('registros', 'gestiones', 'materias'));
    }

    public function notasPdf(Request $request)
    {
        $query = DB::table('vw_notas_estudiante')->orderBy('estudiante');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('id_materia')) $query->where('id_materia', $request->id_materia);
        if ($request->filled('resultado'))  $query->where('resultado', $request->resultado);
        $registros = $query->get();
        $filtros   = $request->only(['id_gestion','id_materia','resultado']);
        $pdf = Pdf::loadView('admin.reportes.pdf.notas', compact('registros','filtros'))
                  ->setPaper('a4','landscape');
        return $pdf->stream('notas.pdf');
    }

    public function notasExcel(Request $request)
    {
        $query = DB::table('vw_notas_estudiante')->orderBy('estudiante');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('id_materia')) $query->where('id_materia', $request->id_materia);
        if ($request->filled('resultado'))  $query->where('resultado', $request->resultado);
        $rows    = $query->get();
        $headers = ['CI','Estudiante','Materia','Gestión','P1','P2','Final','Promedio Final','Resultado'];
        $data    = $rows->map(fn($r) => [
            $r->ci,$r->estudiante,$r->materia,$r->gestion,
            $r->p1,$r->p2,$r->final_nota,
            $r->promedio !== null ? round($r->promedio, 2) : null,
            $r->resultado,
        ])->toArray();
        return Excel::download(new ReporteExport($headers, $data), 'notas.xlsx');
    }

    // ── Reporte 3: Estadísticas por materia ─────────────────────────────────────

    public function estadisticas(Request $request)
    {
        $gestiones = DB::table('gestion')->orderByDesc('anio')->get();
        $materias  = DB::table('materia')->orderBy('nombre')->get();

        $query = DB::table('vw_estadisticas_materia')->orderBy('materia');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('id_materia')) $query->where('id_materia', $request->id_materia);

        $registros  = $query->paginate(20)->withQueryString();
        $chartData  = DB::table('vw_estadisticas_materia')
            ->when($request->filled('id_gestion'), fn($q) => $q->where('id_gestion', $request->id_gestion))
            ->get();

        return view('admin.reportes.estadisticas', compact('registros', 'gestiones', 'materias', 'chartData'));
    }

    public function estadisticasPdf(Request $request)
    {
        $query = DB::table('vw_estadisticas_materia')->orderBy('materia');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('id_materia')) $query->where('id_materia', $request->id_materia);
        $registros = $query->get();
        $filtros   = $request->only(['id_gestion','id_materia']);
        $pdf = Pdf::loadView('admin.reportes.pdf.estadisticas', compact('registros','filtros'))
                  ->setPaper('a4','portrait');
        return $pdf->stream('estadisticas.pdf');
    }

    public function estadisticasExcel(Request $request)
    {
        $query = DB::table('vw_estadisticas_materia')->orderBy('materia');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        $rows    = $query->get();
        $headers = ['Materia','Gestión','Total Estudiantes','Promedio','Nota Máx','Nota Mín','Aprobados','Reprobados'];
        $data    = $rows->map(fn($r) => [
            $r->materia,$r->gestion,$r->total_estudiantes,
            $r->promedio,$r->nota_max,$r->nota_min,$r->aprobados,$r->reprobados,
        ])->toArray();
        return Excel::download(new ReporteExport($headers, $data), 'estadisticas.xlsx');
    }

    // ── Reporte 4: Grupos habilitados ────────────────────────────────────────────

    public function grupos(Request $request)
    {
        $gestiones = DB::table('gestion')->orderByDesc('anio')->get();
        $divisor   = DB::table('config_sistema')->where('clave','divisor_grupos')->value('valor') ?? 70;

        $query = DB::table('vw_grupos_habilitados')->orderByDesc('anio');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);

        $registros = $query->paginate(20)->withQueryString();

        return view('admin.reportes.grupos', compact('registros', 'gestiones', 'divisor'));
    }

    public function gruposPdf(Request $request)
    {
        $query = DB::table('vw_grupos_habilitados')->orderByDesc('anio');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        $registros = $query->get();
        $divisor   = DB::table('config_sistema')->where('clave','divisor_grupos')->value('valor') ?? 70;
        $filtros   = $request->only(['id_gestion']);
        $pdf = Pdf::loadView('admin.reportes.pdf.grupos', compact('registros','filtros','divisor'))
                  ->setPaper('a4','portrait');
        return $pdf->stream('grupos.pdf');
    }

    public function gruposExcel(Request $request)
    {
        $query = DB::table('vw_grupos_habilitados')->orderByDesc('anio');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        $rows    = $query->get();
        $divisor = DB::table('config_sistema')->where('clave','divisor_grupos')->value('valor') ?? 70;
        $headers = ['Gestión','Año','Total Grupos','Capacidad Total','Estudiantes Asignados','Total Docentes','Grupos Necesarios'];
        $data    = $rows->map(fn($r) => [
            $r->gestion, $r->anio, $r->total_grupos, $r->capacidad_total,
            $r->estudiantes_asignados, $r->total_docentes,
            ceil($r->estudiantes_asignados / $divisor),
        ])->toArray();
        return Excel::download(new ReporteExport($headers, $data), 'grupos.xlsx');
    }

    // ── Reporte 5: Rendimiento por docente ──────────────────────────────────────

    public function docentes(Request $request)
    {
        $gestiones = DB::table('gestion')->orderByDesc('anio')->get();
        $materias  = DB::table('materia')->orderBy('nombre')->get();
        $listaDoc  = DB::table('docente')
            ->join('persona','persona.id','=','docente.id_persona')
            ->orderBy('persona.apellido')
            ->get(['docente.id','persona.nombre','persona.apellido']);

        $query = DB::table('vw_rendimiento_docente')
            ->orderByDesc('porcentaje_aprobacion');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('id_materia')) $query->where('id_materia', $request->id_materia);
        if ($request->filled('id_docente')) $query->where('id_docente', $request->id_docente);

        $registros = $query->paginate(20)->withQueryString();

        return view('admin.reportes.docentes', compact('registros', 'gestiones', 'materias', 'listaDoc'));
    }

    public function docentesPdf(Request $request)
    {
        $query = DB::table('vw_rendimiento_docente')->orderByDesc('porcentaje_aprobacion');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('id_materia')) $query->where('id_materia', $request->id_materia);
        if ($request->filled('id_docente')) $query->where('id_docente', $request->id_docente);
        $registros = $query->get();
        $filtros   = $request->only(['id_gestion','id_materia','id_docente']);
        $pdf = Pdf::loadView('admin.reportes.pdf.docentes', compact('registros','filtros'))
                  ->setPaper('a4','portrait');
        return $pdf->stream('rendimiento_docentes.pdf');
    }

    public function docentesExcel(Request $request)
    {
        $query = DB::table('vw_rendimiento_docente')->orderByDesc('porcentaje_aprobacion');
        if ($request->filled('id_gestion')) $query->where('id_gestion', $request->id_gestion);
        if ($request->filled('id_materia')) $query->where('id_materia', $request->id_materia);
        $rows    = $query->get();
        $headers = ['Docente','Materia','Gestión','Total Estudiantes','Aprobados','% Aprobación'];
        $data    = $rows->map(fn($r) => [
            $r->docente,$r->materia,$r->gestion,
            $r->total_estudiantes,$r->aprobados,$r->porcentaje_aprobacion,
        ])->toArray();
        return Excel::download(new ReporteExport($headers, $data), 'rendimiento_docentes.xlsx');
    }

    // ── Reporte 6: Comparativa entre gestiones ──────────────────────────────────

    public function gestiones(Request $request)
    {
        $anioMin = DB::table('gestion')->min('anio');
        $anioMax = DB::table('gestion')->max('anio');

        $desde = $request->input('desde', $anioMin);
        $hasta = $request->input('hasta', $anioMax);

        $query = DB::table('vw_reporte_admision_gestion')
            ->whereBetween('anio', [$desde, $hasta])
            ->orderByDesc('anio');

        $registros  = $query->paginate(20)->withQueryString();
        $chartData  = DB::table('vw_reporte_admision_gestion')
            ->whereBetween('anio', [$desde, $hasta])
            ->orderBy('anio')
            ->get();

        return view('admin.reportes.gestiones', compact('registros', 'chartData', 'desde', 'hasta', 'anioMin', 'anioMax'));
    }

    public function gestionesPdf(Request $request)
    {
        $desde = $request->input('desde', DB::table('gestion')->min('anio'));
        $hasta = $request->input('hasta', DB::table('gestion')->max('anio'));
        $registros = DB::table('vw_reporte_admision_gestion')
            ->whereBetween('anio', [$desde, $hasta])
            ->orderByDesc('anio')->get();
        $filtros = compact('desde','hasta');
        $pdf = Pdf::loadView('admin.reportes.pdf.gestiones', compact('registros','filtros'))
                  ->setPaper('a4','portrait');
        return $pdf->stream('comparativa_gestiones.pdf');
    }

    public function gestionesExcel(Request $request)
    {
        $desde = $request->input('desde', DB::table('gestion')->min('anio'));
        $hasta = $request->input('hasta', DB::table('gestion')->max('anio'));
        $rows  = DB::table('vw_reporte_admision_gestion')
            ->whereBetween('anio', [$desde, $hasta])
            ->orderByDesc('anio')->get();
        $headers = ['Gestión','Año','Semestre','Postulantes','Admitidos','Reprobados','Sin Cupo','% Admisión'];
        $data    = $rows->map(fn($r) => [
            $r->gestion,$r->anio,$r->semestre,
            $r->postulantes,$r->admitidos,$r->reprobados,$r->sin_cupo,$r->porcentaje_admision,
        ])->toArray();
        return Excel::download(new ReporteExport($headers, $data), 'comparativa_gestiones.xlsx');
    }

    public function gestionesTxt(Request $request)
    {
        $desde = $request->input('desde', DB::table('gestion')->min('anio'));
        $hasta = $request->input('hasta', DB::table('gestion')->max('anio'));
        $rows  = DB::table('vw_reporte_admision_gestion')
            ->whereBetween('anio', [$desde, $hasta])
            ->orderByDesc('anio')->get();

        $txt  = "SISTEMA CUP - COMPARATIVA ENTRE GESTIONES\n";
        $txt .= "Generado: " . now()->format('d/m/Y H:i') . "\n";
        $txt .= "Período: {$desde} - {$hasta}\n";
        $txt .= str_repeat('-', 80) . "\n";
        $txt .= sprintf("%-20s %-6s %-4s %-12s %-10s %-12s %-10s %-10s\n",
            'GESTIÓN','AÑO','SEM','POSTULANTES','ADMITIDOS','REPROBADOS','SIN CUPO','% ADMIS');
        $txt .= str_repeat('-', 80) . "\n";
        foreach ($rows as $r) {
            $txt .= sprintf("%-20s %-6s %-4s %-12s %-10s %-12s %-10s %-10s\n",
                $r->gestion, $r->anio, $r->semestre,
                $r->postulantes, $r->admitidos, $r->reprobados,
                $r->sin_cupo, $r->porcentaje_admision.'%');
        }

        return response($txt, 200, [
            'Content-Type'        => 'text/plain',
            'Content-Disposition' => 'attachment; filename="comparativa_gestiones.txt"',
        ]);
    }
}
