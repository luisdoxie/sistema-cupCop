<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSistemaSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            ['clave' => 'cupo_maximo_grupo',  'valor' => '70',  'descripcion' => 'Cupo máximo por grupo'],
            ['clave' => 'divisor_grupos',      'valor' => '70',  'descripcion' => 'Divisor para calcular cantidad de grupos'],
            ['clave' => 'monto_inscripcion',   'valor' => '150', 'descripcion' => 'Monto de inscripción en bolivianos'],
            ['clave' => 'gestiones_minimas',   'valor' => '3',   'descripcion' => 'Número mínimo de gestiones requeridas'],
        ];

        DB::table('config_sistema')->insert($configs);
    }
}
