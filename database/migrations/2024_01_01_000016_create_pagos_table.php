<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id_pago');
            $table->unsignedBigInteger('id_cita');
            $table->string('codigo_pago', 30)->unique();
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['qr', 'efectivo', 'transferencia']);
            $table->enum('estado_pago', ['pendiente', 'pagado', 'anulado'])->default('pendiente');
            $table->string('referencia_externa', 100)->nullable();
            $table->json('datos_remitente')->nullable();
            $table->string('comprobante_url', 255)->nullable();
            $table->dateTime('fecha_pago')->nullable();
            $table->unsignedBigInteger('id_usuario_registra');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('id_cita', 'fk_pagos_cita')
                  ->references('id_cita')->on('citas')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('id_usuario_registra', 'fk_pagos_usuario_registra')
                  ->references('id')->on('users')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->index('id_cita', 'idx_pagos_cita');
            $table->index('estado_pago', 'idx_pagos_estado');
            $table->index('metodo_pago', 'idx_pagos_metodo');
            $table->index('fecha_pago', 'idx_pagos_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
