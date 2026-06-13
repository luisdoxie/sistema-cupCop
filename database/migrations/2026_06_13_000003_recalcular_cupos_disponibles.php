<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Recalcula cupo_disponible en base a las admisiones reales para todas las gestiones.
     * Formula segura: cupo_disponible = cupo_maximo - COUNT(admitidos a esa carrera en esa gestion)
     */
    public function up(): void
    {
        DB::statement("
            UPDATE carrera_gestion cg
            SET cupo_disponible = GREATEST(0, cg.cupo_maximo - (
                SELECT COUNT(*) FROM admision a
                WHERE (
                    (a.id_carrera1 = cg.id_carrera AND a.estado = 'admitido_carrera1')
                    OR
                    (a.id_carrera2 = cg.id_carrera AND a.estado = 'admitido_carrera2')
                )
                AND a.id_gestion = cg.id_gestion
            ))
        ");
    }

    public function down(): void {}
};
