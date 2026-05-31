<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $actualizaciones = [
            'SI'  => 'Ingeniería en Sistemas',
            'INF' => 'Ingeniería Informática',
            'RC'  => 'Ingeniería en Redes y Telecomunicaciones',
            'ROB' => 'Ingeniería en Robótica',
        ];

        foreach ($actualizaciones as $sigla => $nombre) {
            DB::table('carrera')
                ->where('sigla', $sigla)
                ->update(['nombre' => $nombre]);
        }
    }

    public function down(): void
    {
        $originales = [
            'SI'  => 'Sistemas Informaticos',
            'INF' => 'Informatica',
            'RC'  => 'Redes de Computadoras',
            'ROB' => 'Robotica',
        ];

        foreach ($originales as $sigla => $nombre) {
            DB::table('carrera')
                ->where('sigla', $sigla)
                ->update(['nombre' => $nombre]);
        }
    }
};
