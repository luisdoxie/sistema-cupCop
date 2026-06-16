<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\DocenteMateriaHabilitada;
use App\Models\Materia;
use App\Models\Persona;
use App\Models\PostulacionDocente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PostulacionDocenteController extends Controller
{
    public function index(Request $request)
    {
        $query = PostulacionDocente::withCount('materias')->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $postulaciones = $query->paginate(15)->withQueryString();
        $pendientes    = PostulacionDocente::where('estado', 'pendiente')->count();

        return view('admin.postulaciones-docente.index', compact('postulaciones', 'pendientes'));
    }

    public function show(PostulacionDocente $postulacion)
    {
        $postulacion->load('materias', 'aprobadoPor');

        return view('admin.postulaciones-docente.show', compact('postulacion'));
    }

    public function aprobar(Request $request, PostulacionDocente $postulacion)
    {
        if ($postulacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta postulación ya fue procesada.');
        }

        $request->validate([
            'materias_aprobadas'   => 'required|array|min:1',
            'materias_aprobadas.*' => 'exists:materia,id',
            'max_grupos'           => 'nullable|integer|min:1|max:5',
        ], [
            'materias_aprobadas.required' => 'Selecciona al menos una materia para aprobar.',
        ]);

        if (Persona::where('ci', $postulacion->ci)->exists()) {
            return back()->with('error', 'Ya existe una cuenta registrada con el CI de este postulante.');
        }

        DB::transaction(function () use ($request, $postulacion) {
            $persona = Persona::create([
                'ci'        => $postulacion->ci,
                'nombre'    => $postulacion->nombre,
                'apellido'  => $postulacion->apellido,
                'sexo'      => $postulacion->sexo,
                'correo'    => $postulacion->correo,
                'telefono'  => $postulacion->telefono,
                'direccion' => $postulacion->direccion,
                'password'  => Hash::make($postulacion->ci . strtolower(substr($postulacion->apellido, 0, 3)) . '!'),
                'rol'       => 'docente',
                'activo'    => true,
            ]);

            $especialidad = Materia::whereIn('id', $request->materias_aprobadas)
                ->orderBy('nombre')
                ->pluck('nombre')
                ->join(', ');

            $docente = Docente::create([
                'id_persona'          => $persona->id,
                'especialidad'        => $especialidad,
                'grado_academico'     => $postulacion->grado_academico,
                'diplomado_educacion' => $postulacion->diplomado_educacion,
                'anios_experiencia'   => max(4, $postulacion->anios_experiencia),
                'max_grupos'          => $request->max_grupos ?? 4,
                'estado'              => 'activo',
            ]);

            foreach ($request->materias_aprobadas as $materiaId) {
                DocenteMateriaHabilitada::create([
                    'id_docente'   => $docente->id,
                    'id_materia'   => $materiaId,
                    'aprobado_por' => auth()->id(),
                ]);
            }

            $postulacion->update([
                'estado'       => 'aprobada',
                'observacion'  => $request->observacion,
                'aprobado_por' => auth()->id(),
                'aprobado_en'  => now(),
            ]);
        });

        return redirect()->route('admin.postulaciones-docente.index')
            ->with('success', "Postulación aprobada. Se creó la cuenta del docente {$postulacion->nombre} {$postulacion->apellido}.");
    }

    public function rechazar(Request $request, PostulacionDocente $postulacion)
    {
        if ($postulacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta postulación ya fue procesada.');
        }

        $request->validate([
            'observacion' => 'required|string|min:10',
        ], [
            'observacion.required' => 'Indica el motivo del rechazo.',
            'observacion.min'      => 'El motivo debe tener al menos 10 caracteres.',
        ]);

        $postulacion->update([
            'estado'       => 'rechazada',
            'observacion'  => $request->observacion,
            'aprobado_por' => auth()->id(),
            'aprobado_en'  => now(),
        ]);

        return redirect()->route('admin.postulaciones-docente.index')
            ->with('success', 'Postulación rechazada correctamente.');
    }
}
