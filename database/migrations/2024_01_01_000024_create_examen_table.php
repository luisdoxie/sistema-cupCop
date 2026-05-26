<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('examen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_materia_grupo')->constrained('materia_grupo');
            $table->string('tipo');
            $table->integer('puntaje_maximo');
            $table->date('fecha')->nullable();
            $table->string('estado');
            $table->timestamps();
            $table->unique(['id_materia_grupo', 'tipo']);
        });

        DB::statement("ALTER TABLE examen ADD CONSTRAINT chk_examen_tipo CHECK (tipo IN ('parcial1','parcial2','final'))");
        DB::statement('ALTER TABLE examen ADD CONSTRAINT chk_examen_puntaje CHECK (puntaje_maximo IN (30, 40))');
        DB::statement("ALTER TABLE examen ADD CONSTRAINT chk_examen_estado CHECK (estado IN ('programado','realizado','anulado'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('examen');
    }
};
