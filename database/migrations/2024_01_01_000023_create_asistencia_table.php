<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asistencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_clase')->constrained('clase_programada');
            $table->foreignId('id_admision')->constrained('admision');
            $table->string('estado');
            $table->date('fecha');
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->unique(['id_clase', 'id_admision']);
        });

        DB::statement("ALTER TABLE asistencia ADD CONSTRAINT chk_asistencia_estado CHECK (estado IN ('presente','ausente','justificado'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencia');
    }
};
