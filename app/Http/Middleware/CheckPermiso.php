<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermiso
{
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        $user = $request->user();

        if (!$user || !$user->tienePermiso($permiso)) {
            abort(403, 'No tienes permiso para acceder a esta seccion.');
        }

        return $next($request);
    }
}
