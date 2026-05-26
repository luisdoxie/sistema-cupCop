<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\AsignacionAcademica;
use App\Models\Aula;
use App\Models\BloqueHorario;
use App\Models\ClaseProgramada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClaseController extends Controller
{
    public function index(Request $request)
    {
        $docente = Auth::user()->docente;

        $semana = $request->input('semana')
            ? Carbon::parse($request->input('semana') . '-1') // lunes de la semana ISO
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $inicio = $semana->copy()->startOfWeek(Carbon::MONDAY);
        $fin    = $semana->copy()->endOfWeek(Carbon::SATURDAY);

        $asignacionIds = AsignacionAcademica::where('id_docente', $docente->id)
            ->where('estado', 'activo')
            ->pluck('id');

        $clases = ClaseProgramada::with([
                'asignacion.materiaGrupo.materia',
                'asignacion.materiaGrupo.grupo',
                'aula.piso',
                'bloque',
            ])
            ->whereIn('id_asignacion', $asignacionIds)
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->orderBy('fecha')
            ->orderBy('id_bloque')
            ->get()
            ->groupBy('fecha');

        // Días de la semana
        $dias = [];
        for ($i = 0; $i < 6; $i++) {
            $dias[] = $inicio->copy()->addDays($i);
        }

        return view('docente.clases.index', compact('clases', 'dias', 'inicio', 'fin', 'semana'));
    }

    public function create()
    {
        $docente = Auth::user()->docente;

        $asignaciones = AsignacionAcademica::with([
                'materiaGrupo.materia',
                'materiaGrupo.grupo',
                'bloquesHorario',
            ])
            ->where('id_docente', $docente->id)
            ->where('estado', 'activo')
            ->get();

        $aulas = Aula::with('piso')
            ->where('estado', 'disponible')
            ->orderBy('numero')
            ->get();

        return view('docente.clases.create', compact('asignaciones', 'aulas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_asignacion' => 'required|exists:asignacion_academica,id',
            'id_aula'       => 'required|exists:aula,id',
            'id_bloque'     => 'required|exists:bloque_horario,id',
            'fecha'         => 'required|date',
        ], [
            'id_asignacion.required' => 'Seleccione una asignación.',
            'id_aula.required'       => 'Seleccione un aula.',
            'id_bloque.required'     => 'Seleccione un bloque horario.',
            'fecha.required'         => 'La fecha es obligatoria.',
        ]);

        $docente = Auth::user()->docente;

        // Verificar que la asignación pertenece al docente
        $asignacion = AsignacionAcademica::where('id', $request->id_asignacion)
            ->where('id_docente', $docente->id)
            ->firstOrFail();

        // Verificar que el bloque pertenece a la asignación
        $bloque = BloqueHorario::where('id', $request->id_bloque)
            ->where('id_asignacion', $asignacion->id)
            ->firstOrFail();

        // Verificar que el aula no esté reservada en ese bloque/fecha
        $conflicto = ClaseProgramada::where('id_aula', $request->id_aula)
            ->where('fecha', $request->fecha)
            ->whereHas('bloque', function ($q) use ($bloque) {
                $q->where('hora_inicio', '<', $bloque->hora_fin)
                  ->where('hora_fin', '>', $bloque->hora_inicio);
            })
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($conflicto) {
            return back()->withErrors(['id_aula' => 'El aula ya está reservada en ese horario y fecha.'])->withInput();
        }

        // Verificar que no exista ya esa clase programada
        $yaExiste = ClaseProgramada::where('id_asignacion', $request->id_asignacion)
            ->where('id_bloque', $request->id_bloque)
            ->where('fecha', $request->fecha)
            ->exists();

        if ($yaExiste) {
            return back()->withErrors(['fecha' => 'Ya existe una clase programada para esa asignación, bloque y fecha.'])->withInput();
        }

        ClaseProgramada::create([
            'id_asignacion' => $request->id_asignacion,
            'id_aula'       => $request->id_aula,
            'id_bloque'     => $request->id_bloque,
            'fecha'         => $request->fecha,
            'estado'        => 'programada',
        ]);

        return redirect()->route('docente.clases.index')
            ->with('success', 'Clase programada exitosamente.');
    }

    public function cambiarEstado(Request $request, ClaseProgramada $clase)
    {
        $docente = Auth::user()->docente;

        // Verificar que la clase pertenece al docente
        if ($clase->asignacion->id_docente !== $docente->id) {
            abort(403);
        }

        $request->validate([
            'estado' => 'required|in:realizada,cancelada,programada',
        ]);

        $clase->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado de clase actualizado.');
    }

    public function verificarAula(Request $request)
    {
        $request->validate([
            'id_aula'   => 'required',
            'id_bloque' => 'required',
            'fecha'     => 'required|date',
        ]);

        $bloque = BloqueHorario::find($request->id_bloque);
        if (!$bloque) {
            return response()->json(['disponible' => false, 'mensaje' => 'Bloque no encontrado.']);
        }

        $conflicto = ClaseProgramada::where('id_aula', $request->id_aula)
            ->where('fecha', $request->fecha)
            ->whereHas('bloque', function ($q) use ($bloque) {
                $q->where('hora_inicio', '<', $bloque->hora_fin)
                  ->where('hora_fin', '>', $bloque->hora_inicio);
            })
            ->where('estado', '!=', 'cancelada')
            ->exists();

        return response()->json([
            'disponible' => !$conflicto,
            'mensaje'    => $conflicto ? 'Aula no disponible en ese horario.' : 'Aula disponible.',
        ]);
    }
}
