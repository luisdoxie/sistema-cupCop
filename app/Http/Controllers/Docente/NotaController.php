<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Examen;
use App\Models\Grupo;
use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotaController extends Controller
{
    public function index()
    {
        $docente = Auth::user()->docente;

        $grupos = Grupo::with(['gestion','materiaGrupos.materia'])
            ->whereHas('materiaGrupos.asignaciones', function ($q) use ($docente) {
                $q->where('id_docente', $docente->id)->where('estado', 'activo');
            })
            ->get();

        return view('docente.notas.index', compact('grupos'));
    }

    private function verificarAcceso(Grupo $grupo): void
    {
        $docente = Auth::user()->docente;

        if (!$docente) {
            abort(403, 'No tiene perfil de docente.');
        }

        $tieneAcceso = $grupo->materiaGrupos()
            ->whereHas('asignaciones', function ($q) use ($docente) {
                $q->where('id_docente', $docente->id)
                  ->where('estado', 'activo');
            })
            ->exists();

        if (!$tieneAcceso) {
            abort(403, 'No tiene asignación en este grupo.');
        }
    }

    public function planilla(Grupo $grupo)
    {
        $this->verificarAcceso($grupo);

        $grupo->load([
            'gestion',
            'materiaGrupos.materia',
            'materiaGrupos.examenes',
        ]);

        $admisiones = Admision::with(['estudiante.persona'])
            ->where('id_grupo', $grupo->id)
            ->whereIn('estado', ['cursando', 'admitido_carrera1', 'admitido_carrera2', 'reprobado', 'no_admitido'])
            ->orderBy('id')
            ->get();

        // Build a lookup: notas[id_examen][id_admision] = nota
        $todasLasNotas = Nota::whereIn(
            'id_examen',
            $grupo->materiaGrupos->flatMap(fn($mg) => $mg->examenes->pluck('id'))
        )
        ->whereIn('id_admision', $admisiones->pluck('id'))
        ->get()
        ->groupBy('id_examen');

        return view('docente.notas.planilla', compact('grupo', 'admisiones', 'todasLasNotas'));
    }

    public function guardar(Request $request, Grupo $grupo)
    {
        $this->verificarAcceso($grupo);

        $notasInput = $request->input('notas', []);

        // Load exámenes del grupo para validar puntaje_maximo
        $examenes = Examen::whereIn(
            'id_materia_grupo',
            $grupo->materiaGrupos()->pluck('id')
        )->get()->keyBy('id');

        $admisiones = Admision::where('id_grupo', $grupo->id)
            ->where('estado', 'cursando')
            ->pluck('id')
            ->toArray();

        foreach ($notasInput as $idExamen => $porAdmision) {
            if (!isset($examenes[$idExamen])) {
                continue;
            }
            $examen = $examenes[$idExamen];

            foreach ($porAdmision as $idAdmision => $calificacion) {
                if (!in_array($idAdmision, $admisiones)) {
                    continue;
                }
                if ($calificacion === null || $calificacion === '') {
                    continue;
                }
                $cal = (float) $calificacion;
                if ($cal < 0 || $cal > $examen->puntaje_maximo) {
                    continue;
                }

                Nota::updateOrCreate(
                    ['id_examen' => $idExamen, 'id_admision' => $idAdmision],
                    ['calificacion' => $cal, 'estado' => 'registrada']
                );
            }
        }

        // Después de guardar: verificar si todos los estudiantes tienen notas
        // en los 3 exámenes de cada materia → marcar examen como 'realizado'
        $grupo->load('materiaGrupos.examenes');
        $totalAdmisiones = count($admisiones);

        foreach ($grupo->materiaGrupos as $mg) {
            foreach ($mg->examenes as $examen) {
                $cantNotas = Nota::where('id_examen', $examen->id)
                    ->whereIn('id_admision', $admisiones)
                    ->count();

                if ($totalAdmisiones > 0 && $cantNotas >= $totalAdmisiones) {
                    $examen->update(['estado' => 'realizado']);
                }
            }
        }

        return redirect()->route('docente.notas.planilla', $grupo)
            ->with('success', 'Notas guardadas correctamente.');
    }
}
