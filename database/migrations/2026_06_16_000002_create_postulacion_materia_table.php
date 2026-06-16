<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('postulacion_materia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_postulacion')->constrained('postulacion_docente')->cascadeOnDelete();
            $table->foreignId('id_materia')->constrained('materia');
            $table->timestamps();
            $table->unique(['id_postulacion', 'id_materia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulacion_materia');
    }
};
