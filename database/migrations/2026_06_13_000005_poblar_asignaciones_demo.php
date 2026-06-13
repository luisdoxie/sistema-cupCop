<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
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

        // Mapa CI -> especialidad
        $specs = [
            '50000001' => 'Computacion', '50000002' => 'Computacion',
            '50000003' => 'Matematicas', '50000004' => 'Matematicas',
            '50000005' => 'Ingles',      '50000006' => 'Ingles',
            '50000007' => 'Fisica',      '50000008' => 'Fisica',
        ];

        $siglaToEsp = ['COMP' => 'Computacion', 'MAT' => 'Matematicas', 'ING' => 'Ingles', 'FIS' => 'Fisica'];

        // Construir mapa especialidad => [docente_id_A, docente_id_B]
        $docenteIds = [];
        foreach ($specs as $ci => $esp) {
            $pId = DB::table('persona')->where('ci', $ci)->value('id');
            if (! $pId) continue;
            $dId = DB::table('docente')->where('id_persona', $pId)->value('id');
            if (! $dId) continue;
            $docenteIds[$esp][] = $dId;
        }

        // Obtener todos los materia_grupo de los grupos demo (paralelo A/B)
        $materiaGrupos = DB::table('materia_grupo as mg')
            ->join('grupo as g',    'g.id',  '=', 'mg.id_grupo')
            ->join('materia as m',  'm.id',  '=', 'mg.id_materia')
            ->join('gestion as ge', 'ge.id', '=', 'g.id_gestion')
            ->select('mg.id', 'g.paralelo', 'm.sigla', 'ge.fecha_inicio')
            ->whereIn('m.sigla', ['COMP', 'MAT', 'ING', 'FIS'])
            ->whereNotNull('g.paralelo')
            ->get();

        // Desactivar trigger para evitar errores de compilación lazy de PostgreSQL
        DB::statement('ALTER TABLE asignacion_academica DISABLE TRIGGER trg_validar_max_grupos_docente');

        try {
            foreach ($materiaGrupos as $mg) {
                $esp = $siglaToEsp[$mg->sigla] ?? null;
                if (! $esp) continue;

                $docenteIdx = $mg->paralelo === 'A' ? 0 : 1;
                $dId        = $docenteIds[$esp][$docenteIdx] ?? null;
                if (! $dId) continue;

                $existe = DB::table('asignacion_academica')
                    ->where('id_docente', $dId)
                    ->where('id_materia_grupo', $mg->id)
                    ->exists();

                if (! $existe) {
                    DB::table('asignacion_academica')->insert([
                        'id_docente'       => $dId,
                        'id_materia_grupo' => $mg->id,
                        'carga_horaria'    => 4.0,
                        'fecha_asignacion' => $mg->fecha_inicio,
                        'estado'           => 'activo',
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }
        } finally {
            DB::statement('ALTER TABLE asignacion_academica ENABLE TRIGGER trg_validar_max_grupos_docente');
        }
    }

    public function down(): void {}
};
