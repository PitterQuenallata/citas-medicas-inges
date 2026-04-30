<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->enum('tipo_notificacion', ['recordatorio', 'confirmacion', 'cancelacion', 'reserva', 'reprogramacion'])->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->enum('tipo_notificacion', ['recordatorio', 'confirmacion', 'cancelacion'])->nullable(false)->change();
        });
    }
};
