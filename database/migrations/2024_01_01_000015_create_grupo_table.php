<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grupo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_gestion')->constrained('gestion');
            $table->string('nombre');
            $table->string('paralelo');
            $table->string('modalidad');
            $table->integer('cupo_maximo')->default(70);
            $table->string('estado')->default('activo');
            $table->timestamps();
            $table->unique(['id_gestion', 'nombre', 'paralelo']);
        });

        DB::statement("ALTER TABLE grupo ADD CONSTRAINT chk_grupo_modalidad CHECK (modalidad IN ('presencial', 'virtual'))");
        DB::statement('ALTER TABLE grupo ADD CONSTRAINT chk_grupo_cupo CHECK (cupo_maximo BETWEEN 1 AND 80)');
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo');
    }
};
