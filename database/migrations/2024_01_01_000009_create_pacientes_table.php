<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->bigIncrements('id_paciente');
            $table->unsignedBigInteger('id_usuario')->nullable()->unique();
            $table->string('codigo_paciente', 30)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['masculino', 'femenino', 'otro'])->nullable();
            $table->string('ci', 20)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('grupo_sanguineo', 10)->nullable();
            $table->string('contacto_emergencia_nombre', 150)->nullable();
            $table->string('contacto_emergencia_telefono', 20)->nullable();
            $table->text('alergias')->nullable();
            $table->text('observaciones_generales')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();

            $table->foreign('id_usuario', 'fk_pacientes_usuario')
                  ->references('id_usuario')->on('usuarios')
                  ->onDelete('set null')->onUpdate('cascade');

            $table->index('id_usuario', 'idx_pacientes_usuario');
            $table->index('estado', 'idx_pacientes_estado');
            $table->index(['apellidos', 'nombres'], 'idx_pacientes_nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
