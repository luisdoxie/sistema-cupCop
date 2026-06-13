<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        // Solo ejecutar si no hay aulas todavía
        if (DB::table('aula')->count() > 0) {
            return;
        }

        // ── 1. Pisos ──────────────────────────────────────────────────────────
        $pisoIds = [];
        foreach ([1, 2, 3] as $num) {
            $id = DB::table('piso')->where('numero', $num)->value('id');
            if (! $id) {
                $id = DB::table('piso')->insertGetId([
                    'numero'     => $num,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $pisoIds[$num] = $id;
        }

        // ── 2. Aulas ──────────────────────────────────────────────────────────
        // Numeración 15-30 distribuida en 3 pisos, capacidad uniforme de 60
        // [piso, numero]
        // [piso, numero, capacidad, tipo]
        $aulaConfig = [
            [1, '15', 60, 'regular'],
            [1, '16', 60, 'regular'],
            [1, '17', 60, 'regular'],
            [1, '18', 60, 'regular'],
            [1, '19', 60, 'regular'],
            [1, '20', 60, 'regular'],
            [2, '21', 60, 'regular'],
            [2, '22', 60, 'regular'],
            [2, '23', 60, 'regular'],
            [2, '24', 60, 'regular'],
            [2, '25', 60, 'regular'],
            [2, '26', 60, 'regular'],
            [3, '27', 60, 'regular'],
            [3, '28', 60, 'regular'],
            [3, '29', 60, 'regular'],
            [3, '30', 60, 'regular'],
            [3, '42', 30, 'laboratorio'],
            [3, '43', 30, 'laboratorio'],
        ];

        $aulaIds = [];
        foreach ($aulaConfig as [$pNum, $numero, $cap, $tipo]) {
            $aulaIds[] = DB::table('aula')->insertGetId([
                'id_piso'    => $pisoIds[$pNum],
                'numero'     => $numero,
                'capacidad'  => $cap,
                'tipo'       => $tipo,
                'modalidad'  => 'presencial',
                'estado'     => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── 3. Obtener asignaciones de docentes demo ───────────────────────────
        $demoCIs = ['50000001','50000002','50000003','50000004',
                    '50000005','50000006','50000007','50000008'];

        $personaIds = DB::table('persona')->whereIn('ci', $demoCIs)->pluck('id');
        $docenteIds = DB::table('docente')->whereIn('id_persona', $personaIds)->pluck('id');

        if ($docenteIds->isEmpty()) {
            return;
        }

        $asignaciones = DB::table('asignacion_academica as aa')
            ->join('materia_grupo as mg', 'mg.id',  '=', 'aa.id_materia_grupo')
            ->join('grupo as g',          'g.id',   '=', 'mg.id_grupo')
            ->join('materia as m',        'm.id',   '=', 'mg.id_materia')
            ->join('gestion as ge',       'ge.id',  '=', 'g.id_gestion')
            ->select(
                'aa.id as asignacion_id',
                'g.paralelo',
                'm.sigla',
                'ge.fecha_inicio',
                'ge.fecha_fin',
                'ge.estado as gestion_estado'
            )
            ->whereIn('aa.id_docente', $docenteIds)
            ->whereNotNull('g.paralelo')
            ->whereIn('m.sigla', ['COMP', 'MAT', 'ING', 'FIS'])
            ->get();

        if ($asignaciones->isEmpty()) {
            return;
        }

        // ── 4. Horario fijo por materia+paralelo ──────────────────────────────
        // Diseñado para que ningún docente tenga cruce consigo mismo dentro
        // de una gestión (cada docente enseña solo 1 materia a 1 paralelo).
        // El trigger de cruces se desactiva para permitir reusar el mismo
        // horario en gestiones distintas (semestres que no se solapan).
        $horario = [
            'COMP_A' => ['dia' => 'lunes',     'inicio' => '07:00', 'fin' => '10:00', 'aulaIdx' => 0],
            'COMP_B' => ['dia' => 'jueves',    'inicio' => '14:00', 'fin' => '17:00', 'aulaIdx' => 4],
            'MAT_A'  => ['dia' => 'martes',    'inicio' => '07:00', 'fin' => '10:00', 'aulaIdx' => 1],
            'MAT_B'  => ['dia' => 'viernes',   'inicio' => '14:00', 'fin' => '17:00', 'aulaIdx' => 5],
            'ING_A'  => ['dia' => 'miercoles', 'inicio' => '07:00', 'fin' => '10:00', 'aulaIdx' => 2],
            'ING_B'  => ['dia' => 'lunes',     'inicio' => '14:00', 'fin' => '17:00', 'aulaIdx' => 6],
            'FIS_A'  => ['dia' => 'jueves',    'inicio' => '07:00', 'fin' => '10:00', 'aulaIdx' => 3],
            'FIS_B'  => ['dia' => 'martes',    'inicio' => '14:00', 'fin' => '17:00', 'aulaIdx' => 7],
        ];

        $diaDow = [
            'lunes' => 1, 'martes' => 2, 'miercoles' => 3,
            'jueves' => 4, 'viernes' => 5, 'sabado' => 6,
        ];

        // Desactivar trigger de cruce de horarios (gestiones distintas no se solapan
        // en la realidad, pero el trigger no distingue fechas de gestión)
        DB::statement('ALTER TABLE bloque_horario DISABLE TRIGGER trg_validar_cruce_horario');

        try {
            $hoy = new \DateTime('today');

            foreach ($asignaciones as $asig) {
                $key  = $asig->sigla . '_' . $asig->paralelo;
                $slot = $horario[$key] ?? null;
                if (! $slot) {
                    continue;
                }

                // Bloque horario
                $bloqueId = DB::table('bloque_horario')->insertGetId([
                    'id_asignacion' => $asig->asignacion_id,
                    'dia'           => $slot['dia'],
                    'hora_inicio'   => $slot['inicio'],
                    'hora_fin'      => $slot['fin'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                $aulaId      = $aulaIds[$slot['aulaIdx'] % count($aulaIds)];
                $fechaInicio = new \DateTime($asig->fecha_inicio);
                $fechaFin    = new \DateTime($asig->fecha_fin);
                $esCerrada   = $asig->gestion_estado === 'cerrado';

                // Avanzar al primer día de la semana correspondiente
                $dow = $diaDow[$slot['dia']];
                while ((int) $fechaInicio->format('N') !== $dow) {
                    $fechaInicio->modify('+1 day');
                }

                // Generar hasta 16 clases semanales
                $current = clone $fechaInicio;
                $count   = 0;
                while ($current <= $fechaFin && $count < 16) {
                    $estado = ($esCerrada || $current < $hoy) ? 'realizada' : 'programada';

                    DB::table('clase_programada')->insert([
                        'id_asignacion' => $asig->asignacion_id,
                        'id_aula'       => $aulaId,
                        'id_bloque'     => $bloqueId,
                        'fecha'         => $current->format('Y-m-d'),
                        'estado'        => $estado,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);

                    $current->modify('+1 week');
                    $count++;
                }
            }
        } finally {
            DB::statement('ALTER TABLE bloque_horario ENABLE TRIGGER trg_validar_cruce_horario');
        }
    }

    public function down(): void {}
};
