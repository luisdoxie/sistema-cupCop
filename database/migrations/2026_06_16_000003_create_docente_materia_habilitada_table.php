<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('docente_materia_habilitada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_docente')->constrained('docente');
            $table->foreignId('id_materia')->constrained('materia');
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->foreign('aprobado_por')->references('id')->on('persona');
            $table->timestamps();
            $table->unique(['id_docente', 'id_materia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docente_materia_habilitada');
    }
};
