<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GrupoRequest;
use App\Models\Admision;
use App\Models\Gestion;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public function index()
    {
        $gestionActiva = Gestion::where('estado', 'activo')->first();

        $grupos = collect();
        if ($gestionActiva) {
            $grupos = Grupo::where('id_gestion', $gestionActiva->id)
                ->withCount(['admisiones as total_estudiantes'])
                ->orderBy('nombre')
                ->paginate(15)
                ->withQueryString();
        }

        return view('admin.grupos.index', compact('gestionActiva', 'grupos'));
    }

    public function create()
    {
        $gestionActiva = Gestion::where('estado', 'activo')->first();

        if (!$gestionActiva) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No hay una gestión activa. Cree y active una gestión primero.');
        }

        return view('admin.grupos.form', ['grupo' => null, 'gestionActiva' => $gestionActiva]);
    }

    public function store(GrupoRequest $request)
    {
        $gestionActiva = Gestion::where('estado', 'activo')->first();

        if (!$gestionActiva) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No hay una gestión activa.');
        }

        $data = $request->validated();
        $data['id_gestion'] = $gestionActiva->id;
        $data['estado'] = 'activo';

        Grupo::create($data);

        return redirect()->route('admin.grupos.index')
            ->with('success', 'Grupo creado exitosamente.');
    }

    public function calcularNecesarios(Request $request)
    {
        $gestionActiva = Gestion::where('estado', 'activo')->first();

        if (!$gestionActiva) {
            return response()->json(['error' => 'No hay gestión activa'], 422);
        }

        $result = DB::selectOne("
            SELECT CEIL(COUNT(*)::numeric / (
                SELECT valor::numeric FROM config_sistema WHERE clave = 'divisor_grupos'
            )) AS grupos_necesarios
            FROM admision
            WHERE id_gestion = ?
            AND estado IN ('inscrito', 'documentos_pendientes', 'pago_pendiente')
        ", [$gestionActiva->id]);

        return response()->json([
            'grupos_necesarios' => $result ? (int) $result->grupos_necesarios : 0,
            'gestion'           => $gestionActiva->nombre,
        ]);
    }

    public function edit(Grupo $grupo)
    {
        $gestionActiva = Gestion::where('estado', 'activo')->first();
        return view('admin.grupos.form', ['grupo' => $grupo, 'gestionActiva' => $gestionActiva]);
    }

    public function update(GrupoRequest $request, Grupo $grupo)
    {
        $grupo->update($request->validated());

        return redirect()->route('admin.grupos.index')
            ->with('success', "Grupo '{$grupo->nombre}' actualizado correctamente.");
    }

    public function destroy(Grupo $grupo)
    {
        DB::transaction(function () use ($grupo) {
            // Desasignar estudiantes: vuelven a pago_pendiente sin grupo
            $grupo->admisiones()->whereNotNull('id_grupo')->update([
                'id_grupo' => null,
                'estado'   => 'pago_pendiente',
            ]);

            // Eliminar asistencias y clases programadas de las asignaciones del grupo
            foreach ($grupo->materiaGrupos as $mg) {
                foreach ($mg->asignaciones as $asig) {
                    DB::table('asistencia')
                        ->whereIn('id_clase', function ($q) use ($asig) {
                            $q->select('id')->from('clase_programada')->where('id_asignacion', $asig->id);
                        })->delete();
                    DB::table('clase_programada')->where('id_asignacion', $asig->id)->delete();
                    $asig->bloquesHorario()->delete();
                }
                DB::table('nota')
                    ->whereIn('id_examen', function ($q) use ($mg) {
                        $q->select('id')->from('examen')->where('id_materia_grupo', $mg->id);
                    })->delete();
                $mg->asignaciones()->delete();
                $mg->examenes()->delete();
            }
            $grupo->materiaGrupos()->delete();
            $grupo->delete();
        });

        return redirect()->route('admin.grupos.index')
            ->with('success', "Grupo eliminado. Los estudiantes asignados volvieron a estado pendiente.");
    }

    public function asignarPostulantes()
    {
        $gestionActiva = Gestion::where('estado', 'activo')->first();

        if (!$gestionActiva) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No hay una gestión activa.');
        }

        $grupos = Grupo::where('id_gestion', $gestionActiva->id)
            ->where('estado', 'activo')
            ->get();

        if ($grupos->isEmpty()) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No hay grupos activos en la gestión actual.');
        }

        $admisiones = Admision::where('id_gestion', $gestionActiva->id)
            ->where('estado', 'pago_pendiente')
            ->whereNull('id_grupo')
            ->orderBy('fecha')
            ->get();

        if ($admisiones->isEmpty()) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No hay postulantes con estado pago_pendiente sin grupo asignado.');
        }

        DB::transaction(function () use ($admisiones, $grupos) {
            $totalGrupos = $grupos->count();
            foreach ($admisiones as $index => $admision) {
                $grupo = $grupos[$index % $totalGrupos];
                $admision->update([
                    'id_grupo' => $grupo->id,
                    'estado'   => 'cursando',
                ]);
            }
        });

        return redirect()->route('admin.grupos.index')
            ->with('success', "Se asignaron {$admisiones->count()} postulantes a los grupos exitosamente.");
    }
}
