<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        $turnos = [
            ['nombre' => 'Manana', 'descripcion' => '07:00-12:00', 'estado' => 'activo'],
            ['nombre' => 'Tarde',  'descripcion' => '14:00-18:00', 'estado' => 'activo'],
            ['nombre' => 'Noche',  'descripcion' => '19:00-22:00', 'estado' => 'activo'],
        ];

        DB::table('turno')->insertOrIgnore($turnos);
    }
}
