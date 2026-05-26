<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('importacion_lote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_admin')->constrained('persona');
            $table->foreignId('id_gestion')->nullable()->constrained('gestion');
            $table->string('tipo_usuario');
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->integer('total_registros')->default(0);
            $table->integer('exitosos')->default(0);
            $table->integer('fallidos')->default(0);
            $table->text('errores')->nullable();
            $table->string('estado');
            $table->timestamp('fecha_subida');
            $table->timestamp('fecha_proceso')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE importacion_lote ADD CONSTRAINT chk_importacion_tipo CHECK (tipo_usuario IN ('docente', 'coordinador', 'administrador', 'estudiante'))");
        DB::statement("ALTER TABLE importacion_lote ADD CONSTRAINT chk_importacion_estado CHECK (estado IN ('pendiente', 'procesando', 'completado', 'con_errores'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('importacion_lote');
    }
};
