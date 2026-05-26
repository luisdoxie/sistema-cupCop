<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('config_sistema', function (Blueprint $table) {
            $table->string('clave', 50)->primary();
            $table->string('valor', 200);
            $table->string('descripcion', 300)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config_sistema');
    }
};
