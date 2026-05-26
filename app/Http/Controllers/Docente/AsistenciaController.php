<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Asistencia;
use App\Models\ClaseProgramada;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    public function paseLista(ClaseProgramada $clase)
    {
        $docente = Auth::user()->docente;

        // Verificar que la clase pertenece al docente
        if ($clase->asignacion->id_docente !== $docente->id) {
            abort(403, 'No tienes acceso a esta clase.');
        }

        $clase->load([
            'asignacion.materiaGrupo.materia',
            'asignacion.materiaGrupo.grupo',
            'bloque',
            'aula.piso',
            'asistencias.admision.estudiante.persona',
        ]);

        $grupo = $clase->asignacion->materiaGrupo->grupo;

        // Estudiantes del grupo en estado cursando
        $admisiones = Admision::with('estudiante.persona')
            ->where('id_grupo', $grupo->id)
            ->where('estado', 'cursando')
            ->orderBy('id')
            ->get();

        // Asistencias ya registradas, indexadas por id_admision
        $asistenciasExistentes = $clase->asistencias
            ->keyBy('id_admision');

        // Verificar si se puede editar (< 24h desde que se marcó realizada)
        $editable = true;
        if ($clase->estado === 'realizada') {
            $editable = Carbon::parse($clase->updated_at)->diffInHours(now()) < 24;
        }
        if ($clase->estado === 'cancelada') {
            $editable = false;
        }

        return view('docente.asistencia.pase-lista', compact(
            'clase', 'admisiones', 'asistenciasExistentes', 'editable'
        ));
    }

    public function guardar(Request $request, ClaseProgramada $clase)
    {
        $docente = Auth::user()->docente;

        if ($clase->asignacion->id_docente !== $docente->id) {
            abort(403);
        }

        // Verificar editable
        if ($clase->estado === 'cancelada') {
            return back()->with('error', 'No se puede registrar asistencia en una clase cancelada.');
        }
        if ($clase->estado === 'realizada') {
            $horas = Carbon::parse($clase->updated_at)->diffInHours(now());
            if ($horas >= 24) {
                return back()->with('error', 'No se puede modificar la asistencia de una clase realizada hace más de 24 horas.');
            }
        }

        $request->validate([
            'asistencia'              => 'required|array',
            'asistencia.*.id_admision'=> 'required|exists:admision,id',
            'asistencia.*.estado'     => 'required|in:presente,ausente,justificado',
        ]);

        DB::transaction(function () use ($request, $clase) {
            foreach ($request->asistencia as $item) {
                Asistencia::updateOrCreate(
                    ['id_clase' => $clase->id, 'id_admision' => $item['id_admision']],
                    ['estado' => $item['estado'], 'fecha' => $clase->fecha, 'observacion' => $item['observacion'] ?? null]
                );
            }

            // Marcar clase como realizada si estaba programada
            if ($clase->estado === 'programada') {
                $clase->update(['estado' => 'realizada']);
            }
        });

        return redirect()->route('docente.clases.index')
            ->with('success', 'Asistencia registrada exitosamente.');
    }
}
