<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private bool $canAssign = false;

    public function up(): void
    {
        if (DB::table('docente')->count() >= 8) {
            return;
        }

        // Verify asignacion_academica has id_docente before attempting inserts.
        // On first run the trigger compiles lazily; schema differences can cause
        // "column id_docente does not exist" on that first compilation.
        $this->canAssign = Schema::hasColumn('asignacion_academica', 'id_docente');

        $this->ensureTurnos();
        $this->ensureMaterias();

        $carreras  = DB::table('carrera')->pluck('id', 'sigla')->toArray();
        $materias  = DB::table('materia')->pluck('id', 'sigla')->toArray();
        $turnoId   = DB::table('turno')->value('id');

        $docenteIds = $this->crearDocentes();

        // Gestión 2-2025 (cerrada) — 150 postulantes, 70% aprobados
        $this->poblarGestion(
            'Semestre 2-2025', 2025, 2, '2025-07-01', '2025-12-15',
            '60', 150, 70, $carreras, $materias, $turnoId, $docenteIds
        );

        // Gestión 1-2026 (cerrada) — 160 postulantes, 68% aprobados
        $this->poblarGestion(
            'Semestre 1-2026', 2026, 1, '2026-02-01', '2026-06-30',
            '61', 160, 68, $carreras, $materias, $turnoId, $docenteIds
        );

        // Gestión 1-2025 (activa) — completar con estudiantes existentes
        $this->completarGestion1_2025($carreras, $materias, $turnoId, $docenteIds);

        // Cerrar gestiones históricas
        DB::table('gestion')
            ->whereIn('nombre', ['Semestre 2-2025', 'Semestre 1-2026'])
            ->update(['estado' => 'cerrado', 'updated_at' => now()]);
    }

    public function down(): void {}

    // ─── helpers ──────────────────────────────────────────────────────────────

    private function ensureTurnos(): void
    {
        if (DB::table('turno')->count() > 0) {
            return;
        }
        DB::table('turno')->insert([
            ['nombre' => 'Manana', 'descripcion' => '07:00-12:00', 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tarde',  'descripcion' => '14:00-18:00', 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Noche',  'descripcion' => '19:00-22:00', 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function ensureMaterias(): void
    {
        if (DB::table('materia')->count() > 0) {
            return;
        }
        DB::table('materia')->insert([
            ['nombre' => 'Computacion', 'sigla' => 'COMP', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Matematicas', 'sigla' => 'MAT',  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingles',      'sigla' => 'ING',  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Fisica',      'sigla' => 'FIS',  'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /** Crea 8 docentes (2 por materia). Retorna IDs agrupados por especialidad. */
    private function crearDocentes(): array
    {
        $specs = [
            ['ci' => '50000001', 'nombre' => 'Carlos',  'apellido' => 'Mendoza', 'sexo' => 'M', 'esp' => 'Computacion', 'exp' => 8],
            ['ci' => '50000002', 'nombre' => 'Ana',     'apellido' => 'Vargas',  'sexo' => 'F', 'esp' => 'Computacion', 'exp' => 6],
            ['ci' => '50000003', 'nombre' => 'Roberto', 'apellido' => 'Flores',  'sexo' => 'M', 'esp' => 'Matematicas', 'exp' => 10],
            ['ci' => '50000004', 'nombre' => 'Maria',   'apellido' => 'Quispe',  'sexo' => 'F', 'esp' => 'Matematicas', 'exp' => 7],
            ['ci' => '50000005', 'nombre' => 'Jorge',   'apellido' => 'Mamani',  'sexo' => 'M', 'esp' => 'Ingles',      'exp' => 5],
            ['ci' => '50000006', 'nombre' => 'Lucia',   'apellido' => 'Torrez',  'sexo' => 'F', 'esp' => 'Ingles',      'exp' => 9],
            ['ci' => '50000007', 'nombre' => 'Eduardo', 'apellido' => 'Rojas',   'sexo' => 'M', 'esp' => 'Fisica',      'exp' => 12],
            ['ci' => '50000008', 'nombre' => 'Carmen',  'apellido' => 'Salazar', 'sexo' => 'F', 'esp' => 'Fisica',      'exp' => 6],
        ];

        $ids = [];
        foreach ($specs as $s) {
            if (DB::table('persona')->where('ci', $s['ci'])->exists()) {
                // Docente already exists — look up existing id
                $pId = DB::table('persona')->where('ci', $s['ci'])->value('id');
                $dId = DB::table('docente')->where('id_persona', $pId)->value('id');
                if ($dId) {
                    $ids[$s['esp']][] = $dId;
                }
                continue;
            }
            $pId = DB::table('persona')->insertGetId([
                'ci'         => $s['ci'],
                'nombre'     => $s['nombre'],
                'apellido'   => $s['apellido'],
                'sexo'       => $s['sexo'],
                'correo'     => strtolower($s['nombre'] . '.' . $s['apellido']) . '@ficct.edu.bo',
                'password'   => Hash::make('Docente2025!'),
                'rol'        => 'docente',
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $dId = DB::table('docente')->insertGetId([
                'id_persona'          => $pId,
                'especialidad'        => $s['esp'],
                'grado_academico'     => 'Licenciatura',
                'diplomado_educacion' => true,
                'anios_experiencia'   => $s['exp'],
                'max_grupos'          => 5,
                'estado'              => 'activo',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
            $ids[$s['esp']][] = $dId;
        }
        return $ids;
    }

    /**
     * Crea una gestión con grupos, estudiantes ficticios, asignaciones y notas completas.
     * passRate: porcentaje (0-100) de estudiantes que aprueban.
     */
    private function poblarGestion(
        string $nombre, int $anio, int $semestre,
        string $fechaInicio, string $fechaFin,
        string $prefixCI, int $numEstudiantes, int $passRate,
        array $carreras, array $materias, int $turnoId, array $docenteIds
    ): void {
        // Gestión
        $gestionId = DB::table('gestion')->where('nombre', $nombre)->value('id');
        if (! $gestionId) {
            $gestionId = DB::table('gestion')->insertGetId([
                'nombre'      => $nombre,
                'anio'        => $anio,
                'semestre'    => $semestre,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin'   => $fechaFin,
                'estado'      => 'activo',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Carrera_gestion
        foreach (array_values($carreras) as $carreraId) {
            DB::table('carrera_gestion')->insertOrIgnore([
                'id_carrera'      => $carreraId,
                'id_gestion'      => $gestionId,
                'cupo_maximo'     => 80,
                'cupo_disponible' => 80,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // Grupos A y B
        $grupoIds = [];
        foreach (['A', 'B'] as $paralelo) {
            $gId = DB::table('grupo')
                ->where('id_gestion', $gestionId)
                ->where('paralelo', $paralelo)
                ->value('id');
            if (! $gId) {
                $gId = DB::table('grupo')->insertGetId([
                    'id_gestion'  => $gestionId,
                    'nombre'      => 'Grupo ' . $paralelo,
                    'paralelo'    => $paralelo,
                    'modalidad'   => 'presencial',
                    'cupo_maximo' => 75,
                    'estado'      => 'activo',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
            $grupoIds[$paralelo] = $gId;
        }

        // Materia_grupo, asignaciones y exámenes
        [$materiaGrupoIds, $examenIds] = $this->crearEstructuraAcademica(
            $grupoIds, $materias, $turnoId, $docenteIds, $fechaInicio
        );

        // Estudiantes y notas
        $carreraList  = array_values($carreras);
        $numPorGrupo  = (int) ceil($numEstudiantes / 2);
        $nombres_m    = ['Juan', 'Luis', 'Carlos', 'Pedro', 'Miguel', 'Diego', 'Marco', 'Pablo', 'Oscar', 'Raul', 'Victor', 'Ruben'];
        $nombres_f    = ['Maria', 'Ana', 'Laura', 'Sofia', 'Diana', 'Rosa', 'Claudia', 'Elena', 'Isabel', 'Carla', 'Fanny', 'Wendy'];
        $apellidos    = ['Garcia', 'Lopez', 'Martinez', 'Rodriguez', 'Gomez', 'Perez', 'Sanchez', 'Torres', 'Ramirez', 'Flores', 'Condori', 'Mamani', 'Quispe', 'Torrez', 'Copa'];

        foreach (['A', 'B'] as $pIdx => $paralelo) {
            // Grupo A tiene mayor tasa de aprobación
            $grupoPass = $paralelo === 'A' ? min(95, $passRate + 10) : max(30, $passRate - 10);

            for ($i = 1; $i <= $numPorGrupo; $i++) {
                $ci = $prefixCI . str_pad($pIdx * $numPorGrupo + $i, 5, '0', STR_PAD_LEFT);

                if (DB::table('persona')->where('ci', $ci)->exists()) {
                    continue;
                }

                $sexo     = ($i % 3 === 0) ? 'F' : 'M';
                $nombre   = $sexo === 'M' ? $nombres_m[$i % count($nombres_m)] : $nombres_f[$i % count($nombres_f)];
                $apellido = $apellidos[$i % count($apellidos)];

                $pId = DB::table('persona')->insertGetId([
                    'ci'         => $ci,
                    'nombre'     => $nombre,
                    'apellido'   => $apellido,
                    'sexo'       => $sexo,
                    'correo'     => $ci . '@est.ficct.edu.bo',
                    'password'   => Hash::make($ci . strtolower(substr($apellido, 0, 3)) . '!'),
                    'rol'        => 'estudiante',
                    'activo'     => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $estId = DB::table('estudiante')->insertGetId([
                    'id_persona'          => $pId,
                    'fecha_nacimiento'    => '2005-' . str_pad(($i % 12) + 1, 2, '0', STR_PAD_LEFT) . '-' . str_pad(($i % 28) + 1, 2, '0', STR_PAD_LEFT),
                    'colegio_procedencia' => 'Colegio Nacional Santa Cruz',
                    'ciudad'              => 'Santa Cruz',
                    'titulo_bachiller'    => true,
                    'estado'              => 'activo',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);

                $passes = (($i % 100) < $grupoPass);

                // Carreras diferentes
                $c1 = $carreraList[$i % 4];
                $c2 = $carreraList[($i + 1) % 4];
                if ($c1 === $c2) {
                    $c2 = $carreraList[($i + 2) % 4];
                }

                $estado   = $passes ? 'admitido_carrera1' : 'reprobado';
                $promedio = $passes
                    ? round(rand(62, 90) + rand(0, 99) / 100, 2)
                    : round(rand(25, 58) + rand(0, 99) / 100, 2);

                $admisionId = DB::table('admision')->insertGetId([
                    'id_estudiante'  => $estId,
                    'id_gestion'     => $gestionId,
                    'id_grupo'       => $grupoIds[$paralelo],
                    'id_carrera1'    => $c1,
                    'id_carrera2'    => $c2,
                    'fecha'          => $fechaInicio,
                    'estado'         => $estado,
                    'promedio_final' => $promedio,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // Notas: 3 exámenes × 4 materias
                foreach (array_keys($materias) as $sigla) {
                    foreach (['parcial1', 'parcial2', 'final'] as $tipo) {
                        $eId = $examenIds[$paralelo][$sigla][$tipo] ?? null;
                        if (! $eId) {
                            continue;
                        }
                        // Puntuación base: aprobados ~65-88, reprobados ~30-55
                        $score = $passes ? rand(65, 88) : rand(30, 55);
                        DB::table('nota')->insertOrIgnore([
                            'id_examen'    => $eId,
                            'id_admision'  => $admisionId,
                            'calificacion' => $score,
                            'estado'       => 'registrada',
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    }
                }
            }
        }

        // Recalcular cupo_disponible en base a admitidos reales
        foreach (array_values($carreras) as $carreraId) {
            $admitidos = DB::table('admision')
                ->where('id_gestion', $gestionId)
                ->where(function ($q) use ($carreraId) {
                    $q->where(fn ($q2) => $q2->where('id_carrera1', $carreraId)->where('estado', 'admitido_carrera1'))
                      ->orWhere(fn ($q2) => $q2->where('id_carrera2', $carreraId)->where('estado', 'admitido_carrera2'));
                })->count();

            DB::table('carrera_gestion')
                ->where('id_gestion', $gestionId)
                ->where('id_carrera', $carreraId)
                ->update(['cupo_disponible' => max(0, 80 - $admitidos), 'updated_at' => now()]);
        }
    }

    /** Crea materia_grupo, asignaciones (con savepoint) y exámenes para los grupos dados. */
    private function crearEstructuraAcademica(
        array $grupoIds, array $materias, int $turnoId,
        array $docenteIds, string $fechaInicio
    ): array {
        $materiaToEsp = [
            'COMP' => 'Computacion',
            'MAT'  => 'Matematicas',
            'ING'  => 'Ingles',
            'FIS'  => 'Fisica',
        ];

        $materiaGrupoIds = [];
        $examenIds       = [];
        $spCounter       = 0;

        foreach (['A', 'B'] as $paralelo) {
            $docenteIdx = $paralelo === 'A' ? 0 : 1;

            foreach ($materias as $sigla => $materiaId) {
                // materia_grupo
                $mgId = DB::table('materia_grupo')
                    ->where('id_grupo', $grupoIds[$paralelo])
                    ->where('id_materia', $materiaId)
                    ->value('id');

                if (! $mgId) {
                    $mgId = DB::table('materia_grupo')->insertGetId([
                        'id_grupo'   => $grupoIds[$paralelo],
                        'id_materia' => $materiaId,
                        'id_turno'   => $turnoId,
                        'estado'     => 'activo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $materiaGrupoIds[$paralelo][$sigla] = $mgId;

                // asignacion_academica — use savepoint so a trigger/schema error
                // does not abort the outer transaction (PostgreSQL requirement)
                if ($this->canAssign) {
                    $esp  = $materiaToEsp[$sigla] ?? 'Computacion';
                    $dId  = $docenteIds[$esp][$docenteIdx] ?? null;
                    if ($dId) {
                        $spName = 'sp_asig_' . (++$spCounter);
                        try {
                            DB::statement("SAVEPOINT {$spName}");
                            $existe = DB::table('asignacion_academica')
                                ->where('id_docente', $dId)
                                ->where('id_materia_grupo', $mgId)
                                ->exists();
                            if (! $existe) {
                                DB::table('asignacion_academica')->insert([
                                    'id_docente'       => $dId,
                                    'id_materia_grupo' => $mgId,
                                    'carga_horaria'    => 4.0,
                                    'fecha_asignacion' => $fechaInicio,
                                    'estado'           => 'activo',
                                    'created_at'       => now(),
                                    'updated_at'       => now(),
                                ]);
                            }
                            DB::statement("RELEASE SAVEPOINT {$spName}");
                        } catch (\Exception $e) {
                            DB::statement("ROLLBACK TO SAVEPOINT {$spName}");
                        }
                    }
                }

                // Exámenes
                foreach ([['parcial1', 30, 1], ['parcial2', 30, 2], ['final', 40, 3]] as [$tipo, $pMax, $mes]) {
                    $eId = DB::table('examen')
                        ->where('id_materia_grupo', $mgId)
                        ->where('tipo', $tipo)
                        ->value('id');

                    if (! $eId) {
                        $eId = DB::table('examen')->insertGetId([
                            'id_materia_grupo' => $mgId,
                            'tipo'             => $tipo,
                            'puntaje_maximo'   => $pMax,
                            'fecha'            => date('Y-m-d', strtotime($fechaInicio . ' +' . $mes . ' months')),
                            'estado'           => 'realizado',
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ]);
                    }
                    $examenIds[$paralelo][$sigla][$tipo] = $eId;
                }
            }
        }

        return [$materiaGrupoIds, $examenIds];
    }

    /** Completa gestión 1-2025 con los estudiantes ya importados. */
    private function completarGestion1_2025(array $carreras, array $materias, int $turnoId, array $docenteIds): void
    {
        $gestion = DB::table('gestion')->where('estado', 'activo')->first();
        if (! $gestion) {
            return;
        }
        $gestionId   = $gestion->id;
        $carreraList = array_values($carreras);

        // Carrera_gestion
        foreach ($carreraList as $carreraId) {
            DB::table('carrera_gestion')->insertOrIgnore([
                'id_carrera'      => $carreraId,
                'id_gestion'      => $gestionId,
                'cupo_maximo'     => 80,
                'cupo_disponible' => 80,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // Grupos A y B
        $grupoIds = [];
        foreach (['A', 'B'] as $paralelo) {
            $gId = DB::table('grupo')
                ->where('id_gestion', $gestionId)
                ->where('paralelo', $paralelo)
                ->value('id');
            if (! $gId) {
                $gId = DB::table('grupo')->insertGetId([
                    'id_gestion'  => $gestionId,
                    'nombre'      => 'Grupo ' . $paralelo,
                    'paralelo'    => $paralelo,
                    'modalidad'   => 'presencial',
                    'cupo_maximo' => 75,
                    'estado'      => 'activo',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
            $grupoIds[$paralelo] = $gId;
        }

        [$materiaGrupoIds, $examenIds] = $this->crearEstructuraAcademica(
            $grupoIds, $materias, $turnoId, $docenteIds, $gestion->fecha_inicio
        );

        // Estudiantes existentes sin admisión en esta gestión
        $existentes = DB::table('estudiante as e')
            ->whereNotExists(function ($q) use ($gestionId) {
                $q->select(DB::raw(1))
                    ->from('admision')
                    ->whereColumn('admision.id_estudiante', 'e.id')
                    ->where('admision.id_gestion', $gestionId);
            })
            ->limit(140)
            ->pluck('e.id');

        foreach ($existentes as $idx => $estId) {
            $paralelo = ($idx % 2 === 0) ? 'A' : 'B';
            $passes   = ($idx % 10) < 7; // 70% pass rate

            $c1 = $carreraList[$idx % 4];
            $c2 = $carreraList[($idx + 1) % 4];
            if ($c1 === $c2) {
                $c2 = $carreraList[($idx + 2) % 4];
            }

            $admisionId = DB::table('admision')->insertGetId([
                'id_estudiante'  => $estId,
                'id_gestion'     => $gestionId,
                'id_grupo'       => $grupoIds[$paralelo],
                'id_carrera1'    => $c1,
                'id_carrera2'    => $c2,
                'fecha'          => $gestion->fecha_inicio,
                'estado'         => 'cursando',
                'promedio_final' => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Solo parcial1 registrado (gestión en curso)
            foreach (array_keys($materias) as $sigla) {
                $eId = $examenIds[$paralelo][$sigla]['parcial1'] ?? null;
                if (! $eId) {
                    continue;
                }
                $score = $passes ? rand(62, 90) : rand(25, 55);
                DB::table('nota')->insertOrIgnore([
                    'id_examen'    => $eId,
                    'id_admision'  => $admisionId,
                    'calificacion' => $score,
                    'estado'       => 'registrada',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        // Resetear cupos disponibles de la gestión activa
        DB::table('carrera_gestion')
            ->where('id_gestion', $gestionId)
            ->update(['cupo_disponible' => 80, 'updated_at' => now()]);
    }
};
