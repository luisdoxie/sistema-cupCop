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

        $systemPrompt = "Eres un asistente del sistema CUP de la FICCT Bolivia.
Convierte la consulta del usuario en SQL valido para PostgreSQL.
Solo puedes usar SELECT, nunca INSERT, UPDATE, DELETE ni DROP.
Vistas disponibles: vw_lista_postulantes, vw_notas_estudiante,
vw_rendimiento_docente, vw_estadisticas_materia,
vw_grupos_habilitados, vw_reporte_admision_gestion.
Responde SOLO con el SQL sin explicaciones ni backticks.
Si no puedes generar SQL valido responde: ERROR: [motivo]";

        try {
            $res = Http::withToken(env('DEEPSEEK_API_KEY'))
                ->post('https://api.deepseek.com/chat/completions', [
                    'model'    => 'deepseek-chat',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $texto],
                    ],
                ]);

            if ($res->failed()) {
                return response()->json(['error' => 'Error al conectar con DeepSeek: ' . $res->body()], 500);
            }

            $sql = trim($res->json('choices.0.message.content') ?? '');
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al conectar con DeepSeek: ' . $e->getMessage()], 500);
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
