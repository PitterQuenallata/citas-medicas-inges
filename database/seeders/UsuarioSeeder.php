<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            [
                'nombre'     => 'Mallku',
                'apellido'   => 'Admin',
                'email'      => 'mallku@gmail.com',
                'password'   => Hash::make('sopademani1'),
                'estado'     => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre'     => 'Recepcionista',
                'apellido'   => 'Demo',
                'email'      => 'recepcion@clinica.com',
                'password'   => Hash::make('sopademani1'),
                'estado'     => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre'     => 'Carlos',
                'apellido'   => 'Mendoza',
                'email'      => 'c.mendoza@clinica.com',
                'password'   => Hash::make('sopademani1'),
                'estado'     => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
