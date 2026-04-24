<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->bigIncrements('id_cita');
            $table->string('codigo_cita', 30)->unique();
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_medico');
            $table->unsignedBigInteger('id_usuario_registra');
            $table->date('fecha_cita');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->text('motivo_consulta')->nullable();
            $table->enum('estado_cita', [
                'pendiente',
                'confirmada',
                'atendida',
                'cancelada',
                'reprogramada',
                'no_asistio',
            ])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->string('motivo_cancelacion', 255)->nullable();
            $table->unsignedBigInteger('id_cita_reprogramada_desde')->nullable();
            $table->timestamps();

            $table->foreign('id_paciente', 'fk_citas_paciente')
                  ->references('id_paciente')->on('pacientes')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('id_medico', 'fk_citas_medico')
                  ->references('id_medico')->on('medicos')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('id_usuario_registra', 'fk_citas_usuario_registra')
                  ->references('id_usuario')->on('usuarios')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('id_cita_reprogramada_desde', 'fk_citas_reprogramada_desde')
                  ->references('id_cita')->on('citas')
                  ->onDelete('set null')->onUpdate('cascade');

            $table->index('id_paciente', 'idx_citas_paciente');
            $table->index('id_medico', 'idx_citas_medico');
            $table->index('id_usuario_registra', 'idx_citas_usuario_registra');
            $table->index('id_cita_reprogramada_desde', 'idx_citas_reprogramada_desde');
            $table->index(['id_medico', 'fecha_cita'], 'idx_citas_medico_fecha');
            $table->index('estado_cita', 'idx_citas_estado');
            $table->index(['id_paciente', 'fecha_cita'], 'idx_citas_paciente_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
