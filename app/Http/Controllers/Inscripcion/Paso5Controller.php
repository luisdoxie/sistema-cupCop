<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use App\Mail\InscripcionConfirmada;
use App\Models\Admision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Paso5Controller extends Controller
{
    public function index()
    {
        $admisionId = session('inscripcion_admision_id');
        $admision   = Admision::with([
            'estudiante.persona',
            'carrera1',
            'carrera2',
            'gestion',
            'documentos',
            'pago',
        ])->findOrFail($admisionId);

        // Enviar email de confirmación si el pago está completado
        if ($admision->pago && $admision->pago->estado_pago === 'completado') {
            try {
                $correo = Auth::user()->correo;
                if ($correo) {
                    Mail::to($correo)->send(new InscripcionConfirmada($admision));
                }
            } catch (\Throwable $e) {
                // El correo falla silenciosamente para no bloquear la confirmación
                logger()->warning('No se pudo enviar el email de confirmación: ' . $e->getMessage());
            }
        }

        return view('inscripcion.paso5', [
            'admision' => $admision,
            'paso'     => 5,
        ]);
    }
}
