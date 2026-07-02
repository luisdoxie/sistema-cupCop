<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('persona')->where('correo', 'admin@sistema-cup.edu.bo')->exists()) {
            return;
        }

        $personaId = DB::table('persona')->insertGetId([
            'ci'       => '12345678',
            'nombre'   => 'Administrador',
            'apellido' => 'Sistema',
            'sexo'     => 'M',
            'telefono' => null,
            'direccion' => null,
            'correo'   => 'admin@sistema-cup.edu.bo',
            'password' => Hash::make('Admin2025!'),
            'rol'      => 'administrador',
            'activo'   => true,
        ]);

        DB::table('administrador')->insert([
            'id_persona' => $personaId,
            'cargo'      => 'Administrador General',
            'estado'     => 'activo',
        ]);
    }
}
