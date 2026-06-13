<?php

namespace App\Services;

use App\Events\VeredictoAdmisionEvent;
use App\Models\Admision;
use App\Models\Gestion;
use Illuminate\Support\Facades\DB;

class AdmisionFinalService
{
    public function procesarGestion(int $idGestion): array
    {
        $resultado = [
            'total'         => 0,
            'admitidos_c1'  => 0,
            'admitidos_c2'  => 0,
            'reprobados'    => 0,
            'no_admitido'   => 0,
        ];

        DB::transaction(function () use ($idGestion, &$resultado) {
            $admisiones = Admision::where('id_gestion', $idGestion)
                ->where('estado', 'cursando')
                ->get();

            $resultado['total'] = $admisiones->count();

            // Paso 1: verificar aprobación y calcular promedio de todos
            $aprobados = collect();

            foreach ($admisiones as $admision) {
                $aprobado = DB::selectOne(
                    'SELECT verificar_aprobacion(?) AS resultado',
                    [$admision->id]
                );

                if (!$aprobado || !$aprobado->resultado) {
                    $admision->update(['estado' => 'reprobado']);
                    $resultado['reprobados']++;
                    continue;
                }

                $promedioRow = DB::selectOne(
                    'SELECT calcular_promedio_final(?) AS promedio',
                    [$admision->id]
                );
                $promedio = $promedioRow ? (float) $promedioRow->promedio : 0;
                $admision->update(['promedio_final' => $promedio]);
                $admision->promedio_final = $promedio;

                $aprobados->push($admision);
            }

            // Paso 2: ordenar por promedio de mayor a menor (mérito)
            $aprobados = $aprobados->sortByDesc('promedio_final');

            // Paso 3: asignar cupos en orden de mérito
            foreach ($aprobados as $admision) {
                $procesado = DB::selectOne(
                    'SELECT procesar_admision_carrera(?) AS resultado',
                    [$admision->id]
                );

                $estadoResultante = $procesado ? $procesado->resultado : 'no_admitido';

                switch ($estadoResultante) {
                    case 'admitido_carrera1':
                        $resultado['admitidos_c1']++;
                        break;
                    case 'admitido_carrera2':
                        $resultado['admitidos_c2']++;
                        break;
                    default:
                        $resultado['no_admitido']++;
                        break;
                }

                event(new VeredictoAdmisionEvent($admision->fresh()));
            }
        });

        return $resultado;
    }
}
