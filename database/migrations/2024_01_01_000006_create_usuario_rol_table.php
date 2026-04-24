<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_rol', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_rol');

            $table->primary(['id_usuario', 'id_rol']);

            $table->foreign('id_usuario', 'fk_usuario_rol_usuario')
                  ->references('id_usuario')->on('usuarios')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_rol', 'fk_usuario_rol_rol')
                  ->references('id_rol')->on('roles')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->index('id_usuario', 'idx_usuario_rol_usuario');
            $table->index('id_rol', 'idx_usuario_rol_rol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_rol');
    }
};
