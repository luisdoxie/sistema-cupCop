<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Piso;
use Illuminate\Http\Request;

class AulaController extends Controller
{
    public function index(Request $request)
    {
        $query = Aula::with('piso')->orderBy('id_piso')->orderBy('numero');

        if ($request->filled('modalidad')) {
            $query->where('modalidad', $request->modalidad);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('id_piso')) {
            $query->where('id_piso', $request->id_piso);
        }

        $aulas = $query->paginate(15)->withQueryString();
        $pisos = Piso::orderBy('id')->get();

        return view('admin.aulas.index', compact('aulas', 'pisos'));
    }

    public function create()
    {
        $pisos = Piso::orderBy('id')->get();
        return view('admin.aulas.form', ['aula' => null, 'pisos' => $pisos]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_piso'   => 'required|exists:piso,id',
            'numero'    => 'required|string|max:10',
            'capacidad' => 'required|integer|min:1|max:500',
            'tipo'      => 'required|string|max:50',
            'modalidad' => 'required|in:presencial,virtual',
            'estado'    => 'required|in:disponible,ocupada,mantenimiento',
        ], [
            'id_piso.required'   => 'Seleccione un piso.',
            'numero.required'    => 'El número de aula es obligatorio.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'tipo.required'      => 'El tipo es obligatorio.',
            'modalidad.required' => 'La modalidad es obligatoria.',
            'estado.required'    => 'El estado es obligatorio.',
        ]);

        Aula::create($request->only(['id_piso', 'numero', 'capacidad', 'tipo', 'modalidad', 'estado']));

        return redirect()->route('admin.aulas.index')
            ->with('success', 'Aula creada exitosamente.');
    }

    public function edit(Aula $aula)
    {
        $pisos = Piso::orderBy('id')->get();
        return view('admin.aulas.form', compact('aula', 'pisos'));
    }

    public function update(Request $request, Aula $aula)
    {
        $request->validate([
            'id_piso'   => 'required|exists:piso,id',
            'numero'    => 'required|string|max:10',
            'capacidad' => 'required|integer|min:1|max:500',
            'tipo'      => 'required|string|max:50',
            'modalidad' => 'required|in:presencial,virtual',
            'estado'    => 'required|in:disponible,ocupada,mantenimiento',
        ]);

        $aula->update($request->only(['id_piso', 'numero', 'capacidad', 'tipo', 'modalidad', 'estado']));

        return redirect()->route('admin.aulas.index')
            ->with('success', 'Aula actualizada exitosamente.');
    }
}
