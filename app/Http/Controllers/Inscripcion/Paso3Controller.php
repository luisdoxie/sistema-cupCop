<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inscripcion\Paso3Request;
use App\Models\Admision;
use App\Models\DocumentoPostulante;
use Illuminate\Support\Facades\Storage;

class Paso3Controller extends Controller
{
    public function create()
    {
        $admisionId = session('inscripcion_admision_id');
        $admision   = Admision::with(['carrera1', 'carrera2'])->findOrFail($admisionId);

        // Obtener documentos ya subidos
        $documentosSubidos = $admision->documentos->keyBy('tipo_documento');

        return view('inscripcion.paso3', [
            'admision'         => $admision,
            'documentosSubidos'=> $documentosSubidos,
            'paso'             => 3,
        ]);
    }

    public function store(Paso3Request $request)
    {
        $admisionId = session('inscripcion_admision_id');
        $admision   = Admision::findOrFail($admisionId);

        $tiposDocumentos = [
            'certificado_nacimiento',
            'fotocopia_carnet',
            'libreta_colegio',
            'titulo_bachiller',
        ];

        foreach ($tiposDocumentos as $tipo) {
            if ($request->hasFile($tipo)) {
                $archivo = $request->file($tipo);
                $ruta    = $archivo->store("documentos/{$admisionId}", 'local');

                // Actualizar o crear el registro del documento
                DocumentoPostulante::updateOrCreate(
                    [
                        'id_admision'    => $admision->id,
                        'tipo_documento' => $tipo,
                    ],
                    [
                        'ruta_archivo'        => $ruta,
                        'estado_verificacion' => 'pendiente',
                        'observacion'         => null,
                        'fecha_entrega'       => now()->toDateString(),
                    ]
                );
            }
        }

        // Actualizar estado de admisión a documentos_pendientes
        $admision->update(['estado' => 'documentos_pendientes']);

        return redirect()->route('inscripcion.paso4.create')
            ->with('success', 'Documentos subidos correctamente.');
    }
}
