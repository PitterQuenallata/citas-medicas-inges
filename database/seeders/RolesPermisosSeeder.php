<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permiso;
use App\Models\User;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            ['nombre_permiso' => 'acceso_dashboard',       'descripcion' => 'Ver dashboard',                              'modulo' => 'dashboard'],
            ['nombre_permiso' => 'acceso_citas',           'descripcion' => 'Gestionar citas y agenda',                   'modulo' => 'citas'],
            ['nombre_permiso' => 'acceso_medicos',         'descripcion' => 'Gestionar medicos, especialidades, horarios','modulo' => 'medicos'],
            ['nombre_permiso' => 'acceso_pacientes',       'descripcion' => 'Gestionar pacientes e historial',            'modulo' => 'pacientes'],
            ['nombre_permiso' => 'acceso_usuarios',        'descripcion' => 'Gestionar usuarios, roles, permisos',        'modulo' => 'usuarios'],
            ['nombre_permiso' => 'acceso_reportes',        'descripcion' => 'Ver reportes',                               'modulo' => 'reportes'],
            ['nombre_permiso' => 'acceso_auditoria',       'descripcion' => 'Ver auditoria',                              'modulo' => 'auditoria'],
            ['nombre_permiso' => 'acceso_notificaciones',  'descripcion' => 'Gestionar notificaciones',                   'modulo' => 'notificaciones'],
            ['nombre_permiso' => 'acceso_pagos',           'descripcion' => 'Gestionar pagos',                            'modulo' => 'pagos'],
        ];

        foreach ($permisos as $p) {
            Permiso::firstOrCreate(['nombre_permiso' => $p['nombre_permiso']], $p);
        }

        $todosIds = Permiso::pluck('id_permiso')->toArray();

        // Administrador - todos los permisos
        $admin = Rol::firstOrCreate(
            ['nombre_rol' => 'Administrador'],
            ['descripcion' => 'Acceso total al sistema', 'estado' => 'activo']
        );
        $admin->permisos()->sync($todosIds);

        // Recepcionista - dashboard, citas, pacientes, pagos
        $recep = Rol::firstOrCreate(
            ['nombre_rol' => 'Recepcionista'],
            ['descripcion' => 'Gestion de citas y pacientes', 'estado' => 'activo']
        );
        $recepPermisos = Permiso::whereIn('modulo', ['dashboard', 'citas', 'pacientes', 'pagos'])->pluck('id_permiso')->toArray();
        $recep->permisos()->sync($recepPermisos);

        // Medico - dashboard, citas, pacientes
        $medico = Rol::firstOrCreate(
            ['nombre_rol' => 'Medico'],
            ['descripcion' => 'Acceso a agenda y pacientes', 'estado' => 'activo']
        );
        $medicoPermisos = Permiso::whereIn('modulo', ['dashboard', 'citas', 'pacientes'])->pluck('id_permiso')->toArray();
        $medico->permisos()->sync($medicoPermisos);

        // Asignar rol Administrador al primer usuario (Mallku)
        $primerUsuario = User::first();
        if ($primerUsuario) {
            $primerUsuario->roles()->syncWithoutDetaching([$admin->id_rol]);
        }
    }
}
