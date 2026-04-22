<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_medicos', function (Blueprint $table) {
            $table->bigIncrements('id_horario');
            $table->unsignedBigInteger('id_medico');
            $table->tinyInteger('dia_semana')->comment('1=Lunes, 2=Martes, ... 7=Domingo');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('duracion_cita_minutos')->default(30);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('id_medico', 'fk_horarios_medico')
                  ->references('id_medico')->on('medicos')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->index('id_medico', 'idx_horarios_medico');
            $table->index(['id_medico', 'dia_semana'], 'idx_horarios_medico_dia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_medicos');
    }
};
