<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bloqueos_medicos', function (Blueprint $table) {
            $table->bigIncrements('id_bloqueo');
            $table->unsignedBigInteger('id_medico');
            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('motivo', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_medico', 'fk_bloqueos_medico')
                  ->references('id_medico')->on('medicos')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->index('id_medico', 'idx_bloqueos_medico');
            $table->index(['id_medico', 'fecha'], 'idx_bloqueos_medico_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloqueos_medicos');
    }
};
