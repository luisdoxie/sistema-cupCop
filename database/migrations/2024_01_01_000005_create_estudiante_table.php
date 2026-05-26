<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('estudiante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_persona')->constrained('persona');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('colegio_procedencia', 200)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->boolean('titulo_bachiller')->default(false);
            $table->text('otros_documentos')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiante');
    }
};
