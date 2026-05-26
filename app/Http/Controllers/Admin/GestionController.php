<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GestionRequest;
use App\Models\Carrera;
use App\Models\CarreraGestion;
use App\Models\Gestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function edit(Gestion $gestion)
    {
        return view('admin.gestiones.form', compact('gestion'));
    }

    public function update(GestionRequest $request, Gestion $gestion)
    {
        $gestion->update($request->validated());

        return redirect()->route('admin.gestiones.index')
            ->with('success', 'Gestión actualizada exitosamente.');
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
