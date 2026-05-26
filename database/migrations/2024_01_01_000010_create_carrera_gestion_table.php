<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carrera_gestion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_carrera')->constrained('carrera');
            $table->foreignId('id_gestion')->constrained('gestion');
            $table->integer('cupo_maximo');
            $table->integer('cupo_disponible');
            $table->timestamps();
            $table->unique(['id_carrera', 'id_gestion']);
        });

        DB::statement('ALTER TABLE carrera_gestion ADD CONSTRAINT chk_cg_cupo_maximo CHECK (cupo_maximo > 0)');
        DB::statement('ALTER TABLE carrera_gestion ADD CONSTRAINT chk_cg_cupo_disponible CHECK (cupo_disponible >= 0)');
        DB::statement('ALTER TABLE carrera_gestion ADD CONSTRAINT chk_cg_cupo_relacion CHECK (cupo_disponible <= cupo_maximo)');
    }

    public function down(): void
    {
        Schema::dropIfExists('carrera_gestion');
    }
};
