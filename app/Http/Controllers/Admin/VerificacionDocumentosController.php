<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\DocumentoPostulante;
use Illuminate\Http\Request;

class VerificacionDocumentosController extends Controller
{
    // Tipos de documentos obligatorios
    protected array $docsObligatorios = [
        'certificado_nacimiento',
        'certificado_colegio',
        'foto_carnet',
    ];

    public function index(Request $request)
    {
        $admisiones = Admision::with(['estudiante.persona', 'documentos'])
            ->whereHas('documentos', function ($q) {
                $q->where('estado_verificacion', 'pendiente');
            })
            ->orderBy('fecha')
            ->paginate(15)
            ->withQueryString();

        return view('admin.documentos.index', compact('admisiones'));
    }

    public function show(Admision $admision)
    {
        $admision->load(['estudiante.persona', 'documentos', 'gestion']);

        return view('admin.documentos.show', compact('admision'));
    }

    public function verificar(DocumentoPostulante $documento)
    {
        $documento->update(['estado_verificacion' => 'verificado']);

        $this->verificarCompletitud($documento->id_admision);

        return back()->with('success', 'Documento verificado exitosamente.');
    }

    public function rechazar(Request $request, DocumentoPostulante $documento)
    {
        $request->validate([
            'observacion' => 'required|string|max:500',
        ], [
            'observacion.required' => 'Debe ingresar una observación para rechazar el documento.',
        ]);

        $documento->update([
            'estado_verificacion' => 'rechazado',
            'observacion'         => $request->observacion,
        ]);

        return back()->with('success', 'Documento rechazado.');
    }

    protected function verificarCompletitud(int $admisionId): void
    {
        $admision = Admision::with('documentos')->find($admisionId);

        if (!$admision) {
            return;
        }

        $todosVerificados = true;
        foreach ($this->docsObligatorios as $tipo) {
            $doc = $admision->documentos->firstWhere('tipo_documento', $tipo);
            if (!$doc || $doc->estado_verificacion !== 'verificado') {
                $todosVerificados = false;
                break;
            }
        }

        if ($todosVerificados) {
            $admision->update(['estado' => 'cursando']);
        }
    }
}
