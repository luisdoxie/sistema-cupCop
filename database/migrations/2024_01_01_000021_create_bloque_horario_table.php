<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bloque_horario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->constrained('asignacion_academica');
            $table->string('dia');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE bloque_horario ADD CONSTRAINT chk_bloque_dia CHECK (dia IN ('lunes','martes','miercoles','jueves','viernes','sabado'))");
        DB::statement('ALTER TABLE bloque_horario ADD CONSTRAINT chk_bloque_horas CHECK (hora_fin > hora_inicio)');
    }

    public function down(): void
    {
        Schema::dropIfExists('bloque_horario');
    }
};
