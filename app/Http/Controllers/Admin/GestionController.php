<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GestionRequest;
use App\Models\Carrera;
use App\Models\CarreraGestion;
use App\Models\Gestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Gestion::orderBy('anio', 'desc')->orderBy('semestre', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $gestiones = $query->paginate(15)->withQueryString();

        return view('admin.gestiones.index', compact('gestiones'));
    }

    public function create()
    {
        return view('admin.gestiones.form', ['gestion' => null]);
    }

    public function store(GestionRequest $request)
    {
        DB::transaction(function () use ($request) {
            $gestion = Gestion::create($request->validated());

            // Auto-generar carrera_gestion para las 4 carreras
            $carreras = Carrera::all();
            foreach ($carreras as $carrera) {
                CarreraGestion::create([
                    'id_carrera'      => $carrera->id,
                    'id_gestion'      => $gestion->id,
                    'cupo_maximo'     => 100,
                    'cupo_disponible' => 100,
                ]);
            }
        });

        return redirect()->route('admin.gestiones.index')
            ->with('success', 'Gestión creada exitosamente.');
    }

    public function show(Gestion $gestion)
    {
        $carrerasGestion = CarreraGestion::where('id_gestion', $gestion->id)
            ->with('carrera')
            ->get();

        $stats = [
            'total_postulantes' => DB::table('admision')->where('id_gestion', $gestion->id)->count(),
            'cursando'          => DB::table('admision')->where('id_gestion', $gestion->id)->where('estado', 'cursando')->count(),
            'admitidos'         => DB::table('admision')->where('id_gestion', $gestion->id)->whereIn('estado', ['admitido_carrera1', 'admitido_carrera2'])->count(),
            'reprobados'        => DB::table('admision')->where('id_gestion', $gestion->id)->where('estado', 'reprobado')->count(),
            'grupos'            => DB::table('grupo')->where('id_gestion', $gestion->id)->count(),
        ];

        return view('admin.gestiones.show', compact('gestion', 'carrerasGestion', 'stats'));
    }

    public function edit(Gestion $gestion)
    {
        return view('admin.gestiones.form', compact('gestion'));
    }

    public function update(GestionRequest $request, Gestion $gestion)
    {
        $estadoAnterior = $gestion->estado;
        $gestion->update($request->validated());

        // Si la gestión se reactiva, reactivar las asignaciones de sus docentes
        if ($estadoAnterior !== 'activo' && $gestion->estado === 'activo') {
            $materiaGrupoIds = DB::table('materia_grupo as mg')
                ->join('grupo as g', 'g.id', '=', 'mg.id_grupo')
                ->where('g.id_gestion', $gestion->id)
                ->pluck('mg.id');

            DB::table('asignacion_academica')
                ->whereIn('id_materia_grupo', $materiaGrupoIds)
                ->update(['estado' => 'activo']);
        }

        return redirect()->route('admin.gestiones.index')
            ->with('success', 'Gestión actualizada exitosamente.');
    }

    public function actualizarCupo(Request $request, Gestion $gestion, CarreraGestion $carreraGestion)
    {
        $request->validate([
            'cupo_maximo' => 'required|integer|min:1|max:500',
        ]);

        // Contar admitidos reales a esa carrera en esa gestión
        $admitidos = DB::table('admision')
            ->where('id_gestion', $gestion->id)
            ->where(function ($q) use ($carreraGestion) {
                $q->where(function ($q2) use ($carreraGestion) {
                    $q2->where('id_carrera1', $carreraGestion->id_carrera)
                       ->where('estado', 'admitido_carrera1');
                })->orWhere(function ($q2) use ($carreraGestion) {
                    $q2->where('id_carrera2', $carreraGestion->id_carrera)
                       ->where('estado', 'admitido_carrera2');
                });
            })->count();

        if ($request->cupo_maximo < $admitidos) {
            throw ValidationException::withMessages([
                'cupo_maximo' => "No puede ser menor a los {$admitidos} estudiantes ya admitidos en esta carrera.",
            ]);
        }

        $carreraGestion->update([
            'cupo_maximo'     => $request->cupo_maximo,
            'cupo_disponible' => $request->cupo_maximo - $admitidos,
        ]);

        return redirect()->route('admin.gestiones.show', $gestion)
            ->with('success', 'Cupo actualizado correctamente.');
    }

    public function resetearCupos(Gestion $gestion)
    {
        // Resetea cupo_disponible = cupo_maximo para todas las carreras de esta gestión.
        // Útil para limpiar datos de prueba del proceso de admisión.
        DB::table('carrera_gestion')
            ->where('id_gestion', $gestion->id)
            ->update(['cupo_disponible' => DB::raw('cupo_maximo')]);

        return redirect()->route('admin.gestiones.show', $gestion)
            ->with('success', 'Cupos disponibles reseteados al máximo correctamente.');
    }

    public function cerrar(Gestion $gestion)
    {
        if ($gestion->estado !== 'activo') {
            return back()->with('error', 'Solo se puede cerrar una gestión activa.');
        }

        $gestion->update(['estado' => 'cerrado']);

        return redirect()->route('admin.gestiones.index')
            ->with('success', 'Gestión cerrada exitosamente.');
    }
}
