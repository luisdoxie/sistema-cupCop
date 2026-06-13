<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Sin transacción: el ENABLE TRIGGER del finally debe ejecutarse siempre,
    // incluso si un INSERT falla — con transacción envolvente de Laravel,
    // el error aborta la tx y el ENABLE TRIGGER también falla (SQLSTATE 25P02).
    public $withinTransaction = false;

    public function up(): void
    {
        // Solo ejecutar si existen los docentes demo
        $pId = DB::table('persona')->where('ci', '50000001')->value('id');
        if (! $pId) {
            return;
        }

        $dId1 = DB::table('docente')->where('id_persona', $pId)->value('id');
        if (! $dId1) {
            return;
        }

        // Si ya tiene asignaciones, no volver a correr
        if (DB::table('asignacion_academica')->where('id_docente', $dId1)->exists()) {
            return;
        }

        $specs = [
            '50000001' => 'Computacion', '50000002' => 'Computacion',
            '50000003' => 'Matematicas', '50000004' => 'Matematicas',
            '50000005' => 'Ingles',      '50000006' => 'Ingles',
            '50000007' => 'Fisica',      '50000008' => 'Fisica',
        ];

        $siglaToEsp = ['COMP' => 'Computacion', 'MAT' => 'Matematicas', 'ING' => 'Ingles', 'FIS' => 'Fisica'];

        $docenteIds = [];
        foreach ($specs as $ci => $esp) {
            $pid = DB::table('persona')->where('ci', $ci)->value('id');
            if (! $pid) continue;
            $did = DB::table('docente')->where('id_persona', $pid)->value('id');
            if (! $did) continue;
            $docenteIds[$esp][] = $did;
        }

        $materiaGrupos = DB::table('materia_grupo as mg')
            ->join('grupo as g',    'g.id',  '=', 'mg.id_grupo')
            ->join('materia as m',  'm.id',  '=', 'mg.id_materia')
            ->join('gestion as ge', 'ge.id', '=', 'g.id_gestion')
            ->select('mg.id', 'g.paralelo', 'm.sigla', 'ge.fecha_inicio')
            ->whereIn('m.sigla', ['COMP', 'MAT', 'ING', 'FIS'])
            ->whereNotNull('g.paralelo')
            ->get();

        DB::statement('ALTER TABLE asignacion_academica DISABLE TRIGGER trg_validar_max_grupos_docente');

        try {
            foreach ($materiaGrupos as $mg) {
                $esp = $siglaToEsp[$mg->sigla] ?? null;
                if (! $esp) continue;

                $docenteIdx = $mg->paralelo === 'A' ? 0 : 1;
                $did        = $docenteIds[$esp][$docenteIdx] ?? null;
                if (! $did) continue;

                // Usar SQL directo para evitar problemas con plan cache de prepared statements
                $existe = DB::selectOne(
                    'SELECT 1 FROM asignacion_academica WHERE id_docente = ? AND id_materia_grupo = ?',
                    [$did, $mg->id]
                );

                if (! $existe) {
                    DB::statement(
                        'INSERT INTO asignacion_academica (id_docente, id_materia_grupo, carga_horaria, fecha_asignacion, estado, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
                        [$did, $mg->id, 4.0, $mg->fecha_inicio, 'activo', now(), now()]
                    );
                }
            }
        } finally {
            DB::statement('ALTER TABLE asignacion_academica ENABLE TRIGGER trg_validar_max_grupos_docente');
        }
    }

    public function down(): void {}
};
