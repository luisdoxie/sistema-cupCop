-- ============================================================
-- FUNCIONES Y TRIGGERS DEL SISTEMA CUP
-- ============================================================

-- calcular_nota_materia: suma de los 3 examenes de una materia
CREATE OR REPLACE FUNCTION calcular_nota_materia(p_id_admision BIGINT, p_id_materia_grupo BIGINT)
RETURNS DECIMAL AS $$
DECLARE
    v_total DECIMAL := 0;
BEGIN
    SELECT COALESCE(SUM(n.calificacion), 0)
    INTO v_total
    FROM nota n
    INNER JOIN examen e ON e.id = n.id_examen
    WHERE n.id_admision = p_id_admision
      AND e.id_materia_grupo = p_id_materia_grupo
      AND n.estado != 'anulada';

    RETURN v_total;
END;
$$ LANGUAGE plpgsql;

-- calcular_promedio_materia: promedio ponderado (P1*0.30 + P2*0.30 + Final*0.40)
CREATE OR REPLACE FUNCTION calcular_promedio_materia(p_id_admision BIGINT, p_id_materia_grupo BIGINT)
RETURNS DECIMAL AS $$
DECLARE
    v_promedio DECIMAL;
BEGIN
    SELECT COALESCE(SUM(n.calificacion * e.puntaje_maximo / 100.0), 0)
    INTO v_promedio
    FROM nota n
    INNER JOIN examen e ON e.id = n.id_examen
    WHERE n.id_admision = p_id_admision
      AND e.id_materia_grupo = p_id_materia_grupo
      AND n.estado != 'anulada';

    RETURN v_promedio;
END;
$$ LANGUAGE plpgsql;

-- verificar_aprobacion: TRUE si >= 60 en las 4 materias del grupo
CREATE OR REPLACE FUNCTION verificar_aprobacion(p_id_admision BIGINT)
RETURNS BOOLEAN AS $$
DECLARE
    v_aprobadas INT := 0;
    v_total_materias INT := 0;
    r RECORD;
BEGIN
    FOR r IN
        SELECT mg.id AS id_materia_grupo
        FROM admision a
        INNER JOIN grupo g ON g.id = a.id_grupo
        INNER JOIN materia_grupo mg ON mg.id_grupo = g.id
        WHERE a.id = p_id_admision
    LOOP
        v_total_materias := v_total_materias + 1;
        IF calcular_promedio_materia(p_id_admision, r.id_materia_grupo) >= 60 THEN
            v_aprobadas := v_aprobadas + 1;
        END IF;
    END LOOP;

    IF v_total_materias = 0 THEN
        RETURN FALSE;
    END IF;

    RETURN v_aprobadas >= 4;
END;
$$ LANGUAGE plpgsql;

-- calcular_promedio_final: promedio de las 4 materias
CREATE OR REPLACE FUNCTION calcular_promedio_final(p_id_admision BIGINT)
RETURNS DECIMAL AS $$
DECLARE
    v_suma DECIMAL := 0;
    v_count INT := 0;
    r RECORD;
BEGIN
    FOR r IN
        SELECT mg.id AS id_materia_grupo
        FROM admision a
        INNER JOIN grupo g ON g.id = a.id_grupo
        INNER JOIN materia_grupo mg ON mg.id_grupo = g.id
        WHERE a.id = p_id_admision
    LOOP
        v_suma := v_suma + calcular_promedio_materia(p_id_admision, r.id_materia_grupo);
        v_count := v_count + 1;
    END LOOP;

    IF v_count = 0 THEN
        RETURN 0;
    END IF;

    RETURN v_suma / v_count;
END;
$$ LANGUAGE plpgsql;

-- calcular_grupos_necesarios: cuantos grupos se necesitan para una gestion
CREATE OR REPLACE FUNCTION calcular_grupos_necesarios(p_id_gestion BIGINT)
RETURNS INT AS $$
DECLARE
    v_inscritos INT;
    v_divisor INT;
BEGIN
    SELECT COUNT(*)
    INTO v_inscritos
    FROM admision
    WHERE id_gestion = p_id_gestion
      AND estado NOT IN ('no_admitido', 'reprobado');

    SELECT CAST(valor AS INT)
    INTO v_divisor
    FROM config_sistema
    WHERE clave = 'divisor_grupos';

    IF v_divisor IS NULL OR v_divisor = 0 THEN
        v_divisor := 70;
    END IF;

    RETURN CEIL(v_inscritos::DECIMAL / v_divisor);
END;
$$ LANGUAGE plpgsql;

-- procesar_admision_carrera: intenta carrera1 -> carrera2 -> no_admitido
CREATE OR REPLACE FUNCTION procesar_admision_carrera(p_id_admision BIGINT)
RETURNS VARCHAR AS $$
DECLARE
    v_admision admision%ROWTYPE;
    v_cupo_c1 INT;
    v_cupo_c2 INT;
    v_resultado VARCHAR;
BEGIN
    SELECT * INTO v_admision FROM admision WHERE id = p_id_admision;

    -- Verificar cupo en carrera1
    SELECT cupo_disponible INTO v_cupo_c1
    FROM carrera_gestion
    WHERE id_carrera = v_admision.id_carrera1
      AND id_gestion = v_admision.id_gestion;

    IF v_cupo_c1 > 0 THEN
        UPDATE carrera_gestion
        SET cupo_disponible = cupo_disponible - 1
        WHERE id_carrera = v_admision.id_carrera1
          AND id_gestion = v_admision.id_gestion;

        UPDATE admision SET estado = 'admitido_carrera1' WHERE id = p_id_admision;
        v_resultado := 'admitido_carrera1';
    ELSE
        -- Verificar cupo en carrera2
        SELECT cupo_disponible INTO v_cupo_c2
        FROM carrera_gestion
        WHERE id_carrera = v_admision.id_carrera2
          AND id_gestion = v_admision.id_gestion;

        IF v_cupo_c2 > 0 THEN
            UPDATE carrera_gestion
            SET cupo_disponible = cupo_disponible - 1
            WHERE id_carrera = v_admision.id_carrera2
              AND id_gestion = v_admision.id_gestion;

            UPDATE admision SET estado = 'admitido_carrera2' WHERE id = p_id_admision;
            v_resultado := 'admitido_carrera2';
        ELSE
            UPDATE admision SET estado = 'no_admitido' WHERE id = p_id_admision;
            v_resultado := 'no_admitido';
        END IF;
    END IF;

    RETURN v_resultado;
END;
$$ LANGUAGE plpgsql;

-- ============================================================
-- TRIGGER: validar_cruce_horario en bloque_horario
-- ============================================================
CREATE OR REPLACE FUNCTION fn_validar_cruce_horario()
RETURNS TRIGGER AS $$
DECLARE
    v_id_docente BIGINT;
    v_cruces INT;
BEGIN
    SELECT aa.id_docente INTO v_id_docente
    FROM asignacion_academica aa
    WHERE aa.id = NEW.id_asignacion;

    SELECT COUNT(*) INTO v_cruces
    FROM bloque_horario bh
    INNER JOIN asignacion_academica aa ON aa.id = bh.id_asignacion
    WHERE aa.id_docente = v_id_docente
      AND bh.dia = NEW.dia
      AND bh.id != COALESCE(NEW.id, 0)
      AND (
            (NEW.hora_inicio >= bh.hora_inicio AND NEW.hora_inicio < bh.hora_fin)
         OR (NEW.hora_fin > bh.hora_inicio AND NEW.hora_fin <= bh.hora_fin)
         OR (NEW.hora_inicio <= bh.hora_inicio AND NEW.hora_fin >= bh.hora_fin)
      );

    IF v_cruces > 0 THEN
        RAISE EXCEPTION 'El docente tiene cruce de horario el dia % entre % y %', NEW.dia, NEW.hora_inicio, NEW.hora_fin;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_validar_cruce_horario ON bloque_horario;
CREATE TRIGGER trg_validar_cruce_horario
    BEFORE INSERT OR UPDATE ON bloque_horario
    FOR EACH ROW EXECUTE FUNCTION fn_validar_cruce_horario();

-- ============================================================
-- TRIGGER: validar_max_grupos_docente en asignacion_academica
-- ============================================================
CREATE OR REPLACE FUNCTION fn_validar_max_grupos_docente()
RETURNS TRIGGER AS $$
DECLARE
    v_max_grupos INT;
    v_grupos_actuales INT;
BEGIN
    SELECT d.max_grupos INTO v_max_grupos
    FROM docente d
    WHERE d.id = NEW.id_docente;

    SELECT COUNT(*) INTO v_grupos_actuales
    FROM asignacion_academica aa
    WHERE aa.id_docente = NEW.id_docente
      AND aa.estado = 'activo'
      AND aa.id != COALESCE(NEW.id, 0);

    IF v_grupos_actuales >= v_max_grupos THEN
        RAISE EXCEPTION 'El docente ha alcanzado el maximo de % grupos asignados', v_max_grupos;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_validar_max_grupos_docente ON asignacion_academica;
CREATE TRIGGER trg_validar_max_grupos_docente
    BEFORE INSERT OR UPDATE ON asignacion_academica
    FOR EACH ROW EXECUTE FUNCTION fn_validar_max_grupos_docente();
