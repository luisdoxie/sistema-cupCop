<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
CREATE OR REPLACE VIEW vw_lista_postulantes AS
SELECT
    p.ci,
    p.nombre,
    p.apellido,
    p.nombre || ' ' || p.apellido AS nombre_completo,
    p.correo,
    e.colegio_procedencia AS colegio,
    e.ciudad,
    c1.nombre  AS carrera1,
    c1.sigla   AS sigla_carrera1,
    c2.nombre  AS carrera2,
    c2.sigla   AS sigla_carrera2,
    a.estado,
    a.id_gestion,
    g.nombre   AS gestion,
    g.anio,
    CASE
        WHEN a.estado = 'admitido_carrera1' THEN c1.nombre
        WHEN a.estado = 'admitido_carrera2' THEN c2.nombre
        ELSE NULL
    END AS carrera_asignada,
    a.fecha,
    a.id AS id_admision
FROM admision a
INNER JOIN estudiante e  ON e.id  = a.id_estudiante
INNER JOIN persona p     ON p.id  = e.id_persona
INNER JOIN gestion g     ON g.id  = a.id_gestion
LEFT  JOIN carrera c1    ON c1.id = a.id_carrera1
LEFT  JOIN carrera c2    ON c2.id = a.id_carrera2;
");

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
    COUNT(CASE WHEN calcular_promedio_materia(a.id, mg.id) < 60  THEN 1 END) AS reprobados
FROM admision a
INNER JOIN grupo gr         ON gr.id = a.id_grupo
INNER JOIN materia_grupo mg ON mg.id_grupo = gr.id
INNER JOIN materia m        ON m.id  = mg.id_materia
INNER JOIN gestion g        ON g.id  = a.id_gestion
WHERE a.id_grupo IS NOT NULL
GROUP BY m.id, m.nombre, g.id, g.nombre;
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
CREATE OR REPLACE VIEW vw_reporte_admision_gestion AS
SELECT
    g.id                            AS id_gestion,
    g.nombre                        AS gestion,
    g.anio,
    g.semestre,
    COUNT(a.id)                     AS postulantes,
    COUNT(CASE WHEN a.estado IN ('admitido_carrera1','admitido_carrera2') THEN 1 END) AS admitidos,
    COUNT(CASE WHEN a.estado = 'reprobado'   THEN 1 END) AS reprobados,
    COUNT(CASE WHEN a.estado = 'no_admitido' THEN 1 END) AS sin_cupo,
    CASE
        WHEN COUNT(a.id) > 0
        THEN ROUND(
            COUNT(CASE WHEN a.estado IN ('admitido_carrera1','admitido_carrera2') THEN 1 END)::numeric
            / COUNT(a.id) * 100, 2)
        ELSE 0
    END AS porcentaje_admision
FROM gestion g
LEFT JOIN admision a ON a.id_gestion = g.id
GROUP BY g.id, g.nombre, g.anio, g.semestre
ORDER BY g.anio DESC, g.semestre DESC;
");
    }

    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS vw_lista_postulantes");
        DB::unprepared("DROP VIEW IF EXISTS vw_notas_estudiante");
        DB::unprepared("DROP VIEW IF EXISTS vw_estadisticas_materia");
        DB::unprepared("DROP VIEW IF EXISTS vw_grupos_habilitados");
        DB::unprepared("DROP VIEW IF EXISTS vw_rendimiento_docente");
        DB::unprepared("DROP VIEW IF EXISTS vw_reporte_admision_gestion");
    }
};
