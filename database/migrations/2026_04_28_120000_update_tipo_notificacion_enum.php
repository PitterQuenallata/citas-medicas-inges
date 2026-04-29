<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE notificaciones MODIFY COLUMN tipo_notificacion ENUM('recordatorio','confirmacion','cancelacion','reserva','reprogramacion') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE notificaciones MODIFY COLUMN tipo_notificacion ENUM('recordatorio','confirmacion','cancelacion') NOT NULL");
    }
};
