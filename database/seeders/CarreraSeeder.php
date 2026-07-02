<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        $carreras = [
            ['nombre' => 'Ingeniería en Sistemas',                     'sigla' => 'SI',  'plan' => null],
            ['nombre' => 'Ingeniería Informática',                     'sigla' => 'INF', 'plan' => null],
            ['nombre' => 'Ingeniería en Redes y Telecomunicaciones',   'sigla' => 'RC',  'plan' => null],
            ['nombre' => 'Ingeniería en Robótica',                     'sigla' => 'ROB', 'plan' => null],
        ];

        DB::table('carrera')->insertOrIgnore($carreras);
    }
}
