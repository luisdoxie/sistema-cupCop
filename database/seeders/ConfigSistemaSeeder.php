<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSistemaSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            ['clave' => 'cupo_maximo_grupo',  'valor' => '70',  'descripcion' => 'Maximo de alumnos por grupo (doc txt=70, PDF=80). Ajustar segun indicacion del docente'],
            ['clave' => 'divisor_grupos',      'valor' => '70',  'descripcion' => 'Divisor para CEIL(inscritos/divisor). Cambiar a 80 si lo indica la ingeniera'],
            ['clave' => 'monto_inscripcion',   'valor' => '150', 'descripcion' => 'Monto en bolivianos para el pago de inscripcion'],
            ['clave' => 'gestiones_minimas',   'valor' => '3',   'descripcion' => 'Minimo de gestiones requeridas en la BD para reportes comparativos'],
        ];

        DB::table('config_sistema')->upsert(
            $configs,
            ['clave'],
            ['valor', 'descripcion']
        );
    }
}
