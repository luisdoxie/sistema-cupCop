<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Persona;
use App\Models\PostulacionDocente;
use Illuminate\Http\Request;

class PostulacionDocenteController extends Controller
{
    public function create()
    {
        $materias = Materia::orderBy('nombre')->get();
        return view('postulacion-docente.create', compact('materias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ci'                => 'required|string|max:20|unique:postulacion_docente,ci',
            'nombre'            => 'required|string|max:100',
            'apellido'          => 'required|string|max:100',
            'sexo'              => 'required|in:M,F',
            'correo'            => 'nullable|email|max:150',
            'telefono'          => 'nullable|string|max:20',
            'direccion'         => 'nullable|string|max:200',
            'grado_academico'   => 'nullable|string|max:150',
            'anios_experiencia' => 'required|integer|min:4',
            'materias'          => 'required|array|min:1',
            'materias.*'        => 'exists:materia,id',
            'cv'                => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ], [
            'ci.unique'             => 'Ya existe una postulación registrada con este CI.',
            'materias.required'     => 'Selecciona al menos una materia que puedes impartir.',
            'anios_experiencia.min' => 'Se requieren mínimo 4 años de experiencia docente.',
            'cv.mimes'              => 'El CV debe ser un archivo PDF, DOC o DOCX.',
            'cv.max'                => 'El CV no debe superar los 5 MB.',
        ]);

        if (Persona::where('ci', $request->ci)->exists()) {
            return back()->withInput()
                ->withErrors(['ci' => 'Este CI ya está registrado en el sistema como usuario activo.']);
        }

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('postulaciones-docente/cv', 'local');
        }

        $postulacion = PostulacionDocente::create([
            'ci'                  => $request->ci,
            'nombre'              => $request->nombre,
            'apellido'            => $request->apellido,
            'sexo'                => $request->sexo,
            'correo'              => $request->correo,
            'telefono'            => $request->telefono,
            'direccion'           => $request->direccion,
            'grado_academico'     => $request->grado_academico,
            'anios_experiencia'   => $request->anios_experiencia,
            'diplomado_educacion' => $request->boolean('diplomado_educacion'),
            'cv_path'             => $cvPath,
        ]);

        $postulacion->materias()->attach($request->materias);

        return redirect()->route('postulacion-docente.confirmacion')
            ->with('postulacion_nombre', $request->nombre . ' ' . $request->apellido);
    }

    public function confirmacion()
    {
        return view('postulacion-docente.confirmacion');
    }
}
