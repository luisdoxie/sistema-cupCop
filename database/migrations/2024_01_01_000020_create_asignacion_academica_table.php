<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asignacion_academica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_docente')->constrained('docente');
            $table->foreignId('id_materia_grupo')->constrained('materia_grupo');
            $table->decimal('carga_horaria', 5, 2)->nullable();
            $table->date('fecha_asignacion')->nullable();
            $table->string('estado')->default('activo');
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->unique(['id_docente', 'id_materia_grupo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion_academica');
    }
};
