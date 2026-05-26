<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Grupo;
use App\Models\MateriaGrupo;
use Illuminate\Http\Request;

class ExamenController extends Controller
{
    public function index()
    {
        $grupos = Grupo::with(['gestion', 'materiaGrupos.materia', 'materiaGrupos.examenes'])
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.examenes.index', compact('grupos'));
    }

    public function porGrupo(Grupo $grupo)
    {
        $grupo->load([
            'gestion',
            'materiaGrupos.materia',
            'materiaGrupos.examenes',
        ]);

        return view('admin.examenes.por-grupo', compact('grupo'));
    }

    public function activarGrupo(Grupo $grupo)
    {
        $grupo->update(['estado' => 'activo']);

        $materiaGrupos = MateriaGrupo::where('id_grupo', $grupo->id)->get();

        $tipos = [
            'parcial1' => 30,
            'parcial2' => 30,
            'final'    => 40,
        ];

        foreach ($materiaGrupos as $mg) {
            foreach ($tipos as $tipo => $puntaje) {
                Examen::updateOrCreate(
                    ['id_materia_grupo' => $mg->id, 'tipo' => $tipo],
                    ['puntaje_maximo' => $puntaje, 'estado' => 'programado']
                );
            }
        }

        return redirect()->route('admin.examenes.porGrupo', $grupo)
            ->with('success', "Grupo activado y {$materiaGrupos->count()} materias con exámenes creados.");
    }

    public function cambiarEstado(Request $request, Examen $examen)
    {
        $request->validate([
            'estado' => ['required', 'in:realizado,anulado'],
        ]);

        $examen->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado del examen actualizado.');
    }
}
