<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    // -------------------------------------------------------------------------
    // INDEX — Log con filtros y paginación
    // -------------------------------------------------------------------------
    public function index(Request $request)
    {
        $query = Auditoria::with('usuario')
            ->orderBy('created_at', 'desc');

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }
        if ($request->filled('id_usuario')) {
            $query->where('id_usuario', $request->id_usuario);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('tabla')) {
            $query->where('tabla', 'like', '%' . $request->tabla . '%');
        }

        $registros = $query->paginate(25)->withQueryString();

        // Para los selects de filtro
        $usuarios       = User::orderBy('nombre')->get(['id', 'nombre', 'apellido']);
        $accionesUnicas = Auditoria::select('accion')->distinct()->pluck('accion')->sort()->values();
        $tablasUnicas   = Auditoria::select('tabla')->distinct()->pluck('tabla')->sort()->values();

        // Resumen
        $totalRegistros  = Auditoria::count();
        $accionesPorTipo = Auditoria::select('accion', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('accion')->pluck('total', 'accion');

        return view('auditoria.index', compact(
            'registros', 'usuarios', 'accionesUnicas', 'tablasUnicas',
            'totalRegistros', 'accionesPorTipo'
        ));
    }

    // -------------------------------------------------------------------------
    // EXPORTAR PDF — log filtrado
    // -------------------------------------------------------------------------
    public function exportarPdf(Request $request)
    {
        $query = Auditoria::with('usuario')->orderBy('created_at', 'desc');

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }
        if ($request->filled('id_usuario')) {
            $query->where('id_usuario', $request->id_usuario);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('tabla')) {
            $query->where('tabla', 'like', '%' . $request->tabla . '%');
        }

        $registros   = $query->limit(500)->get(); // límite para PDF
        $generadoEn  = now()->format('d/m/Y H:i');
        $filtros     = $request->only(['fecha_desde', 'fecha_hasta', 'id_usuario', 'accion', 'tabla']);

        $pdf = Pdf::loadView('pdf.auditoria-log', compact('registros', 'generadoEn', 'filtros'))
            ->setPaper('a4', 'landscape');

        $filename = 'auditoria-log-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->download($filename);
    }
}
