<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReporteExport;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

class ReporteVozController extends Controller
{
    public function index()
    {
        return view('admin.reportes.voz');
    }

    public function consultar(Request $request)
    {
        $request->validate(['texto' => 'required|string|max:500']);

        $texto = $request->input('texto');

        $systemPrompt = "Eres un asistente del sistema CUP de la FICCT Bolivia. " .
"Convierte la consulta del usuario en SQL valido para PostgreSQL. " .
"Solo puedes usar SELECT, nunca INSERT, UPDATE, DELETE ni DROP. " .
"Responde SOLO con el SQL, sin explicaciones, sin bloques markdown, sin backticks. " .
"Si no puedes generar SQL valido responde: ERROR: [motivo]. " .
"CRITICO: usa UNICAMENTE las columnas listadas. NO inventes columnas. " .
"Para conteos simples (cuantos docentes, estudiantes, etc.) usa las tablas base directamente. " .
"Si una vista no tiene 'anio', filtra por 'gestion' (texto) o 'id_gestion' (numero).\n\n" .
"TABLAS BASE:\n" .
"docente(id, id_persona, especialidad, grado_academico, anios_experiencia, estado)\n" .
"estudiante(id, id_persona, colegio_procedencia, ciudad)\n" .
"persona(id, ci, nombre, apellido, correo, rol, activo)\n" .
"gestion(id, nombre, anio, semestre, estado, fecha_inicio, fecha_fin)\n" .
"carrera(id, nombre, sigla)\n" .
"grupo(id, nombre, cupo_maximo, estado, id_gestion)\n" .
"admision(id, id_estudiante, id_gestion, id_grupo, id_carrera1, id_carrera2, estado, promedio_final)\n" .
"materia(id, nombre, sigla)\n" .
"nota(id, id_admision, id_examen, calificacion, estado)\n" .
"examen(id, nombre, tipo, id_materia_grupo)\n\n" .
"VISTAS (para reportes complejos):\n" .
"vw_lista_postulantes(ci, nombre, apellido, nombre_completo, correo, colegio, ciudad, " .
"carrera1, sigla_carrera1, carrera2, sigla_carrera2, estado, id_gestion, gestion, anio, carrera_asignada, fecha, id_admision)\n" .
"vw_notas_estudiante(ci, estudiante, materia, id_materia, id_materia_grupo, id_admision, " .
"id_gestion, gestion, p1, p2, final_nota, total, promedio, resultado)\n" .
"vw_estadisticas_materia(id_materia, materia, id_gestion, gestion, total_estudiantes, " .
"promedio, nota_max, nota_min, aprobados, reprobados)\n" .
"vw_grupos_habilitados(id_gestion, gestion, anio, total_grupos, capacidad_total, estudiantes_asignados, total_docentes)\n" .
"vw_rendimiento_docente(docente, id_docente, materia, id_materia, gestion, id_gestion, " .
"total_estudiantes, aprobados, porcentaje_aprobacion)\n" .
"vw_reporte_admision_gestion(id_gestion, gestion, anio, semestre, postulantes, admitidos, reprobados, sin_cupo, porcentaje_admision)";

        try {
            $res = Http::withToken(env('GROQ_API_KEY'))
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'    => 'llama-3.3-70b-versatile',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $texto],
                    ],
                ]);

            if ($res->failed()) {
                return response()->json(['error' => 'Error al conectar con Groq: ' . $res->body()], 500);
            }

            $sql = trim($res->json('choices.0.message.content') ?? '');
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al conectar con Groq: ' . $e->getMessage()], 500);
        }

        if (str_starts_with($sql, 'ERROR:')) {
            return response()->json(['error' => $sql]);
        }

        if (!preg_match('/^\s*SELECT\b/i', $sql)) {
            return response()->json(['error' => 'ERROR: Solo se permiten consultas SELECT.']);
        }

        try {
            $resultados = DB::select($sql);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'ERROR al ejecutar SQL: ' . $e->getMessage()]);
        }

        $cantidad = count($resultados);
        $data     = array_map(fn($row) => (array) $row, $resultados);

        Log::channel('voz_consultas')->info('consulta_voz', [
            'fecha'     => now()->toDateTimeString(),
            'texto'     => $texto,
            'sql'       => $sql,
            'resultados' => $cantidad,
        ]);

        $historial = session('voz_historial', []);
        array_unshift($historial, ['texto' => $texto, 'sql' => $sql, 'cantidad' => $cantidad]);
        $historial = array_slice($historial, 0, 5);
        session(['voz_historial' => $historial]);

        return response()->json([
            'sql'        => $sql,
            'resultados' => $data,
            'cantidad'   => $cantidad,
            'historial'  => $historial,
        ]);
    }

    public function exportarPdf(Request $request)
    {
        $request->validate(['sql' => 'required|string', 'texto' => 'required|string']);

        $sql = $request->input('sql');

        if (!preg_match('/^\s*SELECT\b/i', $sql)) {
            abort(403, 'Solo se permiten consultas SELECT.');
        }

        $resultados = DB::select($sql);
        $data       = array_map(fn($row) => (array) $row, $resultados);
        $columnas   = !empty($data) ? array_keys($data[0]) : [];
        $texto      = $request->input('texto');

        $pdf = Pdf::loadView('admin.reportes.pdf.voz', compact('data', 'columnas', 'texto', 'sql'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('reporte_ia.pdf');
    }

    public function exportarExcel(Request $request)
    {
        $request->validate(['sql' => 'required|string']);

        $sql = $request->input('sql');

        if (!preg_match('/^\s*SELECT\b/i', $sql)) {
            abort(403, 'Solo se permiten consultas SELECT.');
        }

        $resultados = DB::select($sql);
        $data       = array_map(fn($row) => (array) $row, $resultados);
        $headers    = !empty($data) ? array_keys($data[0]) : [];
        $rows       = array_map('array_values', $data);

        return Excel::download(new ReporteExport($headers, $rows), 'reporte_ia.xlsx');
    }
}
