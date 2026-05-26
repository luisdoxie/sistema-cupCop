<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('docente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_persona')->constrained('persona');
            $table->string('especialidad')->nullable();
            $table->string('grado_academico')->nullable();
            $table->boolean('diplomado_educacion')->default(false);
            $table->integer('anios_experiencia');
            $table->integer('max_grupos')->default(4);
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE docente ADD CONSTRAINT chk_docente_anios CHECK (anios_experiencia >= 4)');
        DB::statement('ALTER TABLE docente ADD CONSTRAINT chk_docente_max_grupos CHECK (max_grupos BETWEEN 1 AND 5)');
    }

    public function down(): void
    {
        Schema::dropIfExists('docente');
    }
};
