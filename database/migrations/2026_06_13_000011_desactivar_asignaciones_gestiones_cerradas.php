<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') return;

        // Las asignaciones de gestiones cerradas deben ser inactivas.
        // Así el conteo de "grupos activos" refleja solo la gestión vigente,
        // y no viola el límite de max_grupos del docente.
        DB::statement("
            UPDATE asignacion_academica aa
            SET estado = 'inactivo'
            FROM materia_grupo mg
            JOIN grupo gr  ON gr.id = mg.id_grupo
            JOIN gestion g ON g.id  = gr.id_gestion
            WHERE aa.id_materia_grupo = mg.id
              AND g.estado            = 'cerrado'
              AND aa.estado           = 'activo'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') return;

        DB::statement("
            UPDATE asignacion_academica aa
            SET estado = 'activo'
            FROM materia_grupo mg
            JOIN grupo gr  ON gr.id = mg.id_grupo
            JOIN gestion g ON g.id  = gr.id_gestion
            WHERE aa.id_materia_grupo = mg.id
              AND g.estado            = 'cerrado'
              AND aa.estado           = 'inactivo'
        ");
    }
};
