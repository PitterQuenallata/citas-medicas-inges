<?php

namespace App\Providers;

use App\Http\View\Composers\HeaderComposer;
use App\Http\View\Composers\SidebarComposer;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer(
            ['components.app-partials.main-sidebar', 'components.app-partials.header', 'components.app-partials.sidebar-panel'],
            SidebarComposer::class
        );

        View::composer(
            'components.app-partials.header',
            HeaderComposer::class
        );

        $this->registrarGates();
    }

    private function registrarGates(): void
    {
        $permisos = [
            'acceso_dashboard',
            'acceso_citas',
            'acceso_medicos',
            'acceso_pacientes',
            'acceso_usuarios',
            'acceso_reportes',
            'acceso_auditoria',
            'acceso_notificaciones',
            'acceso_pagos',
        ];

        foreach ($permisos as $permiso) {
            Gate::define($permiso, function (User $user) use ($permiso) {
                return $user->tienePermiso($permiso);
            });
        }
    }
}