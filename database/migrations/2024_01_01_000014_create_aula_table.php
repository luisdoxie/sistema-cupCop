<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_piso')->constrained('piso');
            $table->string('numero');
            $table->integer('capacidad')->nullable();
            $table->string('tipo')->nullable();
            $table->string('modalidad');
            $table->string('estado');
            $table->timestamps();
            $table->unique(['id_piso', 'numero']);
        });

        DB::statement("ALTER TABLE aula ADD CONSTRAINT chk_aula_modalidad CHECK (modalidad IN ('presencial', 'virtual'))");
        DB::statement("ALTER TABLE aula ADD CONSTRAINT chk_aula_estado CHECK (estado IN ('disponible', 'ocupada', 'mantenimiento'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('aula');
    }
};
