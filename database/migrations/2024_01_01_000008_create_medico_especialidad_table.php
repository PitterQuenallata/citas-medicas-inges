<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medico_especialidad', function (Blueprint $table) {
            $table->unsignedBigInteger('id_medico');
            $table->unsignedBigInteger('id_especialidad');

            $table->primary(['id_medico', 'id_especialidad']);

            $table->foreign('id_medico', 'fk_me_medico')
                  ->references('id_medico')->on('medicos')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_especialidad', 'fk_me_especialidad')
                  ->references('id_especialidad')->on('especialidades')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->index('id_medico', 'idx_me_medico');
            $table->index('id_especialidad', 'idx_me_especialidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico_especialidad');
    }
};
