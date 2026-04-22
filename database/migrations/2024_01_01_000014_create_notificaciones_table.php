<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->bigIncrements('id_notificacion');
            $table->unsignedBigInteger('id_cita');
            $table->unsignedBigInteger('id_paciente');
            $table->enum('tipo_notificacion', ['recordatorio', 'confirmacion', 'cancelacion']);
            $table->enum('canal', ['email', 'sms', 'whatsapp', 'sistema']);
            $table->text('mensaje');
            $table->dateTime('fecha_envio');
            $table->enum('estado_envio', ['pendiente', 'enviado', 'fallido'])->default('pendiente');
            $table->timestamps();

            $table->foreign('id_cita', 'fk_notificaciones_cita')
                  ->references('id_cita')->on('citas')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_paciente', 'fk_notificaciones_paciente')
                  ->references('id_paciente')->on('pacientes')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->index('id_cita', 'idx_notif_cita');
            $table->index('id_paciente', 'idx_notif_paciente');
            $table->index(['estado_envio', 'canal'], 'idx_notif_estado_canal');
            $table->index('fecha_envio', 'idx_notif_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
