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
                'ci'                => 'V-15234567',
                'telefono'          => '0426-1234567',
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
                'ci'                => 'V-24567890',
                'telefono'          => '0412-9876543',
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
                'ci'                => 'V-08876543',
                'telefono'          => '0424-5554444',
                'email'             => 'pedro.jimenez@email.com',
                'grupo_sanguineo'   => 'B-',
                'estado'            => 'activo',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],

            [
                'id_paciente'       => 4,
                'id_usuario'        => null,
                'codigo_paciente'   => 'PAC-0004',
                'nombres'           => 'Josue',
                'apellidos'         => 'Gonzales Perez',
                'fecha_nacimiento'  => '1984-08-29',
                'sexo'              => 'masculino',
                'ci'                => 'V-6546546',
                'telefono'          => '0424-72650337',
                'email'             => 'jgonzales2908@gmail.com',
                'grupo_sanguineo'   => 'A+',
                'estado'            => 'activo',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
           [
                'id_paciente'       => 5,
                'id_usuario'        => null,
                'codigo_paciente'   => 'PAC-0005',
                'nombres'           => 'Ana',
                'apellidos'         => 'Gonzales Perez',
                'fecha_nacimiento'  => '1989-02-08',
                'sexo'              => 'femenino',
                'ci'                => 'V-6556556',
                'telefono'          => '0525-72650387',
                'email'             => 'aarnez@gmail.com',
                'grupo_sanguineo'   => 'A+',
                'estado'            => 'activo',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}
