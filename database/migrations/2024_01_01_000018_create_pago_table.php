<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_admision')->unique()->constrained('admision');
            $table->decimal('monto', 10, 2);
            $table->string('tipo_pasarela');
            $table->string('referencia_transaccion')->unique();
            $table->string('estado_pago');
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE pago ADD CONSTRAINT chk_pago_monto CHECK (monto > 0)');
        DB::statement("ALTER TABLE pago ADD CONSTRAINT chk_pago_pasarela CHECK (tipo_pasarela IN ('stripe','paypal','otro'))");
        DB::statement("ALTER TABLE pago ADD CONSTRAINT chk_pago_estado CHECK (estado_pago IN ('pendiente','completado','fallido','reembolsado'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('pago');
    }
};
