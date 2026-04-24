<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

class SidebarComposer
{
    private static function seccionCitas(): array
    {
        return [
            'title' => 'Citas',
            'items' => [[
                ['title' => 'Lista de Citas',    'route_name' => 'citas.index'],
                ['title' => 'Nueva Cita',        'route_name' => 'citas.create'],
                ['title' => 'Agenda Medica',     'route_name' => 'agenda'],
            ]],
        ];
    }

    private static function seccionMedicos(): array
    {
        return [
            'title' => 'Medicos',
            'items' => [[
                ['title' => 'Lista de Medicos',  'route_name' => 'medicos.index'],
                ['title' => 'Nuevo Medico',      'route_name' => 'medicos.create'],
                ['title' => 'Especialidades',    'route_name' => 'especialidades.index'],
                ['title' => 'Horarios Medicos',  'route_name' => 'horarios.index'],
            ]],
        ];
    }

    private static function seccionPacientes(): array
    {
        return [
            'title' => 'Pacientes',
            'items' => [[
                ['title' => 'Lista de Pacientes','route_name' => 'pacientes.index'],
                ['title' => 'Nuevo Paciente',    'route_name' => 'pacientes.create'],
                ['title' => 'Historial Clinico', 'route_name' => 'historial.index'],
            ]],
        ];
    }

    private static function seccionAdmin(): array
    {
        return [
            'title' => 'Administracion',
            'items' => [[
                ['title' => 'Usuarios',          'route_name' => 'usuarios.index'],
                ['title' => 'Roles',             'route_name' => 'roles.index'],
                ['title' => 'Permisos',          'route_name' => 'permisos.index'],
            ]],
        ];
    }

    private static function seccionNotificaciones(): array
    {
        return [
            'title' => 'Notificaciones',
            'items' => [[
                ['title' => 'Historial de Envios', 'route_name' => 'notificaciones.index'],
            ]],
        ];
    }

    private static function seccionSistema(): array
    {
        return [
            'title' => 'Sistema',
            'items' => [[
                ['title' => 'Reportes',          'route_name' => 'reportes.index'],
                ['title' => 'Auditoria',         'route_name' => 'auditoria.index'],
            ]],
        ];
    }

    private static function seccionDefault(): array
    {
        return ['title' => 'Menu', 'items' => [[]]];
    }

    public function compose(View $view)
    {
        $pageName = optional(request()->route())->getName() ?? '';

        $menu = match(true) {
            str_starts_with($pageName, 'citas') || $pageName === 'agenda'
                => self::seccionCitas(),
            str_starts_with($pageName, 'medicos') || str_starts_with($pageName, 'especialidades') || str_starts_with($pageName, 'horarios')
                => self::seccionMedicos(),
            str_starts_with($pageName, 'pacientes') || str_starts_with($pageName, 'historial')
                => self::seccionPacientes(),
            str_starts_with($pageName, 'usuarios') || str_starts_with($pageName, 'roles') || str_starts_with($pageName, 'permisos')
                => self::seccionAdmin(),
            str_starts_with($pageName, 'notificaciones')
                => self::seccionNotificaciones(),
            str_starts_with($pageName, 'reportes') || str_starts_with($pageName, 'auditoria')
                => self::seccionSistema(),
            default => self::seccionDefault(),
        };

        $view->with('sidebarMenu', $menu);
        $view->with('pageName', $pageName);
        $view->with('allSidebarItems', []);
        $view->with('routePrefix', '');
    }
}
