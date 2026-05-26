<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('persona', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->char('sexo', 1);
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('correo')->unique();
            $table->string('password', 255);
            $table->string('rol');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE persona ADD CONSTRAINT chk_persona_sexo CHECK (sexo IN ('M', 'F'))");
        DB::statement("ALTER TABLE persona ADD CONSTRAINT chk_persona_rol CHECK (rol IN ('administrador', 'coordinador', 'docente', 'estudiante'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};
