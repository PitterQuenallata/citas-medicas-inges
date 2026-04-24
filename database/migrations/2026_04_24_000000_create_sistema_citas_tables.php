<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->bigIncrements('id_permiso');
            $table->string('nombre_permiso', 100)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->string('modulo', 50);
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id_rol');
            $table->string('nombre_rol', 50)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });

        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->unsignedBigInteger('id_rol');
            $table->unsignedBigInteger('id_permiso');

            $table->primary(['id_rol', 'id_permiso']);

            $table->foreign('id_rol')->references('id_rol')->on('roles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_permiso')->references('id_permiso')->on('permisos')->cascadeOnUpdate()->cascadeOnDelete();

            $table->index('id_rol');
            $table->index('id_permiso');
        });

        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email', 150)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('password', 255);
            $table->enum('estado', ['activo', 'inactivo', 'bloqueado'])->default('activo');
            $table->dateTime('ultimo_login')->nullable();
            $table->timestamps();

            $table->index('estado');
        });

        Schema::create('usuario_rol', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_rol');

            $table->primary(['id_usuario', 'id_rol']);

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_rol')->references('id_rol')->on('roles')->cascadeOnUpdate()->restrictOnDelete();

            $table->index('id_usuario');
            $table->index('id_rol');
        });

        Schema::create('especialidades', function (Blueprint $table) {
            $table->bigIncrements('id_especialidad');
            $table->string('nombre_especialidad', 100)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });

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

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->cascadeOnUpdate()->restrictOnDelete();

            $table->index('id_usuario');
            $table->index('estado');
        });

        Schema::create('medico_especialidad', function (Blueprint $table) {
            $table->unsignedBigInteger('id_medico');
            $table->unsignedBigInteger('id_especialidad');

            $table->primary(['id_medico', 'id_especialidad']);

            $table->foreign('id_medico')->references('id_medico')->on('medicos')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_especialidad')->references('id_especialidad')->on('especialidades')->cascadeOnUpdate()->restrictOnDelete();

            $table->index('id_medico');
            $table->index('id_especialidad');
        });

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

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->cascadeOnUpdate()->nullOnDelete();

            $table->index('id_usuario');
            $table->index('estado');
            $table->index(['apellidos', 'nombres']);
        });

        Schema::create('horarios_medicos', function (Blueprint $table) {
            $table->bigIncrements('id_horario');
            $table->unsignedBigInteger('id_medico');
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('duracion_cita_minutos')->default(30);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('id_medico')->references('id_medico')->on('medicos')->cascadeOnUpdate()->restrictOnDelete();

            $table->index('id_medico');
            $table->index(['id_medico', 'dia_semana']);
        });

        Schema::create('bloqueos_medicos', function (Blueprint $table) {
            $table->bigIncrements('id_bloqueo');
            $table->unsignedBigInteger('id_medico');
            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('motivo', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_medico')->references('id_medico')->on('medicos')->cascadeOnUpdate()->cascadeOnDelete();

            $table->index('id_medico');
            $table->index(['id_medico', 'fecha']);
        });

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
            $table->enum('estado_cita', ['pendiente', 'confirmada', 'atendida', 'cancelada', 'reprogramada', 'no_asistio'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->string('motivo_cancelacion', 255)->nullable();
            $table->unsignedBigInteger('id_cita_reprogramada_desde')->nullable();
            $table->timestamps();

            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_medico')->references('id_medico')->on('medicos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_usuario_registra')->references('id_usuario')->on('usuarios')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_cita_reprogramada_desde')->references('id_cita')->on('citas')->cascadeOnUpdate()->nullOnDelete();

            $table->index('id_paciente');
            $table->index('id_medico');
            $table->index('id_usuario_registra');
            $table->index('id_cita_reprogramada_desde');
            $table->index(['id_medico', 'fecha_cita']);
            $table->index('estado_cita');
            $table->index(['id_paciente', 'fecha_cita']);
        });

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
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            $table->string('presion_arterial', 20)->nullable();
            $table->decimal('temperatura', 4, 2)->nullable();
            $table->timestamps();

            $table->foreign('id_cita')->references('id_cita')->on('citas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_medico')->references('id_medico')->on('medicos')->cascadeOnUpdate()->restrictOnDelete();

            $table->index('id_cita');
            $table->index('id_paciente');
            $table->index('id_medico');
            $table->index(['id_paciente', 'fecha_consulta']);
        });

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

            $table->foreign('id_cita')->references('id_cita')->on('citas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->cascadeOnUpdate()->cascadeOnDelete();

            $table->index('id_cita');
            $table->index('id_paciente');
            $table->index(['estado_envio', 'canal']);
            $table->index('fecha_envio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('consultas_medicas');
        Schema::dropIfExists('citas');
        Schema::dropIfExists('bloqueos_medicos');
        Schema::dropIfExists('horarios_medicos');
        Schema::dropIfExists('pacientes');
        Schema::dropIfExists('medico_especialidad');
        Schema::dropIfExists('medicos');
        Schema::dropIfExists('especialidades');
        Schema::dropIfExists('usuario_rol');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('rol_permiso');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permisos');
    }
};
