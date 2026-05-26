<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admision', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_estudiante')->constrained('estudiante');
            $table->foreignId('id_gestion')->constrained('gestion');
            $table->foreignId('id_grupo')->nullable()->constrained('grupo');
            $table->unsignedBigInteger('id_carrera1');
            $table->unsignedBigInteger('id_carrera2');
            $table->date('fecha')->useCurrent();
            $table->string('estado');
            $table->decimal('promedio_final', 5, 2)->nullable();
            $table->timestamps();
            $table->unique(['id_estudiante', 'id_gestion']);
            $table->foreign('id_carrera1')->references('id')->on('carrera');
            $table->foreign('id_carrera2')->references('id')->on('carrera');
        });

        DB::statement("ALTER TABLE admision ADD CONSTRAINT chk_admision_estado CHECK (estado IN ('inscrito','documentos_pendientes','pago_pendiente','cursando','aprobado','admitido_carrera1','admitido_carrera2','no_admitido','reprobado'))");
        DB::statement('ALTER TABLE admision ADD CONSTRAINT chk_admision_carreras CHECK (id_carrera1 != id_carrera2)');
    }

    public function down(): void
    {
        Schema::dropIfExists('admision');
    }
};
