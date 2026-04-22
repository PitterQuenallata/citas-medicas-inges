<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->bigIncrements('id_medico');
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->string('codigo_medico', 30)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('ci', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('matricula_profesional', 50)->unique();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();

            $table->foreign('id_usuario', 'fk_medicos_usuario')
                  ->references('id')->on('users')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->index('id_usuario', 'idx_medicos_usuario');
            $table->index('estado', 'idx_medicos_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};
