<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('persona')->where('ci', '99999999')->exists()) return;

        $personaId = DB::table('persona')->insertGetId([
            'ci'        => '99999999',
            'nombre'    => 'Coordinador',
            'apellido'  => 'Demo',
            'sexo'      => 'M',
            'correo'    => 'coordinador@ficct.edu.bo',
            'password'  => Hash::make('Coord2025!'),
            'rol'       => 'coordinador',
            'activo'    => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('coordinador')->insert([
            'id_persona' => $personaId,
            'area'       => 'Ingeniería en Sistemas',
            'estado'     => 'activo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $persona = DB::table('persona')->where('ci', '99999999')->first();
        if ($persona) {
            DB::table('coordinador')->where('id_persona', $persona->id)->delete();
            DB::table('persona')->where('id', $persona->id)->delete();
        }
    }
};
