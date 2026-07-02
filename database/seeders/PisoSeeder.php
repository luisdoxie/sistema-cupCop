<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PisoSeeder extends Seeder
{
    public function run(): void
    {
        $pisos = [
            ['numero' => 1],
            ['numero' => 2],
            ['numero' => 3],
            ['numero' => 4],
        ];

        DB::table('piso')->insertOrIgnore($pisos);
    }
}
