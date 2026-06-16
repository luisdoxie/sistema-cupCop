<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('postulacion_docente', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('sexo', 1);
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('grado_academico')->nullable();
            $table->integer('anios_experiencia')->default(4);
            $table->boolean('diplomado_educacion')->default(false);
            $table->string('cv_path')->nullable();
            $table->string('estado')->default('pendiente');
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->foreign('aprobado_por')->references('id')->on('persona');
            $table->timestamp('aprobado_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulacion_docente');
    }
};
