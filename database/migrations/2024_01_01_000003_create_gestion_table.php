<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gestion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->integer('anio');
            $table->integer('semestre');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE gestion ADD CONSTRAINT chk_gestion_semestre CHECK (semestre IN (1, 2))");
        DB::statement("ALTER TABLE gestion ADD CONSTRAINT chk_gestion_estado CHECK (estado IN ('activo', 'cerrado', 'planificado'))");
        DB::statement("ALTER TABLE gestion ADD CONSTRAINT chk_gestion_fechas CHECK (fecha_fin > fecha_inicio)");
    }

    public function down(): void
    {
        Schema::dropIfExists('gestion');
    }
};
