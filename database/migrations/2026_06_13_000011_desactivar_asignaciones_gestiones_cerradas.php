<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') return;

        // Deshabilitar el trigger que bloquea updates cuando el docente
        // ya alcanzó max_grupos — el trigger no distingue si estamos
        // activando o desactivando, y lanzaría excepción al intentar
        // poner estado='inactivo'. Se rehabilita en el finally.
        DB::statement('ALTER TABLE asignacion_academica DISABLE TRIGGER USER');

        try {
            // Las asignaciones de gestiones cerradas deben ser inactivas.
            // Así el conteo de "grupos activos" refleja solo la gestión vigente.
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
        } finally {
            DB::statement('ALTER TABLE asignacion_academica ENABLE TRIGGER USER');
        }
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
