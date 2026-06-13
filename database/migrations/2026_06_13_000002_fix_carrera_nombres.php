<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $nombres = [
            'SI'  => 'Ingeniería en Sistemas',
            'INF' => 'Ingeniería Informática',
            'RC'  => 'Ingeniería en Redes y Telecomunicaciones',
            'ROB' => 'Ingeniería en Robótica',
        ];

        foreach ($nombres as $sigla => $nombre) {
            DB::table('carrera')->where('sigla', $sigla)->update(['nombre' => $nombre]);
        }
    }

    public function down(): void {}
};
