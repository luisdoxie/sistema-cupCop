<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        $materias = [
            ['nombre' => 'Computacion', 'sigla' => 'COMP'],
            ['nombre' => 'Matematicas', 'sigla' => 'MAT'],
            ['nombre' => 'Ingles',      'sigla' => 'ING'],
            ['nombre' => 'Fisica',      'sigla' => 'FIS'],
        ];

        DB::table('materia')->insert($materias);
    }
}
