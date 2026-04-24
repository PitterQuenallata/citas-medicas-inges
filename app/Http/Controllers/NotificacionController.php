<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        return view('notificaciones.index');
    }
}
