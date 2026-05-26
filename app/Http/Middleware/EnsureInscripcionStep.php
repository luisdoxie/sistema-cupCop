<?php

namespace App\Http\Middleware;

use App\Models\Admision;
use App\Models\Gestion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureInscripcionStep
{
    public function handle(Request $request, Closure $next, int $paso = 2): Response
    {
        // Verificar que el usuario está autenticado
        if (! Auth::check()) {
            return redirect()->route('inscripcion.paso1.create')
                ->with('error', 'Debe completar el registro primero.');
        }

        $user = Auth::user();

        // Verificar que es estudiante
        if ($user->rol !== 'estudiante') {
            abort(403, 'Solo los estudiantes pueden acceder a este proceso.');
        }

        // Para paso 3 en adelante, verificar admisión activa
        if ($paso >= 3) {
            $admisionId = session('inscripcion_admision_id');

            if (! $admisionId) {
                return redirect()->route('inscripcion.paso2.create')
                    ->with('error', 'Debe seleccionar sus carreras primero.');
            }

            $gestion = Gestion::where('estado', 'activo')->first();

            if (! $gestion) {
                return redirect()->route('inscripcion.paso1.create')
                    ->with('error', 'No hay una gestión activa en este momento.');
            }

            $admision = Admision::where('id', $admisionId)
                ->where('id_gestion', $gestion->id)
                ->first();

            if (! $admision) {
                return redirect()->route('inscripcion.paso2.create')
                    ->with('error', 'No se encontró una admisión válida. Inicie el proceso nuevamente.');
            }
        }

        return $next($request);
    }
}
