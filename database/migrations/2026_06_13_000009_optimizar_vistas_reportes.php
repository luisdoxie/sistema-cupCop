<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') return;

        // ── 1. Índices para eliminar seq scans en queries de reportes ─────────
        $indexes = [
            'idx_nota_id_admision'               => 'nota(id_admision)',
            'idx_nota_id_examen'                 => 'nota(id_examen)',
            'idx_examen_id_materia_grupo'        => 'examen(id_materia_grupo)',
            'idx_admision_id_gestion'            => 'admision(id_gestion)',
            'idx_admision_id_grupo'              => 'admision(id_grupo)',
            'idx_asistencia_id_clase'            => 'asistencia(id_clase)',
            'idx_asistencia_id_admision'         => 'asistencia(id_admision)',
            'idx_clase_programada_id_asignacion' => 'clase_programada(id_asignacion)',
            'idx_clase_programada_estado'        => 'clase_programada(estado)',
        ];

        foreach ($indexes as $name => $definition) {
            DB::statement("CREATE INDEX IF NOT EXISTS {$name} ON {$definition}");
        }

        // ── 2. vw_notas_estudiante ────────────────────────────────────────────
        // Antes: calcular_promedio_materia() llamada 2x por fila (~4 800 llamadas)
        // Ahora: SUM inline en el GROUP BY, referenciado desde CTE (0 llamadas)
        DB::unprepared("
CREATE OR REPLACE VIEW vw_notas_estudiante AS
WITH base AS (
    SELECT
        p.ci,
        p.nombre || ' ' || p.apellido    AS estudiante,
        m.nombre                          AS materia,
        m.id                              AS id_materia,
        mg.id                             AS id_materia_grupo,
        a.id                              AS id_admision,
        a.id_gestion,
        g.nombre                          AS gestion,
        MAX(CASE WHEN ex.tipo = 'parcial1' THEN n.calificacion END) AS p1,
        MAX(CASE WHEN ex.tipo = 'parcial2' THEN n.calificacion END) AS p2,
        MAX(CASE WHEN ex.tipo = 'final'    THEN n.calificacion END) AS final_nota,
        COALESCE(SUM(n.calificacion), 0)                              AS total,
        COALESCE(SUM(n.calificacion * ex.puntaje_maximo / 100.0), 0) AS promedio
    FROM admision a
    INNER JOIN estudiante e     ON e.id        = a.id_estudiante
    INNER JOIN persona p        ON p.id        = e.id_persona
    INNER JOIN gestion g        ON g.id        = a.id_gestion
    INNER JOIN grupo gr         ON gr.id       = a.id_grupo
    INNER JOIN materia_grupo mg ON mg.id_grupo = gr.id
    INNER JOIN materia m        ON m.id        = mg.id_materia
    LEFT  JOIN examen ex        ON ex.id_materia_grupo = mg.id
    LEFT  JOIN nota n           ON n.id_admision = a.id
                               AND n.id_examen   = ex.id
                               AND n.estado      != 'anulada'
    WHERE a.id_grupo IS NOT NULL
    GROUP BY p.ci, p.nombre, p.apellido, m.nombre, m.id, mg.id, a.id, a.id_gestion, g.nombre
)
SELECT *, CASE WHEN promedio >= 60 THEN 'APROBADO' ELSE 'REPROBADO' END AS resultado
FROM base;
");

        // ── 3. vw_estadisticas_materia ────────────────────────────────────────
        // Antes: calcular_promedio_materia() llamada 5x por fila (~12 000 llamadas)
        // Ahora: CTE calcula el promedio una vez, el SELECT externo lo agrega
        DB::unprepared("
CREATE OR REPLACE VIEW vw_estadisticas_materia AS
WITH promedios AS (
    SELECT
        m.id      AS id_materia,
        m.nombre  AS materia,
        g.id      AS id_gestion,
        g.nombre  AS gestion,
        a.id      AS id_admision,
        COALESCE(SUM(n.calificacion * ex.puntaje_maximo / 100.0), 0) AS promedio
    FROM admision a
    INNER JOIN grupo gr         ON gr.id       = a.id_grupo
    INNER JOIN materia_grupo mg ON mg.id_grupo = gr.id
    INNER JOIN materia m        ON m.id        = mg.id_materia
    INNER JOIN gestion g        ON g.id        = a.id_gestion
    LEFT  JOIN examen ex        ON ex.id_materia_grupo = mg.id
    LEFT  JOIN nota n           ON n.id_admision = a.id
                               AND n.id_examen   = ex.id
                               AND n.estado      != 'anulada'
    WHERE a.id_grupo IS NOT NULL
    GROUP BY m.id, m.nombre, g.id, g.nombre, a.id
)
SELECT
    id_materia,
    materia,
    id_gestion,
    gestion,
    COUNT(DISTINCT id_admision)                  AS total_estudiantes,
    ROUND(AVG(promedio)::numeric, 2)             AS promedio,
    MAX(promedio)                                AS nota_max,
    MIN(promedio)                                AS nota_min,
    COUNT(CASE WHEN promedio >= 60 THEN 1 END)  AS aprobados,
    COUNT(CASE WHEN promedio <  60 THEN 1 END)  AS reprobados
FROM promedios
GROUP BY id_materia, materia, id_gestion, gestion;
");

        // ── 4. vw_rendimiento_docente ─────────────────────────────────────────
        // Antes: calcular_promedio_materia() llamada 2x por fila (~4 800 llamadas)
        // Ahora: CTE precalcula promedios por (admision, materia_grupo)
        DB::unprepared("
CREATE OR REPLACE VIEW vw_rendimiento_docente AS
WITH promedios AS (
    SELECT
        a.id    AS id_admision,
        mg.id   AS id_materia_grupo,
        COALESCE(SUM(n.calificacion * ex.puntaje_maximo / 100.0), 0) AS promedio
    FROM admision a
    INNER JOIN materia_grupo mg ON mg.id_grupo = a.id_grupo
    LEFT  JOIN examen ex        ON ex.id_materia_grupo = mg.id
    LEFT  JOIN nota n           ON n.id_admision = a.id
                               AND n.id_examen   = ex.id
                               AND n.estado      != 'anulada'
    WHERE a.estado IN ('cursando','admitido_carrera1','admitido_carrera2','reprobado','no_admitido')
      AND a.id_grupo IS NOT NULL
    GROUP BY a.id, mg.id
)
SELECT
    p.nombre || ' ' || p.apellido   AS docente,
    doc.id                           AS id_docente,
    m.nombre                         AS materia,
    m.id                             AS id_materia,
    g.nombre                         AS gestion,
    g.id                             AS id_gestion,
    COUNT(DISTINCT pr.id_admision)   AS total_estudiantes,
    COUNT(CASE WHEN pr.promedio >= 60 THEN 1 END) AS aprobados,
    CASE
        WHEN COUNT(DISTINCT pr.id_admision) > 0
        THEN ROUND(
            COUNT(CASE WHEN pr.promedio >= 60 THEN 1 END)::numeric
            / COUNT(DISTINCT pr.id_admision) * 100, 2)
        ELSE 0
    END AS porcentaje_aprobacion
FROM asignacion_academica aa
INNER JOIN docente doc      ON doc.id = aa.id_docente
INNER JOIN persona p        ON p.id   = doc.id_persona
INNER JOIN materia_grupo mg ON mg.id  = aa.id_materia_grupo
INNER JOIN materia m        ON m.id   = mg.id_materia
INNER JOIN grupo gr         ON gr.id  = mg.id_grupo
INNER JOIN gestion g        ON g.id   = gr.id_gestion
LEFT  JOIN promedios pr     ON pr.id_materia_grupo = aa.id_materia_grupo
GROUP BY p.nombre, p.apellido, doc.id, m.nombre, m.id, g.nombre, g.id;
");

        // ── 5. vw_grupos_habilitados ──────────────────────────────────────────
        // Bug anterior: SUM(gr.cupo_maximo) se multiplicaba por el cross-product
        // admision×materia_grupo (hasta 2 400 filas → valores como 180 000).
        // Fix: CTEs separados para capacidad y docentes, sin cruzar admisiones.
        DB::unprepared("
CREATE OR REPLACE VIEW vw_grupos_habilitados AS
WITH caps AS (
    SELECT id_gestion,
           COUNT(*)                    AS total_grupos,
           COALESCE(SUM(cupo_maximo), 0) AS capacidad_total
    FROM grupo
    WHERE estado = 'activo'
    GROUP BY id_gestion
),
docs AS (
    SELECT gr.id_gestion,
           COUNT(DISTINCT aa.id_docente) AS total_docentes
    FROM grupo gr
    INNER JOIN materia_grupo mg        ON mg.id_grupo        = gr.id
    INNER JOIN asignacion_academica aa ON aa.id_materia_grupo = mg.id
                                      AND aa.estado           = 'activo'
    GROUP BY gr.id_gestion
)
SELECT
    g.id                            AS id_gestion,
    g.nombre                        AS gestion,
    g.anio,
    COALESCE(c.total_grupos,    0)  AS total_grupos,
    COALESCE(c.capacidad_total, 0)  AS capacidad_total,
    COUNT(DISTINCT a.id)            AS estudiantes_asignados,
    COALESCE(d.total_docentes,  0)  AS total_docentes
FROM gestion g
LEFT JOIN caps c    ON c.id_gestion = g.id
LEFT JOIN docs d    ON d.id_gestion = g.id
LEFT JOIN admision a ON a.id_gestion = g.id AND a.id_grupo IS NOT NULL
GROUP BY g.id, g.nombre, g.anio, c.total_grupos, c.capacidad_total, d.total_docentes;
");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') return;

        DB::unprepared("
CREATE OR REPLACE VIEW vw_notas_estudiante AS
SELECT
    p.ci,
    p.nombre || ' ' || p.apellido  AS estudiante,
    m.nombre                        AS materia,
    m.id                            AS id_materia,
    mg.id                           AS id_materia_grupo,
    a.id                            AS id_admision,
    a.id_gestion,
    g.nombre                        AS gestion,
    MAX(CASE WHEN ex.tipo = 'parcial1' THEN n.calificacion END) AS p1,
    MAX(CASE WHEN ex.tipo = 'parcial2' THEN n.calificacion END) AS p2,
    MAX(CASE WHEN ex.tipo = 'final'    THEN n.calificacion END) AS final_nota,
    calcular_nota_materia(a.id, mg.id)     AS total,
    calcular_promedio_materia(a.id, mg.id) AS promedio,
    CASE
        WHEN calcular_promedio_materia(a.id, mg.id) >= 60 THEN 'APROBADO'
        ELSE 'REPROBADO'
    END AS resultado
FROM admision a
INNER JOIN estudiante e   ON e.id  = a.id_estudiante
INNER JOIN persona p      ON p.id  = e.id_persona
INNER JOIN gestion g      ON g.id  = a.id_gestion
INNER JOIN grupo gr       ON gr.id = a.id_grupo
INNER JOIN materia_grupo mg ON mg.id_grupo = gr.id
INNER JOIN materia m      ON m.id  = mg.id_materia
LEFT  JOIN examen ex      ON ex.id_materia_grupo = mg.id
LEFT  JOIN nota n         ON n.id_admision = a.id AND n.id_examen = ex.id AND n.estado != 'anulada'
WHERE a.id_grupo IS NOT NULL
GROUP BY p.ci, p.nombre, p.apellido, m.nombre, m.id, mg.id, a.id, a.id_gestion, g.nombre;
");

        DB::unprepared("
CREATE OR REPLACE VIEW vw_estadisticas_materia AS
SELECT
    m.id                                                        AS id_materia,
    m.nombre                                                    AS materia,
    g.id                                                        AS id_gestion,
    g.nombre                                                    AS gestion,
    COUNT(DISTINCT a.id)                                        AS total_estudiantes,
    ROUND(AVG(calcular_promedio_materia(a.id, mg.id))::numeric, 2) AS promedio,
    MAX(calcular_promedio_materia(a.id, mg.id))                 AS nota_max,
    MIN(calcular_promedio_materia(a.id, mg.id))                 AS nota_min,
    COUNT(CASE WHEN calcular_promedio_materia(a.id, mg.id) >= 60 THEN 1 END) AS aprobados,
    COUNT(CASE WHEN calcular_promedio_materia(a.id, mg.id) <  60 THEN 1 END) AS reprobados
FROM admision a
INNER JOIN grupo gr         ON gr.id = a.id_grupo
INNER JOIN materia_grupo mg ON mg.id_grupo = gr.id
INNER JOIN materia m        ON m.id  = mg.id_materia
INNER JOIN gestion g        ON g.id  = a.id_gestion
WHERE a.id_grupo IS NOT NULL
GROUP BY m.id, m.nombre, g.id, g.nombre;
");

        DB::unprepared("
CREATE OR REPLACE VIEW vw_rendimiento_docente AS
SELECT
    p.nombre || ' ' || p.apellido   AS docente,
    doc.id                           AS id_docente,
    m.nombre                         AS materia,
    m.id                             AS id_materia,
    g.nombre                         AS gestion,
    g.id                             AS id_gestion,
    COUNT(DISTINCT a.id)             AS total_estudiantes,
    COUNT(CASE WHEN calcular_promedio_materia(a.id, mg.id) >= 60 THEN 1 END) AS aprobados,
    CASE
        WHEN COUNT(DISTINCT a.id) > 0
        THEN ROUND(
            COUNT(CASE WHEN calcular_promedio_materia(a.id, mg.id) >= 60 THEN 1 END)::numeric
            / COUNT(DISTINCT a.id) * 100, 2)
        ELSE 0
    END AS porcentaje_aprobacion
FROM asignacion_academica aa
INNER JOIN docente doc      ON doc.id = aa.id_docente
INNER JOIN persona p        ON p.id   = doc.id_persona
INNER JOIN materia_grupo mg ON mg.id  = aa.id_materia_grupo
INNER JOIN materia m        ON m.id   = mg.id_materia
INNER JOIN grupo gr         ON gr.id  = mg.id_grupo
INNER JOIN gestion g        ON g.id   = gr.id_gestion
LEFT  JOIN admision a       ON a.id_grupo = gr.id
    AND a.estado IN ('cursando','admitido_carrera1','admitido_carrera2','reprobado','no_admitido')
GROUP BY p.nombre, p.apellido, doc.id, m.nombre, m.id, g.nombre, g.id;
");

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
LEFT JOIN grupo gr              ON gr.id_gestion = g.id AND gr.estado = 'activo'
LEFT JOIN admision a            ON a.id_gestion  = g.id AND a.id_grupo IS NOT NULL
LEFT JOIN materia_grupo mg      ON mg.id_grupo   = gr.id
LEFT JOIN asignacion_academica aa ON aa.id_materia_grupo = mg.id AND aa.estado = 'activo'
GROUP BY g.id, g.nombre, g.anio;
");

        $indexes = [
            'idx_nota_id_admision','idx_nota_id_examen','idx_examen_id_materia_grupo',
            'idx_admision_id_gestion','idx_admision_id_grupo',
            'idx_asistencia_id_clase','idx_asistencia_id_admision',
            'idx_clase_programada_id_asignacion','idx_clase_programada_estado',
        ];
        foreach ($indexes as $name) {
            DB::statement("DROP INDEX IF EXISTS {$name}");
        }
    }
};
