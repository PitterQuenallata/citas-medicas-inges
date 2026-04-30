<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pacientes')->insertOrIgnore([
            [
                'id_paciente'       => 1,
                'id_usuario'        => null,
                'codigo_paciente'   => 'PAC-0001',
                'nombres'           => 'Juan',
                'apellidos'         => 'García López',
                'fecha_nacimiento'  => '1985-03-15',
                'sexo'              => 'masculino',
                'ci'                => '15234567',
                'telefono'          => '67033711',
                'email'             => 'juan.garcia@email.com',
                'grupo_sanguineo'   => 'O+',
                'estado'            => 'activo',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'id_paciente'       => 2,
                'id_usuario'        => null,
                'codigo_paciente'   => 'PAC-0002',
                'nombres'           => 'María',
                'apellidos'         => 'Torres Vidal',
                'fecha_nacimiento'  => '1992-07-20',
                'sexo'              => 'femenino',
                'ci'                => '24567890',
                'telefono'          => '67033711',
                'email'             => 'maria.torres@email.com',
                'grupo_sanguineo'   => 'A+',
                'estado'            => 'activo',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'id_paciente'       => 3,
                'id_usuario'        => null,
                'codigo_paciente'   => 'PAC-0003',
                'nombres'           => 'Pedro',
                'apellidos'         => 'Jiménez Castro',
                'fecha_nacimiento'  => '1970-11-08',
                'sexo'              => 'masculino',
                'ci'                => '08876543',
                'telefono'          => '67033711',
                'email'             => 'pedro.jimenez@email.com',
                'grupo_sanguineo'   => 'B-',
                'estado'            => 'activo',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        ]);
    }
}
