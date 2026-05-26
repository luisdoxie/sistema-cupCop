<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_examen')->constrained('examen');
            $table->foreignId('id_admision')->constrained('admision');
            $table->decimal('calificacion', 5, 2);
            $table->string('estado');
            $table->timestamps();
            $table->unique(['id_examen', 'id_admision']);
        });

        DB::statement('ALTER TABLE nota ADD CONSTRAINT chk_nota_calificacion CHECK (calificacion BETWEEN 0 AND 100)');
        DB::statement("ALTER TABLE nota ADD CONSTRAINT chk_nota_estado CHECK (estado IN ('registrada','revisada','anulada'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('nota');
    }
};
