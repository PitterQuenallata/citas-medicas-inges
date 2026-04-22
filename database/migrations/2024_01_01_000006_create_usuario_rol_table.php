<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_rol', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_rol');

            $table->primary(['user_id', 'id_rol']);

            $table->foreign('user_id', 'fk_usuario_rol_usuario')
                  ->references('id')->on('users')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_rol', 'fk_usuario_rol_rol')
                  ->references('id_rol')->on('roles')
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->index('user_id', 'idx_usuario_rol_usuario');
            $table->index('id_rol', 'idx_usuario_rol_rol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_rol');
    }
};
