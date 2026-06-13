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

                // Calcular promedio final
                $promedioRow = DB::selectOne(
                    'SELECT calcular_promedio_final(?) AS promedio',
                    [$admision->id]
                );
                $promedio = $promedioRow ? (float) $promedioRow->promedio : 0;
                $admision->update(['promedio_final' => $promedio]);

                // Procesar asignación a carrera
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
