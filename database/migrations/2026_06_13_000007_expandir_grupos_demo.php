<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        // ── 1. Actualizar calcular_promedio_materia a fórmula ponderada ──────
        // P1*0.30 + P2*0.30 + Final*0.40  (puntaje_maximo = 30|30|40 = el peso)
        DB::unprepared("
CREATE OR REPLACE FUNCTION calcular_promedio_materia(p_id_admision BIGINT, p_id_materia_grupo BIGINT)
RETURNS DECIMAL AS \$\$
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
\$\$ LANGUAGE plpgsql;
");

        // ── Datos base ────────────────────────────────────────────────────────
        $gestiones = DB::table('gestion')->get();
        if ($gestiones->isEmpty()) return;

        $materiaIds = DB::table('materia')
            ->whereIn('sigla', ['COMP', 'MAT', 'ING', 'FIS'])
            ->pluck('id', 'sigla');
        if ($materiaIds->isEmpty()) return;

        $siglaToEsp = [
            'COMP' => 'Computacion', 'MAT' => 'Matematicas',
            'ING'  => 'Ingles',      'FIS' => 'Fisica',
        ];

        $specsMap = [
            '50000001' => 'Computacion', '50000002' => 'Computacion',
            '50000003' => 'Matematicas', '50000004' => 'Matematicas',
            '50000005' => 'Ingles',      '50000006' => 'Ingles',
            '50000007' => 'Fisica',      '50000008' => 'Fisica',
        ];

        $docenteIds = [];
        foreach ($specsMap as $ci => $esp) {
            $pid = DB::table('persona')->where('ci', $ci)->value('id');
            if (! $pid) continue;
            $did = DB::table('docente')->where('id_persona', $pid)->value('id');
            if (! $did) continue;
            $docenteIds[$esp][] = $did;
        }

        $aulaIds = DB::table('aula')->pluck('id')->toArray();
        $carreras = DB::table('carrera')->pluck('id')->toArray();

        $turnos = DB::table('turno')->pluck('id', 'nombre');
        $turnoManana = $turnos['Manana'] ?? $turnos->first();
        $turnoTarde  = $turnos['Tarde']  ?? $turnoManana;
        $turnoNoche  = $turnos['Noche']  ?? $turnoManana;

        if (empty($docenteIds) || empty($aulaIds) || empty($carreras) || ! $turnoManana) return;

        // ── 0. Corregir turno de grupos A y B ya existentes ──────────────────
        // Migración 000004 asignó el primer turno a todos; B debe ser Tarde.
        DB::statement("
            UPDATE materia_grupo
            SET id_turno = ?
            WHERE id_grupo IN (SELECT id FROM grupo WHERE paralelo = 'B')
        ", [$turnoTarde]);

        // Turno por paralelo para los grupos nuevos
        $turnosPorParalelo = [
            'C' => $turnoManana,
            'D' => $turnoManana,
            'E' => $turnoNoche,
            'F' => $turnoNoche,
        ];

        // Horarios para paralelos C, D, E, F
        // Distribuidos en mañana (10-13), tarde (14-17 ya existente en B), noche (19-22)
        // Verificados sin cruce real para cada docente dentro de la misma gestión:
        //   docente0 (A, C, E): A=lu07-10 + C=ma10-13 + E=lu19-22  → ok (lu07≠lu19, ma es distinto)
        //   docente1 (B, D, F): B=ju14-17 + D=mi10-13 + F=mi19-22  → ok (ju≠mi, mi10≠mi19)
        $nuevoHorario = [
            'C' => ['dia' => 'martes',    'inicio' => '10:00', 'fin' => '13:00'],
            'D' => ['dia' => 'miercoles', 'inicio' => '10:00', 'fin' => '13:00'],
            'E' => ['dia' => 'lunes',     'inicio' => '19:00', 'fin' => '22:00'],
            'F' => ['dia' => 'miercoles', 'inicio' => '19:00', 'fin' => '22:00'],
        ];
        // docente0 → paralelos C, E  |  docente1 → paralelos D, F
        $paraleloDcIdx = ['C' => 0, 'D' => 1, 'E' => 0, 'F' => 1];

        $diaDow = [
            'lunes' => 1, 'martes' => 2, 'miercoles' => 3,
            'jueves' => 4, 'viernes' => 5, 'sabado' => 6,
        ];

        // ── 2. Crear grupos C, D, E, F (idempotente: salta si ya existen) ──
        $yaExpandido = DB::table('grupo')->where('paralelo', 'C')->exists();

        if (! $yaExpandido) {
            DB::statement('ALTER TABLE asignacion_academica DISABLE TRIGGER USER');
            DB::statement('ALTER TABLE bloque_horario DISABLE TRIGGER USER');

            try {
                foreach ($gestiones as $gestion) {
                    $hoy        = new \DateTime('today');
                    $fechaIni   = new \DateTime($gestion->fecha_inicio);
                    $fechaFin   = new \DateTime($gestion->fecha_fin);
                    $esCerrada  = $gestion->estado === 'cerrado';

                    foreach (['C', 'D', 'E', 'F'] as $paralelo) {
                        $grupoId = DB::table('grupo')->insertGetId([
                            'nombre'      => 'Grupo ' . $paralelo,
                            'paralelo'    => $paralelo,
                            'modalidad'   => 'presencial',
                            'cupo_maximo' => 60,
                            'estado'      => $esCerrada ? 'cerrado' : 'activo',
                            'id_gestion'  => $gestion->id,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);

                        $slot   = $nuevoHorario[$paralelo];
                        $dcIdx  = $paraleloDcIdx[$paralelo];
                        // Aulas: índices 8-11 para C-F (rotando sobre array de aulas)
                        $aulaIdx = (8 + array_search($paralelo, ['C', 'D', 'E', 'F'])) % count($aulaIds);
                        $aulaId  = $aulaIds[$aulaIdx];

                        foreach ($materiaIds as $sigla => $materiaId) {
                            $esp = $siglaToEsp[$sigla] ?? null;
                            if (! $esp) continue;
                            $did = $docenteIds[$esp][$dcIdx] ?? null;
                            if (! $did) continue;

                            // materia_grupo
                            $mgId = DB::table('materia_grupo')->insertGetId([
                                'id_grupo'   => $grupoId,
                                'id_materia' => $materiaId,
                                'id_turno'   => $turnosPorParalelo[$paralelo],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            // 3 exámenes con puntaje_maximo correcto (peso %)
                            $examenIds = [];
                            foreach ([['parcial1', 30, 1], ['parcial2', 30, 2], ['final', 40, 3]] as [$tipo, $pMax, $mes]) {
                                $examenIds[$tipo] = DB::table('examen')->insertGetId([
                                    'id_materia_grupo' => $mgId,
                                    'tipo'             => $tipo,
                                    'puntaje_maximo'   => $pMax,
                                    'fecha'            => date('Y-m-d', strtotime($gestion->fecha_inicio . ' +' . $mes . ' months')),
                                    'estado'           => $esCerrada ? 'realizado' : 'programado',
                                    'created_at'       => now(),
                                    'updated_at'       => now(),
                                ]);
                            }

                            // Asignación académica
                            DB::statement(
                                'INSERT INTO asignacion_academica (id_docente, id_materia_grupo, carga_horaria, fecha_asignacion, estado, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
                                [$did, $mgId, 4.0, $gestion->fecha_inicio, 'activo', now(), now()]
                            );

                            $asigRow = DB::selectOne(
                                'SELECT id FROM asignacion_academica WHERE id_docente = ? AND id_materia_grupo = ?',
                                [$did, $mgId]
                            );
                            if (! $asigRow) continue;
                            $asigId = $asigRow->id;

                            // Bloque horario
                            $bloqueId = DB::table('bloque_horario')->insertGetId([
                                'id_asignacion' => $asigId,
                                'dia'           => $slot['dia'],
                                'hora_inicio'   => $slot['inicio'],
                                'hora_fin'      => $slot['fin'],
                                'created_at'    => now(),
                                'updated_at'    => now(),
                            ]);

                            // Clases programadas (hasta 16 por semestre)
                            $dow     = $diaDow[$slot['dia']];
                            $current = clone $fechaIni;
                            while ((int) $current->format('N') !== $dow) {
                                $current->modify('+1 day');
                            }
                            $cnt = 0;
                            while ($current <= $fechaFin && $cnt < 16) {
                                $estadoClase = ($esCerrada || $current < $hoy) ? 'realizada' : 'programada';
                                DB::table('clase_programada')->insert([
                                    'id_asignacion' => $asigId,
                                    'id_aula'       => $aulaId,
                                    'id_bloque'     => $bloqueId,
                                    'fecha'         => $current->format('Y-m-d'),
                                    'estado'        => $estadoClase,
                                    'created_at'    => now(),
                                    'updated_at'    => now(),
                                ]);
                                $current->modify('+1 week');
                                $cnt++;
                            }
                        }
                    }
                }
            } finally {
                DB::statement('ALTER TABLE asignacion_academica ENABLE TRIGGER USER');
                DB::statement('ALTER TABLE bloque_horario ENABLE TRIGGER USER');
            }
        }

        // ── 3. Completar notas parcial2 y final en gestión(es) activas/inscripciones ──
        // La migración 000004 solo insertó parcial1 para la gestión activa.
        $gestionesNoC = DB::table('gestion')->where('estado', '!=', 'cerrado')->get();
        foreach ($gestionesNoC as $gestion) {
            $admisiones = DB::table('admision')
                ->where('id_gestion', $gestion->id)
                ->select('id', 'id_grupo', 'promedio_final')
                ->get();

            foreach ($admisiones as $adm) {
                // Inferir si aprueba por la nota de parcial1 existente
                $p1 = DB::table('nota as n')
                    ->join('examen as e', 'e.id', '=', 'n.id_examen')
                    ->where('n.id_admision', $adm->id)
                    ->where('e.tipo', 'parcial1')
                    ->value('n.calificacion');

                $passes = ($p1 !== null && (float) $p1 >= 60);

                $mgs = DB::table('materia_grupo')->where('id_grupo', $adm->id_grupo)->pluck('id');

                $notaBatch = [];
                foreach ($mgs as $mgId) {
                    $examenes = DB::table('examen')
                        ->where('id_materia_grupo', $mgId)
                        ->whereIn('tipo', ['parcial2', 'final'])
                        ->get();

                    foreach ($examenes as $ex) {
                        $ya = DB::table('nota')
                            ->where('id_admision', $adm->id)
                            ->where('id_examen', $ex->id)
                            ->exists();
                        if ($ya) continue;

                        $score = $passes ? rand(61, 95) : rand(15, 55);
                        $notaBatch[] = [
                            'id_examen'    => $ex->id,
                            'id_admision'  => $adm->id,
                            'calificacion' => $score,
                            'estado'       => 'registrada',
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ];
                    }
                }

                if (! empty($notaBatch)) {
                    DB::table('nota')->insertOrIgnore($notaBatch);
                }
            }
        }

        // ── 4. Agregar postulantes hasta llegar a 300 por gestión ────────────
        // CI base: 70000001+ (rango diferente a los de migración 000004: 61xxxxx)
        $maxCiRow = DB::selectOne(
            "SELECT COALESCE(MAX(ci::bigint), 70000000) AS max FROM persona WHERE ci ~ '^7[0-9]{7}$'"
        );
        $ciBase = (int) ($maxCiRow->max ?? 70000000) + 1;

        $paraleloCycle = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($gestiones as $gestion) {
            $existing = DB::table('admision')->where('id_gestion', $gestion->id)->count();
            $needed   = max(0, 300 - $existing);
            if ($needed <= 0) continue;

            $esCerrada = $gestion->estado === 'cerrado';

            // Cargar grupos y sus materia_grupos/exámenes para evitar N+1 en el loop
            $grupos = DB::table('grupo')
                ->where('id_gestion', $gestion->id)
                ->whereNotNull('paralelo')
                ->get()
                ->keyBy('paralelo');

            if ($grupos->isEmpty()) continue;

            // Pre-cargar exámenes por grupo
            $examenesPorGrupo = [];
            foreach ($grupos as $paralelo => $grupo) {
                $mgs = DB::table('materia_grupo')
                    ->where('id_grupo', $grupo->id)
                    ->pluck('id');
                $examenesPorGrupo[$grupo->id] = DB::table('examen')
                    ->whereIn('id_materia_grupo', $mgs)
                    ->get()
                    ->toArray();
            }

            $nombresM  = ['Juan', 'Luis', 'Carlos', 'Pedro', 'Miguel', 'Diego', 'Marco', 'Pablo', 'Oscar', 'Raul', 'Victor', 'Ruben', 'Andres', 'Felipe', 'Jorge'];
            $nombresF  = ['Maria', 'Ana', 'Laura', 'Sofia', 'Diana', 'Rosa', 'Claudia', 'Elena', 'Isabel', 'Carla', 'Fanny', 'Wendy', 'Valeria', 'Paola', 'Sandra'];
            $apellidos = ['Garcia', 'Lopez', 'Martinez', 'Rodriguez', 'Gomez', 'Perez', 'Sanchez', 'Torres', 'Ramirez', 'Flores', 'Condori', 'Mamani', 'Quispe', 'Torrez', 'Copa', 'Vargas', 'Mendoza', 'Rojas', 'Salazar', 'Cruz'];

            for ($i = 0; $i < $needed; $i++) {
                $ci    = (string) $ciBase++;
                $sexo  = ($i % 2 === 0) ? 'M' : 'F';
                $nom   = $sexo === 'M' ? $nombresM[$i % count($nombresM)] : $nombresF[$i % count($nombresF)];
                $ape1  = $apellidos[$i % count($apellidos)];
                $ape2  = $apellidos[($i + 7) % count($apellidos)];

                $personaId = DB::table('persona')->insertGetId([
                    'ci'         => $ci,
                    'nombre'     => $nom,
                    'apellido'   => $ape1 . ' ' . $ape2,
                    'sexo'       => $sexo,
                    'correo'     => strtolower($nom . '.' . $ape1 . $ci) . '@ficct.edu.bo',
                    'password'   => Hash::make('Est2025!'),
                    'rol'        => 'estudiante',
                    'activo'     => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $estId = DB::table('estudiante')->insertGetId([
                    'id_persona'          => $personaId,
                    'colegio_procedencia' => 'Colegio Demo',
                    'ciudad'              => 'Santa Cruz',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);

                $paralelo = $paraleloCycle[$i % count($paraleloCycle)];
                $grupo    = $grupos[$paralelo] ?? $grupos->first();
                $passes   = ($i % 10) < 6; // 60% aprobados

                $estado   = $esCerrada ? ($passes ? 'admitido_carrera1' : 'reprobado') : 'cursando';
                $promedio = $esCerrada
                    ? ($passes
                        ? round(rand(61, 95) + rand(0, 99) / 100, 2)
                        : round(rand(15, 55) + rand(0, 99) / 100, 2))
                    : null;

                $c1 = $carreras[$i % count($carreras)];
                $c2 = $carreras[($i + 1) % count($carreras)];
                if ($c1 === $c2) {
                    $c2 = $carreras[($i + 2) % count($carreras)];
                }

                $admId = DB::table('admision')->insertGetId([
                    'id_estudiante'  => $estId,
                    'id_gestion'     => $gestion->id,
                    'id_grupo'       => $grupo->id,
                    'id_carrera1'    => $c1,
                    'id_carrera2'    => $c2,
                    'fecha'          => $gestion->fecha_inicio,
                    'estado'         => $estado,
                    'promedio_final' => $promedio,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // Notas para los 3 exámenes (scores 0-100 en escala uniforme)
                // Aprobados: rand(61-95) → ponderado mín 61 > 60 ✓
                // Reprobados: rand(15-55) → ponderado máx 55 < 60 ✓
                $notaBatch = [];
                foreach ($examenesPorGrupo[$grupo->id] ?? [] as $ex) {
                    $score = $passes ? rand(61, 95) : rand(15, 55);
                    $notaBatch[] = [
                        'id_examen'    => $ex->id,
                        'id_admision'  => $admId,
                        'calificacion' => $score,
                        'estado'       => 'registrada',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                }
                if (! empty($notaBatch)) {
                    DB::table('nota')->insertOrIgnore($notaBatch);
                }
            }
        }
    }

    public function down(): void {}
};
