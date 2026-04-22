<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder de prueba para el módulo Médicos.
 * SOLO inserta datos si las tablas están vacías (idempotente).
 * NO modifica pacientes, usuarios ni otras tablas del equipo.
 *
 * Uso:
 *   php artisan db:seed --class=MedicoSeeder
 */
class MedicoSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Especialidades ────────────────────────────────────────────────
        // Solo insertamos si el catálogo está vacío para no duplicar.
        if (DB::table('especialidades')->count() === 0) {
            DB::table('especialidades')->insert([
                ['nombre_especialidad' => 'Medicina General',   'descripcion' => 'Atención primaria general',           'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
                ['nombre_especialidad' => 'Cardiología',        'descripcion' => 'Enfermedades del corazón',            'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
                ['nombre_especialidad' => 'Pediatría',          'descripcion' => 'Atención médica infantil',            'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
                ['nombre_especialidad' => 'Neurología',         'descripcion' => 'Sistema nervioso',                    'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
                ['nombre_especialidad' => 'Traumatología',      'descripcion' => 'Huesos, músculos y articulaciones',   'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ]);
            $this->command->info('✓ Especialidades insertadas');
        } else {
            $this->command->warn('⚠ Especialidades ya existen — omitiendo inserción');
        }

        // ─── 2. Médicos de prueba ─────────────────────────────────────────────
        // Requiere que existan usuarios con id 1 y 2 (módulo de Walter).
        // Si no existen, se omite silenciosamente.
        $usuariosExistentes = DB::table('usuarios')
                                ->whereIn('id_usuario', [1, 2])
                                ->pluck('id_usuario')
                                ->toArray();

        if (empty($usuariosExistentes)) {
            $this->command->warn('⚠ No se encontraron usuarios con id 1 o 2. Corre el seeder de usuarios primero (Walter).');
            return;
        }

        foreach ($usuariosExistentes as $idUsuario) {
            // No duplicar si ya tiene médico asignado
            if (DB::table('medicos')->where('id_usuario', $idUsuario)->exists()) {
                $this->command->warn("⚠ El usuario {$idUsuario} ya tiene médico asignado — omitiendo");
                continue;
            }

            $num       = str_pad($idUsuario, 4, '0', STR_PAD_LEFT);
            $idMedico  = DB::table('medicos')->insertGetId([
                'id_usuario'            => $idUsuario,
                'codigo_medico'         => "MED-{$num}",
                'nombres'               => "Nombre{$idUsuario}",
                'apellidos'             => "Apellido{$idUsuario}",
                'ci'                    => "1234567{$idUsuario}",
                'telefono'              => "7999000{$idUsuario}",
                'email'                 => "medico{$idUsuario}@clinica.com",
                'matricula_profesional' => "MAT-{$num}",
                'estado'                => 'activo',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            $this->command->info("✓ Médico MED-{$num} creado (id_medico={$idMedico})");

            // ── Especialidad (Medicina General = id 1) ────────────────────────
            $espId = DB::table('especialidades')->where('nombre_especialidad', 'Medicina General')->value('id_especialidad');
            if ($espId) {
                DB::table('medico_especialidad')->insertOrIgnore([
                    'id_medico'       => $idMedico,
                    'id_especialidad' => $espId,
                ]);
            }

            // ── Horarios de prueba: Lun-Vie 08:00-12:00 ──────────────────────
            for ($dia = 1; $dia <= 5; $dia++) {
                DB::table('horarios_medicos')->insert([
                    'id_medico'             => $idMedico,
                    'dia_semana'            => $dia,
                    'hora_inicio'           => '08:00:00',
                    'hora_fin'              => '12:00:00',
                    'duracion_cita_minutos' => 30,
                    'activo'                => true,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            }
            $this->command->info("  ↳ Horarios Lun-Vie 08:00-12:00 creados");
        }
    }
}
