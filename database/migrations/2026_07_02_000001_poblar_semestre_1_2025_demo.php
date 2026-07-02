<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        $gestion = DB::table('gestion')->where('nombre', 'Semestre 1-2025')->first();
        if (! $gestion) {
            return;
        }

        // Si ya tiene admisiones, no volver a correr
        if (DB::table('admision')->where('id_gestion', $gestion->id)->exists()) {
            return;
        }

        $carreras  = DB::table('carrera')->pluck('id', 'sigla')->toArray();
        $materias  = DB::table('materia')->pluck('id', 'sigla')->toArray();
        $turnoId   = DB::table('turno')->value('id');

        $docenteIds = $this->obtenerDocentes();

        // Grupos A y B
        $grupoIds = [];
        foreach (['A', 'B'] as $paralelo) {
            $gId = DB::table('grupo')
                ->where('id_gestion', $gestion->id)
                ->where('paralelo', $paralelo)
                ->value('id');
            if (! $gId) {
                $gId = DB::table('grupo')->insertGetId([
                    'id_gestion'  => $gestion->id,
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

        // carrera_gestion
        foreach (array_values($carreras) as $carreraId) {
            DB::table('carrera_gestion')->insertOrIgnore([
                'id_carrera'      => $carreraId,
                'id_gestion'      => $gestion->id,
                'cupo_maximo'     => 80,
                'cupo_disponible' => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // materia_grupo, asignaciones y exámenes
        $examenIds = $this->crearEstructuraAcademica($grupoIds, $materias, $turnoId, $docenteIds, $gestion->fecha_inicio);

        // 300 estudiantes ficticios, prefijo CI '59'
        $carreraList = array_values($carreras);
        $numPorGrupo = 150;
        $passRate    = 65;

        $nombres_m = ['Juan', 'Luis', 'Carlos', 'Pedro', 'Miguel', 'Diego', 'Marco', 'Pablo', 'Oscar', 'Raul', 'Victor', 'Ruben'];
        $nombres_f = ['Maria', 'Ana', 'Laura', 'Sofia', 'Diana', 'Rosa', 'Claudia', 'Elena', 'Isabel', 'Carla', 'Fanny', 'Wendy'];
        $apellidos  = ['Garcia', 'Lopez', 'Martinez', 'Rodriguez', 'Gomez', 'Perez', 'Sanchez', 'Torres', 'Ramirez', 'Flores', 'Condori', 'Mamani', 'Quispe', 'Torrez', 'Copa'];

        foreach (['A', 'B'] as $pIdx => $paralelo) {
            $grupoPass = $paralelo === 'A' ? min(95, $passRate + 10) : max(30, $passRate - 10);

            for ($i = 1; $i <= $numPorGrupo; $i++) {
                $ci = '59' . str_pad($pIdx * $numPorGrupo + $i, 5, '0', STR_PAD_LEFT);

                if (DB::table('persona')->where('ci', $ci)->exists()) {
                    continue;
                }

                $sexo     = ($i % 3 === 0) ? 'F' : 'M';
                $nombre   = $sexo === 'M' ? $nombres_m[$i % count($nombres_m)] : $nombres_f[$i % count($nombres_f)];
                $apellido = $apellidos[$i % count($apellidos)];
                $passes   = (($i % 100) < $grupoPass);

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
                    'id_gestion'     => $gestion->id,
                    'id_grupo'       => $grupoIds[$paralelo],
                    'id_carrera1'    => $c1,
                    'id_carrera2'    => $c2,
                    'fecha'          => $gestion->fecha_inicio,
                    'estado'         => $estado,
                    'promedio_final' => $promedio,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // Notas: 3 exámenes × 4 materias
                foreach (array_keys($materias) as $sigla) {
                    foreach (['parcial1', 'parcial2', 'final'] as $tipo) {
                        $eId = $examenIds[$paralelo][$sigla][$tipo] ?? null;
                        if (! $eId) continue;
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
    }

    public function down(): void {}

    private function obtenerDocentes(): array
    {
        $specs = [
            '50000001' => 'Computacion', '50000002' => 'Computacion',
            '50000003' => 'Matematicas', '50000004' => 'Matematicas',
            '50000005' => 'Ingles',      '50000006' => 'Ingles',
            '50000007' => 'Fisica',      '50000008' => 'Fisica',
        ];

        $ids = [];
        foreach ($specs as $ci => $esp) {
            $pid = DB::table('persona')->where('ci', $ci)->value('id');
            if (! $pid) continue;
            $did = DB::table('docente')->where('id_persona', $pid)->value('id');
            if (! $did) continue;
            $ids[$esp][] = $did;
        }
        return $ids;
    }

    private function crearEstructuraAcademica(array $grupoIds, array $materias, int $turnoId, array $docenteIds, string $fechaInicio): array
    {
        $materiaToEsp = [
            'COMP' => 'Computacion',
            'MAT'  => 'Matematicas',
            'ING'  => 'Ingles',
            'FIS'  => 'Fisica',
        ];

        $examenIds = [];

        DB::statement('ALTER TABLE asignacion_academica DISABLE TRIGGER USER');

        try {
        foreach (['A', 'B'] as $paralelo) {
            $docenteIdx = $paralelo === 'A' ? 0 : 1;

            foreach ($materias as $sigla => $materiaId) {
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

                // Asignación del docente
                $esp = $materiaToEsp[$sigla] ?? 'Computacion';
                $dId = $docenteIds[$esp][$docenteIdx] ?? null;
                if ($dId && ! DB::table('asignacion_academica')->where('id_docente', $dId)->where('id_materia_grupo', $mgId)->exists()) {
                    DB::table('asignacion_academica')->insert([
                        'id_docente'       => $dId,
                        'id_materia_grupo' => $mgId,
                        'carga_horaria'    => 4.0,
                        'fecha_asignacion' => $fechaInicio,
                        'estado'           => 'inactivo',
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
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
        } finally {
            DB::statement('ALTER TABLE asignacion_academica ENABLE TRIGGER USER');
        }

        return $examenIds;
    }
};
