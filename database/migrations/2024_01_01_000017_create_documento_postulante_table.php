<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documento_postulante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_admision')->constrained('admision');
            $table->string('tipo_documento');
            $table->string('ruta_archivo');
            $table->string('estado_verificacion');
            $table->text('observacion')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->timestamps();
            $table->unique(['id_admision', 'tipo_documento']);
        });

        DB::statement("ALTER TABLE documento_postulante ADD CONSTRAINT chk_doc_tipo CHECK (tipo_documento IN ('certificado_nacimiento','fotocopia_carnet','libreta_colegio','titulo_bachiller','otro'))");
        DB::statement("ALTER TABLE documento_postulante ADD CONSTRAINT chk_doc_estado CHECK (estado_verificacion IN ('pendiente','verificado','rechazado'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_postulante');
    }
};
