<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clase_programada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->constrained('asignacion_academica');
            $table->foreignId('id_aula')->constrained('aula');
            $table->foreignId('id_bloque')->constrained('bloque_horario');
            $table->date('fecha');
            $table->string('estado');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE clase_programada ADD CONSTRAINT chk_clase_estado CHECK (estado IN ('programada','realizada','cancelada'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('clase_programada');
    }
};
