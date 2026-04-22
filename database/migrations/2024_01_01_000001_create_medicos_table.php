<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración compatible con BD compartida.
 * Usa CREATE TABLE IF NOT EXISTS mediante Schema::hasTable().
 * NO modifica tablas de otros módulos (usuarios, pacientes, etc.).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── TABLA: especialidades ──────────────────────────────────────────────
        // Catálogo previo requerido por medicos. Creamos solo si no existe.
        if (!Schema::hasTable('especialidades')) {
            Schema::create('especialidades', function (Blueprint $table) {
                $table->bigIncrements('id_especialidad');
                $table->string('nombre_especialidad', 100)->unique();
                $table->string('descripcion', 255)->nullable();
                $table->enum('estado', ['activo', 'inactivo'])->default('activo');
                $table->timestamps();
            });
        }

        // ─── TABLA: medicos ─────────────────────────────────────────────────────
        // Perfil profesional del médico. Relación 1:1 con usuarios.
        if (!Schema::hasTable('medicos')) {
            Schema::create('medicos', function (Blueprint $table) {
                $table->bigIncrements('id_medico');

                // FK a usuarios — relación 1:1 obligatoria
                $table->unsignedBigInteger('id_usuario')->unique();
                $table->foreign('id_usuario')
                      ->references('id_usuario')
                      ->on('usuarios')
                      ->onDelete('restrict')
                      ->onUpdate('cascade');

                $table->string('codigo_medico', 30)->unique();
                $table->string('nombres', 100);
                $table->string('apellidos', 100);
                $table->string('ci', 20)->nullable();
                $table->string('telefono', 20)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('matricula_profesional', 50)->unique();
                $table->enum('estado', ['activo', 'inactivo'])->default('activo');
                $table->timestamps();

                // Índices para búsquedas frecuentes
                $table->index('estado', 'idx_medicos_estado');
            });
        }

        // ─── TABLA: medico_especialidad (N:M) ───────────────────────────────────
        if (!Schema::hasTable('medico_especialidad')) {
            Schema::create('medico_especialidad', function (Blueprint $table) {
                $table->unsignedBigInteger('id_medico');
                $table->unsignedBigInteger('id_especialidad');

                $table->primary(['id_medico', 'id_especialidad'], 'pk_medico_especialidad');

                $table->foreign('id_medico')
                      ->references('id_medico')
                      ->on('medicos')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');

                $table->foreign('id_especialidad')
                      ->references('id_especialidad')
                      ->on('especialidades')
                      ->onDelete('restrict')
                      ->onUpdate('cascade');
            });
        }

        // ─── TABLA: horarios_medicos ────────────────────────────────────────────
        // Disponibilidad semanal. Se usa en lugar de columna JSON en medicos.
        if (!Schema::hasTable('horarios_medicos')) {
            Schema::create('horarios_medicos', function (Blueprint $table) {
                $table->bigIncrements('id_horario');

                $table->unsignedBigInteger('id_medico');
                $table->foreign('id_medico')
                      ->references('id_medico')
                      ->on('medicos')
                      ->onDelete('restrict')
                      ->onUpdate('cascade');

                // 1=Lunes, 2=Martes, ..., 7=Domingo
                $table->tinyInteger('dia_semana')->unsigned()
                      ->comment('1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes, 6=Sábado, 7=Domingo');
                $table->time('hora_inicio');
                $table->time('hora_fin');

                // Duración del slot en minutos (entre 5 y 240)
                $table->integer('duracion_cita_minutos')->default(30);
                $table->boolean('activo')->default(true);
                $table->timestamps();

                // Índice compuesto para validar disponibilidad: médico + día
                $table->index(['id_medico', 'dia_semana'], 'idx_horarios_medico_dia');
            });
        }

        // ─── TABLA: bloqueos_medicos ────────────────────────────────────────────
        if (!Schema::hasTable('bloqueos_medicos')) {
            Schema::create('bloqueos_medicos', function (Blueprint $table) {
                $table->bigIncrements('id_bloqueo');
                $table->unsignedBigInteger('id_medico');
                $table->foreign('id_medico')
                      ->references('id_medico')
                      ->on('medicos')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');

                $table->date('fecha');
                $table->time('hora_inicio')->nullable();
                $table->time('hora_fin')->nullable();
                $table->string('motivo', 255)->nullable();
                $table->timestamps();

                $table->index(['id_medico', 'fecha'], 'idx_bloqueos_medico_fecha');
            });
        }
    }

    public function down(): void
    {
        // El orden inverso respeta las FK
        Schema::dropIfExists('bloqueos_medicos');
        Schema::dropIfExists('horarios_medicos');
        Schema::dropIfExists('medico_especialidad');
        Schema::dropIfExists('medicos');
        Schema::dropIfExists('especialidades');
    }
};
