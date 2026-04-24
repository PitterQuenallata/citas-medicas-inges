<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Usuario;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $rolAdmin = Rol::query()->firstOrCreate(
            ['nombre_rol' => 'Administrador'],
            ['descripcion' => 'Acceso completo', 'estado' => 'activo'],
        );

        $admin = Usuario::query()->firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'telefono' => null,
                'password' => Hash::make('admin123'),
                'estado' => 'activo',
                'ultimo_login' => null,
            ],
        );

        DB::table('usuario_rol')->updateOrInsert(
            ['id_usuario' => $admin->id_usuario, 'id_rol' => $rolAdmin->id_rol],
            [],
        );
    }
}
