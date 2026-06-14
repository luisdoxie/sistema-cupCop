<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') return;

        // Corrige el SUM(cupo_maximo) inflado por el cross-product
        // admision × materia_grupo que existía en la vista original.
        DB::unprepared("
CREATE OR REPLACE VIEW vw_grupos_habilitados AS
WITH caps AS (
    SELECT id_gestion,
           COUNT(*)                      AS total_grupos,
           COALESCE(SUM(cupo_maximo), 0) AS capacidad_total
    FROM grupo
    WHERE estado = 'activo'
    GROUP BY id_gestion
),
docs AS (
    SELECT gr.id_gestion,
           COUNT(DISTINCT aa.id_docente) AS total_docentes
    FROM grupo gr
    INNER JOIN materia_grupo mg        ON mg.id_grupo         = gr.id
    INNER JOIN asignacion_academica aa ON aa.id_materia_grupo  = mg.id
                                      AND aa.estado            = 'activo'
    GROUP BY gr.id_gestion
)
SELECT
    g.id                           AS id_gestion,
    g.nombre                       AS gestion,
    g.anio,
    COALESCE(c.total_grupos,    0) AS total_grupos,
    COALESCE(c.capacidad_total, 0) AS capacidad_total,
    COUNT(DISTINCT a.id)           AS estudiantes_asignados,
    COALESCE(d.total_docentes,  0) AS total_docentes
FROM gestion g
LEFT JOIN caps c     ON c.id_gestion = g.id
LEFT JOIN docs d     ON d.id_gestion = g.id
LEFT JOIN admision a ON a.id_gestion = g.id AND a.id_grupo IS NOT NULL
GROUP BY g.id, g.nombre, g.anio, c.total_grupos, c.capacidad_total, d.total_docentes;
");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') return;

        DB::unprepared("
CREATE OR REPLACE VIEW vw_grupos_habilitados AS
SELECT
    g.id                            AS id_gestion,
    g.nombre                        AS gestion,
    g.anio,
    COUNT(DISTINCT gr.id)           AS total_grupos,
    COALESCE(SUM(gr.cupo_maximo),0) AS capacidad_total,
    COUNT(DISTINCT a.id)            AS estudiantes_asignados,
    COUNT(DISTINCT aa.id_docente)   AS total_docentes
FROM gestion g
LEFT JOIN grupo gr               ON gr.id_gestion = g.id AND gr.estado = 'activo'
LEFT JOIN admision a             ON a.id_gestion  = g.id AND a.id_grupo IS NOT NULL
LEFT JOIN materia_grupo mg       ON mg.id_grupo   = gr.id
LEFT JOIN asignacion_academica aa ON aa.id_materia_grupo = mg.id AND aa.estado = 'activo'
GROUP BY g.id, g.nombre, g.anio;
");
    }
};
