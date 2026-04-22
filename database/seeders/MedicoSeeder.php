<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('medicos')->insertOrIgnore([
            [
                'id_medico'            => 1,
                'id_usuario'           => 2,
                'codigo_medico'        => 'MED-0001',
                'nombres'              => 'Carlos',
                'apellidos'            => 'Mendoza Pérez',
                'ci'                   => 'V-12345678',
                'telefono'             => '0414-1111111',
                'email'                => 'c.mendoza@clinica.com',
                'matricula_profesional'=> 'MP-001234',
                'estado'               => 'activo',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'id_medico'            => 2,
                'id_usuario'           => 3,
                'codigo_medico'        => 'MED-0002',
                'nombres'              => 'Laura',
                'apellidos'            => 'Rodríguez Silva',
                'ci'                   => 'V-23456789',
                'telefono'             => '0414-2222222',
                'email'                => 'l.rodriguez@clinica.com',
                'matricula_profesional'=> 'MP-005678',
                'estado'               => 'activo',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ]);

        $esp = DB::table('especialidades')->whereIn('nombre_especialidad', ['Medicina General', 'Cardiología', 'Pediatría'])->pluck('id_especialidad', 'nombre_especialidad');

        DB::table('medico_especialidad')->insertOrIgnore([
            ['id_medico' => 1, 'id_especialidad' => $esp['Medicina General'] ?? 1],
            ['id_medico' => 1, 'id_especialidad' => $esp['Cardiología'] ?? 2],
            ['id_medico' => 2, 'id_especialidad' => $esp['Pediatría'] ?? 3],
        ]);

        DB::table('horarios_medicos')->insertOrIgnore([
            ['id_medico' => 1, 'dia_semana' => 1, 'hora_inicio' => '08:00:00', 'hora_fin' => '12:00:00', 'duracion_cita_minutos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_medico' => 1, 'dia_semana' => 2, 'hora_inicio' => '08:00:00', 'hora_fin' => '12:00:00', 'duracion_cita_minutos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_medico' => 1, 'dia_semana' => 3, 'hora_inicio' => '14:00:00', 'hora_fin' => '18:00:00', 'duracion_cita_minutos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_medico' => 1, 'dia_semana' => 4, 'hora_inicio' => '08:00:00', 'hora_fin' => '12:00:00', 'duracion_cita_minutos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_medico' => 1, 'dia_semana' => 5, 'hora_inicio' => '08:00:00', 'hora_fin' => '12:00:00', 'duracion_cita_minutos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_medico' => 2, 'dia_semana' => 1, 'hora_inicio' => '13:00:00', 'hora_fin' => '17:00:00', 'duracion_cita_minutos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_medico' => 2, 'dia_semana' => 3, 'hora_inicio' => '13:00:00', 'hora_fin' => '17:00:00', 'duracion_cita_minutos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_medico' => 2, 'dia_semana' => 5, 'hora_inicio' => '08:00:00', 'hora_fin' => '12:00:00', 'duracion_cita_minutos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
