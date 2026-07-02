<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GestionSeeder extends Seeder
{
    public function run(): void
    {
        $gestiones = [
            [
                'nombre'      => 'Semestre 1-2025',
                'anio'        => 2025,
                'semestre'    => 1,
                'fecha_inicio' => '2025-02-01',
                'fecha_fin'   => '2025-06-30',
                'estado'      => 'cerrado',
            ],
            [
                'nombre'      => 'Semestre 2-2025',
                'anio'        => 2025,
                'semestre'    => 2,
                'fecha_inicio' => '2025-07-01',
                'fecha_fin'   => '2025-12-15',
                'estado'      => 'cerrado',
            ],
            [
                'nombre'      => 'Semestre 1-2026',
                'anio'        => 2026,
                'semestre'    => 1,
                'fecha_inicio' => '2026-02-01',
                'fecha_fin'   => '2026-06-30',
                'estado'      => 'activo',
            ],
        ];

        DB::table('gestion')->insertOrIgnore($gestiones);
    }
}
