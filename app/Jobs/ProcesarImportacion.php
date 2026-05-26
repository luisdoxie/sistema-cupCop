<?php

namespace App\Jobs;

use App\Models\Docente;
use App\Models\ImportacionLote;
use App\Models\Persona;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcesarImportacion implements ShouldQueue
{
    use Queueable;

    public function __construct(public ImportacionLote $lote)
    {
    }

    public function handle(): void
    {
        $this->lote->update(['estado' => 'procesando']);

        $errores   = [];
        $exitosos  = 0;
        $fallidos  = 0;
        $total     = 0;

        try {
            $rutaAbsoluta = Storage::disk('local')->path($this->lote->ruta_archivo);
            $extension    = strtolower(pathinfo($rutaAbsoluta, PATHINFO_EXTENSION));

            if (in_array($extension, ['csv', 'txt'])) {
                $filas = $this->leerCsv($rutaAbsoluta);
            } else {
                $filas = $this->leerExcel($rutaAbsoluta);
            }

            foreach ($filas as $index => $fila) {
                $total++;
                $numFila = $index + 2;

                try {
                    $this->procesarFila($fila, $this->lote->tipo_usuario);
                    $exitosos++;
                } catch (\Throwable $e) {
                    $fallidos++;
                    $errores[] = [
                        'fila'  => $numFila,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        } catch (\Throwable $e) {
            $fallidos++;
            $errores[] = [
                'fila'  => 0,
                'error' => 'Error al leer el archivo: ' . $e->getMessage(),
            ];
            Log::error('ProcesarImportacion error: ' . $e->getMessage());
        }

        $estado = $fallidos > 0 ? 'con_errores' : 'completado';

        $this->lote->update([
            'total_registros' => $total,
            'exitosos'        => $exitosos,
            'fallidos'        => $fallidos,
            'errores'         => !empty($errores) ? json_encode($errores) : null,
            'estado'          => $estado,
            'fecha_proceso'   => now(),
        ]);
    }

    protected function leerCsv(string $ruta): array
    {
        $filas    = [];
        $cabecera = null;

        if (($handle = fopen($ruta, 'r')) !== false) {
            while (($datos = fgetcsv($handle, 1000, ',')) !== false) {
                if ($cabecera === null) {
                    $cabecera = array_map('trim', $datos);
                    continue;
                }
                if (count($cabecera) !== count($datos)) {
                    continue;
                }
                $filas[] = array_combine($cabecera, array_map('trim', $datos));
            }
            fclose($handle);
        }

        return $filas;
    }

    protected function leerExcel(string $ruta): array
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($ruta);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);

            if (empty($rows)) {
                return [];
            }

            $cabecera = array_map('trim', $rows[0]);
            $filas    = [];

            for ($i = 1; $i < count($rows); $i++) {
                $fila = $rows[$i];
                if (count($cabecera) !== count($fila)) {
                    continue;
                }
                $combined = array_combine($cabecera, array_map(fn($v) => trim((string)($v ?? '')), $fila));
                if (!empty(array_filter($combined))) {
                    $filas[] = $combined;
                }
            }

            return $filas;
        } catch (\Throwable $e) {
            return $this->leerCsv($ruta);
        }
    }

    protected function procesarFila(array $fila, string $tipo): void
    {
        $ci       = trim($fila['ci'] ?? '');
        $nombre   = trim($fila['nombre'] ?? '');
        $apellido = trim($fila['apellido'] ?? '');

        if (empty($ci) || empty($nombre) || empty($apellido)) {
            throw new \InvalidArgumentException('CI, nombre y apellido son obligatorios.');
        }

        if (Persona::where('ci', $ci)->exists()) {
            throw new \InvalidArgumentException("Ya existe una persona con CI {$ci}.");
        }

        $password = Hash::make($ci . strtolower(substr($apellido, 0, 3)) . '!');

        DB::transaction(function () use ($fila, $tipo, $ci, $nombre, $apellido, $password) {
            $persona = Persona::create([
                'ci'        => $ci,
                'nombre'    => $nombre,
                'apellido'  => $apellido,
                'sexo'      => $fila['sexo'] ?? 'M',
                'correo'    => $fila['correo'] ?? null,
                'telefono'  => $fila['telefono'] ?? null,
                'password'  => $password,
                'rol'       => $tipo,
                'activo'    => true,
            ]);

            if ($tipo === 'docente') {
                Docente::create([
                    'id_persona'          => $persona->id,
                    'especialidad'        => $fila['especialidad'] ?? 'General',
                    'grado_academico'     => $fila['grado_academico'] ?? 'Licenciado',
                    'diplomado_educacion' => true,
                    'anios_experiencia'   => max(4, (int) ($fila['anios_experiencia'] ?? 4)),
                    'max_grupos'          => 3,
                    'estado'              => 'activo',
                ]);
            } elseif ($tipo === 'estudiante') {
                \App\Models\Estudiante::create([
                    'id_persona'          => $persona->id,
                    'fecha_nacimiento'    => $fila['fecha_nacimiento'] ?? null,
                    'colegio_procedencia' => $fila['colegio_procedencia'] ?? null,
                    'ciudad'              => $fila['ciudad'] ?? null,
                ]);
            }
            // coordinador: solo se crea la persona con rol=coordinador
        });
    }
}
