<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ConfigSistemaSeeder::class,
            CarreraSeeder::class,
            MateriaSeeder::class,
            TurnoSeeder::class,
            PisoSeeder::class,
            GestionSeeder::class,
            CarreraGestionSeeder::class,
            AdminSeeder::class,
        ]);

        $sqlPath = database_path('sql/funciones.sql');
        if (file_exists($sqlPath)) {
            DB::unprepared(file_get_contents($sqlPath));
        }
    }
}
