<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraGestionSeeder extends Seeder
{
    public function run(): void
    {
        $carreras = DB::table('carrera')->pluck('id', 'sigla');
        $gestiones = DB::table('gestion')->orderBy('id')->get();

        $cupos = [
            'SI'  => 200,
            'INF' => 150,
            'RC'  => 100,
            'ROB' => 80,
        ];

        $registros = [];
        foreach ($gestiones as $gestion) {
            foreach ($cupos as $sigla => $cupo) {
                $esCerrado = $gestion->estado === 'cerrado';
                $registros[] = [
                    'id_carrera'      => $carreras[$sigla],
                    'id_gestion'      => $gestion->id,
                    'cupo_maximo'     => $cupo,
                    'cupo_disponible' => $esCerrado ? 0 : $cupo,
                ];
            }
        }

        DB::table('carrera_gestion')->insert($registros);
    }
}
