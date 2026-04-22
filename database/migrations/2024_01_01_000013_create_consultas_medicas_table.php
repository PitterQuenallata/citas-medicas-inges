<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas_medicas', function (Blueprint $table) {
            $table->bigIncrements('id_consulta');
            $table->unsignedBigInteger('id_cita')->unique();
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_medico');
            $table->dateTime('fecha_consulta')->useCurrent();
            $table->text('motivo_consulta')->nullable();
            $table->text('sintomas')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento')->nullable();
            $table->text('receta')->nullable();
            $table->text('observaciones_medicas')->nullable();
            $table->decimal('peso', 5, 2)->nullable()->comment('Peso en kilogramos');
            $table->decimal('talla', 5, 2)->nullable()->comment('Talla en centímetros');
            $table->string('presion_arterial', 20)->nullable()->comment('Formato: sistólica/diastólica');
            $table->decimal('temperatura', 4, 2)->nullable()->comment('Temperatura en grados Celsius');
            $table->timestamps();

            $table->foreign('id_cita', 'fk_consultas_cita')
                  ->references('id_cita')->on('citas')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_paciente', 'fk_consultas_paciente')
                  ->references('id_paciente')->on('pacientes')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('id_medico', 'fk_consultas_medico')
                  ->references('id_medico')->on('medicos')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->index('id_cita', 'idx_consultas_cita');
            $table->index('id_paciente', 'idx_consultas_paciente');
            $table->index('id_medico', 'idx_consultas_medico');
            $table->index(['id_paciente', 'fecha_consulta'], 'idx_consultas_pac_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas_medicas');
    }
};
