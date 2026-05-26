<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcesarImportacion;
use App\Models\Gestion;
use App\Models\ImportacionLote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CargaMasivaController extends Controller
{
    protected array $plantillas = [
        'docente'      => ['ci', 'nombre', 'apellido', 'sexo', 'correo', 'telefono', 'especialidad', 'grado_academico', 'anios_experiencia'],
        'estudiante'   => ['ci', 'nombre', 'apellido', 'sexo', 'correo', 'telefono', 'fecha_nacimiento', 'colegio_procedencia', 'ciudad'],
        'coordinador'  => ['ci', 'nombre', 'apellido', 'sexo', 'correo', 'telefono'],
    ];

    public function index()
    {
        $lotes = ImportacionLote::where('id_admin', Auth::id())
            ->orderBy('fecha_subida', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.carga-masiva.index', compact('lotes'));
    }

    public function descargarPlantilla(string $tipo)
    {
        if (!array_key_exists($tipo, $this->plantillas)) {
            abort(404);
        }

        $headers = $this->plantillas[$tipo];
        $csv = implode(',', $headers) . "\n";
        // Fila de ejemplo
        $ejemplo = array_fill(0, count($headers), 'ejemplo');
        $csv .= implode(',', $ejemplo) . "\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"plantilla_{$tipo}.csv\"",
        ]);
    }

    public function subir(Request $request)
    {
        $request->validate([
            'tipo_usuario' => 'required|in:docente,estudiante,coordinador',
            'archivo'      => 'required|file|mimes:xlsx,csv,txt|max:5120',
        ], [
            'tipo_usuario.required' => 'Seleccione el tipo de usuario.',
            'tipo_usuario.in'       => 'Tipo de usuario no válido.',
            'archivo.required'      => 'Seleccione un archivo.',
            'archivo.mimes'         => 'El archivo debe ser XLSX o CSV.',
            'archivo.max'           => 'El archivo no debe superar 5MB.',
        ]);

        $archivo     = $request->file('archivo');
        $nombreOrig  = $archivo->getClientOriginalName();
        $ruta        = $archivo->store('importaciones', 'local');

        $gestionActiva = Gestion::where('estado', 'activo')->first();

        $lote = ImportacionLote::create([
            'id_admin'        => Auth::id(),
            'id_gestion'      => $gestionActiva?->id,
            'tipo_usuario'    => $request->tipo_usuario,
            'nombre_archivo'  => $nombreOrig,
            'ruta_archivo'    => $ruta,
            'total_registros' => 0,
            'exitosos'        => 0,
            'fallidos'        => 0,
            'errores'         => null,
            'estado'          => 'pendiente',
            'fecha_subida'    => now(),
        ]);

        ProcesarImportacion::dispatch($lote);

        return redirect()->route('admin.carga-masiva.resultado', $lote->id)
            ->with('success', 'Archivo subido. El procesamiento está en curso.');
    }

    public function verResultado(ImportacionLote $lote)
    {
        $errores = $lote->errores ? json_decode($lote->errores, true) : [];

        return view('admin.carga-masiva.resultado', compact('lote', 'errores'));
    }
}
