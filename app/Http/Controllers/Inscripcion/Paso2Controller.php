<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inscripcion\Paso2Request;
use App\Models\Admision;
use App\Models\CarreraGestion;
use App\Models\Gestion;
use Illuminate\Support\Facades\Auth;

class Paso2Controller extends Controller
{
    public function create()
    {
        $gestion = Gestion::where('estado', 'activo')->first();

        if (! $gestion) {
            return view('inscripcion.paso2', [
                'carreras' => collect(),
                'gestion'  => null,
                'paso'     => 2,
            ])->with('warning', 'No hay una gestión activa actualmente.');
        }

        // Obtener carreras con cupo disponible en la gestión activa
        $carrerasGestion = CarreraGestion::with('carrera')
            ->where('id_gestion', $gestion->id)
            ->where('cupo_disponible', '>', 0)
            ->get();

        $carreras = $carrerasGestion->map(fn ($cg) => $cg->carrera)->filter()->values();

        return view('inscripcion.paso2', [
            'carreras' => $carreras,
            'gestion'  => $gestion,
            'paso'     => 2,
        ]);
    }

    public function store(Paso2Request $request)
    {
        $gestion = Gestion::where('estado', 'activo')->firstOrFail();

        $estudiante = Auth::user()->estudiante;

        // Verificar si ya tiene admisión en esta gestión
        $admisionExistente = Admision::where('id_estudiante', $estudiante->id)
            ->where('id_gestion', $gestion->id)
            ->first();

        if ($admisionExistente) {
            session()->put('inscripcion_admision_id', $admisionExistente->id);
            return redirect()->route('inscripcion.paso3.create')
                ->with('info', 'Ya tenía una admisión registrada. Continuando con el proceso.');
        }

        $admision = Admision::create([
            'id_estudiante' => $estudiante->id,
            'id_gestion'    => $gestion->id,
            'id_grupo'      => null,
            'id_carrera1'   => $request->id_carrera1,
            'id_carrera2'   => $request->id_carrera2,
            'fecha'         => now()->toDateString(),
            'estado'        => 'inscrito',
            'promedio_final'=> null,
        ]);

        session()->put('inscripcion_admision_id', $admision->id);

        return redirect()->route('inscripcion.paso3.create')
            ->with('success', 'Selección de carreras guardada correctamente.');
    }
}
