<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inscripcion\Paso1Request;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Paso1Controller extends Controller
{
    public function create()
    {
        // Si ya está autenticado como estudiante, redirigir al paso 2
        if (Auth::check() && Auth::user()->rol === 'estudiante') {
            return redirect()->route('inscripcion.paso2.create');
        }

        return view('inscripcion.paso1');
    }

    public function store(Paso1Request $request)
    {
        DB::beginTransaction();

        try {
            // Crear persona con rol estudiante
            $persona = User::create([
                'ci'       => $request->ci,
                'nombre'   => $request->nombre,
                'apellido' => $request->apellido,
                'sexo'     => $request->sexo,
                'telefono' => $request->telefono,
                'direccion'=> $request->direccion,
                'correo'   => $request->correo,
                'password' => $request->password,
                'rol'      => 'estudiante',
                'activo'   => true,
            ]);

            // Crear registro estudiante
            Estudiante::create([
                'id_persona'          => $persona->id,
                'fecha_nacimiento'    => $request->fecha_nacimiento,
                'colegio_procedencia' => $request->colegio_procedencia,
                'ciudad'              => $request->ciudad,
                'titulo_bachiller'    => $request->boolean('titulo_bachiller'),
                'estado'              => 'activo',
            ]);

            DB::commit();

            // Autenticar al usuario recién creado
            Auth::login($persona);

            return redirect()->route('inscripcion.paso2.create')
                ->with('success', '¡Registro exitoso! Ahora seleccione sus carreras.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Ocurrió un error al registrar. Intente nuevamente.');
        }
    }
}
