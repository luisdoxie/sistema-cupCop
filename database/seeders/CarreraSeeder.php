<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        $carreras = [
            ['nombre' => 'Sistemas Informaticos',   'sigla' => 'SI',  'plan' => null],
            ['nombre' => 'Informatica',              'sigla' => 'INF', 'plan' => null],
            ['nombre' => 'Redes de Computadoras',   'sigla' => 'RC',  'plan' => null],
            ['nombre' => 'Robotica',                 'sigla' => 'ROB', 'plan' => null],
        ];

        DB::table('carrera')->insert($carreras);
    }
}
