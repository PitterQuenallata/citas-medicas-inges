<?php

namespace App\Providers;

use App\Http\View\Composers\SidebarComposer;
use Illuminate\Pagination\Paginator;
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
        Paginator::useTailwind();

        View::composer(
            ['components.app-partials.main-sidebar', 'components.app-partials.header', 'components.app-partials.sidebar-panel'],
            SidebarComposer::class
        );
    }
}