<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocenteRequest;
use App\Models\AsignacionAcademica;
use App\Models\Docente;
use App\Models\Gestion;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DocenteController extends Controller
{
    public function index(Request $request)
    {
        $query = Docente::with('persona')
            ->withCount([
                'asignaciones as grupos_activos' => function ($q) {
                    $q->where('estado', 'activo');
                },
            ]);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('persona', function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('apellido', 'ilike', "%{$buscar}%")
                  ->orWhere('ci', 'ilike', "%{$buscar}%");
            });
        }

        $docentes = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('admin.docentes.index', compact('docentes'));
    }

    public function create()
    {
        return view('admin.docentes.form', ['docente' => null]);
    }

    public function store(DocenteRequest $request)
    {
        DB::transaction(function () use ($request) {
            $persona = Persona::create([
                'ci'        => $request->ci,
                'nombre'    => $request->nombre,
                'apellido'  => $request->apellido,
                'sexo'      => $request->sexo,
                'correo'    => $request->correo,
                'telefono'  => $request->telefono,
                'direccion' => $request->direccion,
                'password'  => Hash::make($request->ci . strtolower(substr($request->apellido, 0, 3)) . '!'),
                'rol'       => 'docente',
                'activo'    => true,
            ]);

            Docente::create([
                'id_persona'          => $persona->id,
                'especialidad'        => $request->especialidad,
                'grado_academico'     => $request->grado_academico,
                'diplomado_educacion' => true,
                'anios_experiencia'   => $request->anios_experiencia,
                'max_grupos'          => $request->max_grupos,
                'estado'              => $request->estado,
            ]);
        });

        return redirect()->route('admin.docentes.index')
            ->with('success', 'Docente registrado exitosamente.');
    }

    public function show(Docente $docente)
    {
        $docente->load('persona');

        $gestionActiva = Gestion::where('estado', 'activo')->first();

        $asignaciones = AsignacionAcademica::where('id_docente', $docente->id)
            ->with(['materiaGrupo.materia', 'materiaGrupo.grupo', 'materiaGrupo.turno', 'bloquesHorario'])
            ->get();

        return view('admin.docentes.show', compact('docente', 'asignaciones', 'gestionActiva'));
    }

    public function edit(Docente $docente)
    {
        $docente->load('persona');
        return view('admin.docentes.form', compact('docente'));
    }

    public function update(DocenteRequest $request, Docente $docente)
    {
        DB::transaction(function () use ($request, $docente) {
            $docente->persona->update([
                'nombre'    => $request->nombre,
                'apellido'  => $request->apellido,
                'sexo'      => $request->sexo,
                'correo'    => $request->correo,
                'telefono'  => $request->telefono,
                'direccion' => $request->direccion,
            ]);

            $docente->update([
                'especialidad'        => $request->especialidad,
                'grado_academico'     => $request->grado_academico,
                'diplomado_educacion' => true,
                'anios_experiencia'   => $request->anios_experiencia,
                'max_grupos'          => $request->max_grupos,
                'estado'              => $request->estado,
            ]);
        });

        return redirect()->route('admin.docentes.index')
            ->with('success', 'Docente actualizado exitosamente.');
    }
}
