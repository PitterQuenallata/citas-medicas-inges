<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            ['nombre_especialidad' => 'Medicina General',  'descripcion' => 'Atención médica general y preventiva', 'estado' => 'activo'],
            ['nombre_especialidad' => 'Cardiología',        'descripcion' => 'Enfermedades del corazón y sistema cardiovascular', 'estado' => 'activo'],
            ['nombre_especialidad' => 'Pediatría',          'descripcion' => 'Atención médica a niños y adolescentes', 'estado' => 'activo'],
            ['nombre_especialidad' => 'Ginecología',        'descripcion' => 'Salud femenina y obstetricia', 'estado' => 'activo'],
            ['nombre_especialidad' => 'Traumatología',      'descripcion' => 'Lesiones y enfermedades del sistema musculoesquelético', 'estado' => 'activo'],
        ];

        foreach ($especialidades as $esp) {
            DB::table('especialidades')->insertOrIgnore(array_merge($esp, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
