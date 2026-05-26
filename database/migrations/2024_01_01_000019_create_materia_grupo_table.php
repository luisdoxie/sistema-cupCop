<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materia_grupo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_grupo')->constrained('grupo');
            $table->foreignId('id_materia')->constrained('materia');
            $table->foreignId('id_turno')->constrained('turno');
            $table->string('estado')->default('activo');
            $table->timestamps();
            $table->unique(['id_grupo', 'id_materia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materia_grupo');
    }
};
