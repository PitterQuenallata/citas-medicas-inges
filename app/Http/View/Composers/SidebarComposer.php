<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

class SidebarComposer
{
    private static function menu(): array
    {
        return [
            'title' => 'Menu',
            'items' => [
                [
                    ['title' => 'Dashboard',  'route_name' => 'dashboard'],
                    ['title' => 'Citas',      'route_name' => 'citas.index'],
                    ['title' => 'Medicos',    'route_name' => 'medicos.index'],
                    ['title' => 'Pacientes',  'route_name' => 'pacientes.index'],
                ],
            ],
        ];
    }

    public function compose(View $view)
    {
        $pageName = optional(request()->route())->getName() ?? '';

        $view->with('sidebarMenu', self::menu());
        $view->with('pageName', $pageName);
        $view->with('allSidebarItems', []);
        $view->with('routePrefix', '');
    }
}
