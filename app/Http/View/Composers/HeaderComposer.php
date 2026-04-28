<?php

namespace App\Http\View\Composers;

use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HeaderComposer
{
    public function compose(View $view): void
    {
        if (!Auth::check()) {
            $view->with('headerNotificaciones', collect());
            $view->with('headerNotifCount', 0);
            return;
        }

        $notificaciones = Notificacion::with(['paciente', 'cita'])
            ->where('canal', 'whatsapp')
            ->latest('fecha_envio')
            ->take(5)
            ->get();

        $view->with('headerNotificaciones', $notificaciones);
        $view->with('headerNotifCount', $notificaciones->count());
    }
}
