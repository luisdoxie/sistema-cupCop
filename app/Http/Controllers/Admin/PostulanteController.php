<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Carrera;
use App\Models\Gestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostulanteController extends Controller
{
    public function index(Request $request)
    {
        $gestionActiva = Gestion::where('estado', 'activo')->first();

        $query = Admision::with(['estudiante.persona', 'carrera1', 'carrera2', 'gestion'])
            ->when($gestionActiva, fn($q) => $q->where('id_gestion', $gestionActiva->id));

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('estudiante.persona', function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('apellido', 'ilike', "%{$buscar}%")
                  ->orWhere('ci', 'ilike', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $admisiones = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $estados = [
            'inscrito', 'documentos_pendientes', 'pago_pendiente',
            'cursando', 'admitido_carrera1', 'admitido_carrera2',
            'reprobado', 'no_admitido',
        ];

        return view('admin.postulantes.index', compact('admisiones', 'estados', 'gestionActiva'));
    }

    public function edit(Admision $admision)
    {
        $admision->load(['estudiante.persona', 'carrera1', 'carrera2']);
        $carreras = Carrera::orderBy('nombre')->get();

        $estados = [
            'inscrito', 'documentos_pendientes', 'pago_pendiente',
            'cursando', 'admitido_carrera1', 'admitido_carrera2',
            'reprobado', 'no_admitido',
        ];

        return view('admin.postulantes.edit', compact('admision', 'carreras', 'estados'));
    }

    public function update(Request $request, Admision $admision)
    {
        $request->validate([
            'nombre'             => 'required|string|max:100',
            'apellido'           => 'required|string|max:100',
            'sexo'               => 'required|in:M,F',
            'correo'             => 'nullable|email|max:150',
            'telefono'           => 'nullable|string|max:20',
            'fecha_nacimiento'   => 'nullable|date',
            'colegio_procedencia'=> 'nullable|string|max:200',
            'ciudad'             => 'nullable|string|max:100',
            'titulo_bachiller'   => 'boolean',
            'id_carrera1'        => 'required|exists:carrera,id',
            'id_carrera2'        => 'required|exists:carrera,id|different:id_carrera1',
            'estado'             => 'required|string',
        ]);

        DB::transaction(function () use ($request, $admision) {
            $admision->load('estudiante.persona');

            $admision->estudiante->persona->update([
                'nombre'   => $request->nombre,
                'apellido' => $request->apellido,
                'sexo'     => $request->sexo,
                'correo'   => $request->correo,
                'telefono' => $request->telefono,
            ]);

            $admision->estudiante->update([
                'fecha_nacimiento'    => $request->fecha_nacimiento,
                'colegio_procedencia' => $request->colegio_procedencia,
                'ciudad'              => $request->ciudad,
                'titulo_bachiller'    => $request->boolean('titulo_bachiller'),
            ]);

            $admision->update([
                'id_carrera1' => $request->id_carrera1,
                'id_carrera2' => $request->id_carrera2,
                'estado'      => $request->estado,
            ]);
        });

        return redirect()->route('admin.postulantes.index')
            ->with('success', 'Datos del postulante actualizados correctamente.');
    }

    public function destroy(Admision $admision)
    {
        $admision->load('estudiante.persona');
        $nombre = $admision->estudiante->persona->nombre . ' ' . $admision->estudiante->persona->apellido;

        DB::transaction(function () use ($admision) {
            $estudiante = $admision->estudiante;
            $persona    = $estudiante->persona;

            $admision->documentos()->delete();
            $admision->notas()->delete();
            $admision->asistencias()->delete();
            $admision->pago()?->delete();
            $admision->delete();
            $estudiante->delete();
            $persona->delete();
        });

        return redirect()->route('admin.postulantes.index')
            ->with('success', "Postulante {$nombre} eliminado correctamente.");
    }
}
