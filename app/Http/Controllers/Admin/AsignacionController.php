<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionAcademica;
use App\Models\BloqueHorario;
use App\Models\Docente;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\MateriaGrupo;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignacionController extends Controller
{
    public function index()
    {
        $asignaciones = AsignacionAcademica::with([
            'docente.persona',
            'materiaGrupo.materia',
            'materiaGrupo.grupo',
            'materiaGrupo.turno',
            'bloquesHorario',
        ])->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('admin.asignaciones.index', compact('asignaciones'));
    }

    public function create()
    {
        $gestionActiva = Gestion::where('estado', 'activo')->first();

        $docentes = Docente::with('persona')
            ->where('estado', 'activo')
            ->withCount([
                'asignaciones as grupos_asignados' => function ($q) {
                    $q->where('estado', 'activo');
                },
            ])
            ->get()
            ->map(function ($d) {
                $d->disponible = $d->grupos_asignados < $d->max_grupos;
                return $d;
            });

        $grupos = $gestionActiva
            ? Grupo::where('id_gestion', $gestionActiva->id)->where('estado', 'activo')->get()
            : collect();

        $materias = Materia::orderBy('nombre')->get();
        $turnos   = Turno::orderBy('nombre')->get();

        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

        return view('admin.asignaciones.form', compact('docentes', 'grupos', 'materias', 'turnos', 'dias', 'gestionActiva'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_docente'      => 'required|exists:docente,id',
            'id_grupo'        => 'required|exists:grupo,id',
            'id_materia'      => 'required|exists:materia,id',
            'id_turno'        => 'required|exists:turno,id',
            'carga_horaria'   => 'required|integer|min:1|max:40',
            'bloques'         => 'required|array|min:1',
            'bloques.*.dia'   => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado',
            'bloques.*.hora_inicio' => 'required|date_format:H:i',
            'bloques.*.hora_fin'    => 'required|date_format:H:i|after:bloques.*.hora_inicio',
        ], [
            'id_docente.required'  => 'Seleccione un docente.',
            'id_grupo.required'    => 'Seleccione un grupo.',
            'id_materia.required'  => 'Seleccione una materia.',
            'id_turno.required'    => 'Seleccione un turno.',
            'carga_horaria.required' => 'La carga horaria es obligatoria.',
            'bloques.required'     => 'Agregue al menos un bloque horario.',
            'bloques.*.dia.required'         => 'El día del bloque es obligatorio.',
            'bloques.*.hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'bloques.*.hora_fin.required'    => 'La hora de fin es obligatoria.',
            'bloques.*.hora_fin.after'       => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        DB::transaction(function () use ($request) {
            $materiaGrupo = MateriaGrupo::create([
                'id_grupo'   => $request->id_grupo,
                'id_materia' => $request->id_materia,
                'id_turno'   => $request->id_turno,
                'estado'     => 'activo',
            ]);

            $asignacion = AsignacionAcademica::create([
                'id_docente'       => $request->id_docente,
                'id_materia_grupo' => $materiaGrupo->id,
                'carga_horaria'    => $request->carga_horaria,
                'fecha_asignacion' => now()->toDateString(),
                'estado'           => 'activo',
                'observacion'      => $request->observacion,
            ]);

            foreach ($request->bloques as $bloque) {
                BloqueHorario::create([
                    'id_asignacion' => $asignacion->id,
                    'dia'           => $bloque['dia'],
                    'hora_inicio'   => $bloque['hora_inicio'],
                    'hora_fin'      => $bloque['hora_fin'],
                ]);
            }
        });

        return redirect()->route('admin.asignaciones.index')
            ->with('success', 'Asignación académica creada exitosamente.');
    }

    public function verificarHorario(Request $request)
    {
        $request->validate([
            'id_docente'  => 'required|exists:docente,id',
            'dia'         => 'required|string',
            'hora_inicio' => 'required',
            'hora_fin'    => 'required',
        ]);

        $conflicto = BloqueHorario::whereHas('asignacion', function ($q) use ($request) {
            $q->where('id_docente', $request->id_docente)
              ->where('estado', 'activo');
        })
        ->where('dia', $request->dia)
        ->where('hora_inicio', '<', $request->hora_fin)
        ->where('hora_fin', '>', $request->hora_inicio)
        ->with('asignacion.materiaGrupo.materia')
        ->first();

        if ($conflicto) {
            $materia = $conflicto->asignacion->materiaGrupo->materia->nombre ?? 'materia';
            return response()->json([
                'conflicto' => true,
                'detalle'   => "Conflicto con {$materia} el día {$request->dia} de {$conflicto->hora_inicio} a {$conflicto->hora_fin}.",
            ]);
        }

        return response()->json(['conflicto' => false, 'detalle' => '']);
    }
}
