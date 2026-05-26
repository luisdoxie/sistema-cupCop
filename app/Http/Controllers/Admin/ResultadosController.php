<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Gestion;
use App\Services\AdmisionFinalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ResultadosController extends Controller
{
    public function index(Request $request)
    {
        $gestion = Gestion::where('estado', 'activo')->first();

        $query = Admision::with(['estudiante.persona', 'carrera1', 'carrera2'])
            ->whereIn('estado', [
                'aprobado', 'admitido_carrera1', 'admitido_carrera2',
                'reprobado', 'no_admitido', 'cursando',
            ]);

        if ($gestion) {
            $query->where('id_gestion', $gestion->id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('id_carrera')) {
            $query->where(function ($q) use ($request) {
                $q->where('id_carrera1', $request->id_carrera)
                  ->orWhere('id_carrera2', $request->id_carrera);
            });
        }

        $admisiones = $query->orderByDesc('promedio_final')->paginate(15)->withQueryString();

        // Resumen estadístico
        $resumen = null;
        if ($gestion) {
            $resumen = [
                'total'        => Admision::where('id_gestion', $gestion->id)->count(),
                'admitidos_c1' => Admision::where('id_gestion', $gestion->id)->where('estado', 'admitido_carrera1')->count(),
                'admitidos_c2' => Admision::where('id_gestion', $gestion->id)->where('estado', 'admitido_carrera2')->count(),
                'reprobados'   => Admision::where('id_gestion', $gestion->id)->where('estado', 'reprobado')->count(),
                'no_admitido'  => Admision::where('id_gestion', $gestion->id)->where('estado', 'no_admitido')->count(),
                'cursando'     => Admision::where('id_gestion', $gestion->id)->where('estado', 'cursando')->count(),
            ];
        }

        return view('admin.resultados.index', compact('admisiones', 'gestion', 'resumen'));
    }

    public function procesar()
    {
        $gestion = Gestion::where('estado', 'activo')->firstOrFail();

        $service = new AdmisionFinalService();
        $resultado = $service->procesarGestion($gestion->id);

        $msg = "Procesado: {$resultado['total']} admisiones. "
            . "C1: {$resultado['admitidos_c1']}, C2: {$resultado['admitidos_c2']}, "
            . "Reprobados: {$resultado['reprobados']}, Sin cupo: {$resultado['no_admitido']}";

        return redirect()->route('admin.resultados.index')->with('success', $msg);
    }

    public function exportarPdf(Request $request)
    {
        $gestion = Gestion::where('estado', 'activo')->first();

        $admisiones = Admision::with(['estudiante.persona', 'carrera1', 'carrera2'])
            ->whereIn('estado', ['admitido_carrera1', 'admitido_carrera2'])
            ->when($gestion, fn($q) => $q->where('id_gestion', $gestion->id))
            ->orderByDesc('promedio_final')
            ->get();

        $pdf = Pdf::loadView('admin.resultados.pdf', compact('admisiones', 'gestion'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('admitidos-' . now()->format('Y-m-d') . '.pdf');
    }
}
