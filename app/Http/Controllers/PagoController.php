<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Cita;
use App\Models\Pago;
use App\Services\VeriPagosService;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function __construct(protected VeriPagosService $veriPagos) {}

    // -------------------------------------------------------------------------
    // INDEX — lista paginada de pagos
    // -------------------------------------------------------------------------
    public function index(Request $request)
    {
        $query = Pago::with(['cita.paciente', 'cita.medico', 'usuarioRegistra'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado_pago', $request->estado);
        }
        if ($request->filled('metodo')) {
            $query->where('metodo_pago', $request->metodo);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_pago', 'like', "%{$buscar}%")
                  ->orWhereHas('cita', function ($q2) use ($buscar) {
                      $q2->where('codigo_cita', 'like', "%{$buscar}%");
                  })
                  ->orWhereHas('cita.paciente', function ($q2) use ($buscar) {
                      $q2->where('nombres', 'like', "%{$buscar}%")
                         ->orWhere('apellidos', 'like', "%{$buscar}%");
                  });
            });
        }

        $pagos = $query->paginate(15)->withQueryString();

        return view('pagos.index', compact('pagos'));
    }

    // -------------------------------------------------------------------------
    // SHOW — detalle de un pago
    // -------------------------------------------------------------------------
    public function show(Pago $pago)
    {
        $pago->load(['cita.paciente', 'cita.medico.especialidades', 'usuarioRegistra']);
        return view('pagos.show', compact('pago'));
    }

    // -------------------------------------------------------------------------
    // STORE — registrar pago manual (efectivo / transferencia)
    // -------------------------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'id_cita'      => ['required', 'exists:citas,id_cita'],
            'monto'        => ['required', 'numeric', 'min:0.01'],
            'metodo_pago'  => ['required', 'in:efectivo,transferencia'],
            'observaciones'=> ['nullable', 'string', 'max:500'],
        ], [
            'id_cita.required' => 'La cita es obligatoria.',
            'monto.required'   => 'El monto es obligatorio.',
            'monto.min'        => 'El monto debe ser mayor a 0.',
        ]);

        $cita = Cita::findOrFail($request->id_cita);

        // Verificar que no tenga un pago activo
        $pagoActivo = $cita->pago;
        if ($pagoActivo && $pagoActivo->estado_pago === 'pagado') {
            return back()->with('error', 'Esta cita ya tiene un pago confirmado.');
        }

        $pago = Pago::create([
            'id_cita'             => $cita->id_cita,
            'codigo_pago'         => Pago::generarCodigo(),
            'monto'               => $request->monto,
            'metodo_pago'         => $request->metodo_pago,
            'estado_pago'         => 'pagado',
            'fecha_pago'          => now(),
            'id_usuario_registra' => auth()->id(),
            'observaciones'       => $request->observaciones,
        ]);

        Auditoria::registrar('crear', 'pagos', $pago->id_pago, null, $pago->toArray());

        return redirect()->route('citas.show', $cita)
            ->with('success', 'Pago registrado exitosamente.');
    }

    // -------------------------------------------------------------------------
    // ANULAR — anular un pago existente
    // -------------------------------------------------------------------------
    public function anular(Request $request, Pago $pago)
    {
        if ($pago->estado_pago === 'anulado') {
            return back()->with('error', 'Este pago ya fue anulado.');
        }

        $request->validate([
            'motivo_anulacion' => ['required', 'string', 'max:500'],
        ], [
            'motivo_anulacion.required' => 'Debe indicar el motivo de anulación.',
        ]);

        $datosAnteriores = $pago->toArray();

        $pago->update([
            'estado_pago'  => 'anulado',
            'observaciones'=> ($pago->observaciones ? $pago->observaciones . ' | ' : '') . 'ANULADO: ' . $request->motivo_anulacion,
        ]);

        Auditoria::registrar('anular', 'pagos', $pago->id_pago, $datosAnteriores, $pago->fresh()->toArray());

        return back()->with('success', 'Pago anulado correctamente.');
    }

    // -------------------------------------------------------------------------
    // API — Generar QR VeriPagos
    // -------------------------------------------------------------------------
    public function generarQR(Request $request)
    {
        $request->validate([
            'id_cita' => ['required', 'exists:citas,id_cita'],
            'monto'   => ['required', 'numeric', 'min:0.01'],
        ]);

        $cita = Cita::findOrFail($request->id_cita);

        // Verificar que no tenga pago activo
        $pagoActivo = $cita->pago;
        if ($pagoActivo && $pagoActivo->estado_pago === 'pagado') {
            return response()->json(['success' => false, 'error' => 'Esta cita ya tiene un pago confirmado.'], 422);
        }

        // Crear pago pendiente
        $pago = Pago::create([
            'id_cita'             => $cita->id_cita,
            'codigo_pago'         => Pago::generarCodigo(),
            'monto'               => $request->monto,
            'metodo_pago'         => 'qr',
            'estado_pago'         => 'pendiente',
            'id_usuario_registra' => auth()->id(),
        ]);

        // Llamar a VeriPagos
        $resultado = $this->veriPagos->generarQR(
            (float) $request->monto,
            "Pago cita {$cita->codigo_cita}"
        );

        if (!$resultado['success']) {
            $pago->update(['estado_pago' => 'anulado', 'observaciones' => 'Error al generar QR: ' . $resultado['error']]);
            return response()->json(['success' => false, 'error' => $resultado['error']], 500);
        }

        $pago->update([
            'referencia_externa' => $resultado['movimiento_id'],
        ]);

        Auditoria::registrar('crear', 'pagos', $pago->id_pago, null, $pago->toArray());

        return response()->json([
            'success'        => true,
            'qr_url'         => $resultado['qr_url'],
            'movimiento_id'  => $resultado['movimiento_id'],
            'id_pago'        => $pago->id_pago,
            'codigo_pago'    => $pago->codigo_pago,
        ]);
    }

    // -------------------------------------------------------------------------
    // API — Verificar estado QR
    // -------------------------------------------------------------------------
    public function verificarQR(string $movimientoId)
    {
        $pago = Pago::where('referencia_externa', $movimientoId)
            ->where('estado_pago', 'pendiente')
            ->first();

        if (!$pago) {
            return response()->json(['success' => false, 'error' => 'Pago no encontrado.'], 404);
        }

        $resultado = $this->veriPagos->verificarEstado($movimientoId);

        if (!$resultado['success']) {
            return response()->json(['success' => false, 'error' => $resultado['error']], 500);
        }

        $estadoVP = strtolower($resultado['estado'] ?? '');

        if (in_array($estadoVP, ['completado', 'pagado', 'aprobado'])) {
            $datosAnteriores = $pago->toArray();

            $pago->update([
                'estado_pago'     => 'pagado',
                'fecha_pago'      => now(),
                'datos_remitente' => $resultado['remitente'] ?? null,
            ]);

            Auditoria::registrar('confirmar_pago_qr', 'pagos', $pago->id_pago, $datosAnteriores, $pago->fresh()->toArray());

            return response()->json([
                'success'   => true,
                'estado'    => 'pagado',
                'pago'      => $pago->fresh(),
            ]);
        }

        return response()->json([
            'success' => true,
            'estado'  => $estadoVP ?: 'pendiente',
        ]);
    }

    // -------------------------------------------------------------------------
    // WEBHOOK — callback de VeriPagos (opcional)
    // -------------------------------------------------------------------------
    /**
     * Webhook de VeriPagos — recibe notificación cuando un QR es pagado.
     * Formato esperado:
     * {
     *   "movimiento_id": 99,
     *   "monto": 1000,
     *   "detalle": "...",
     *   "estado": "Completado",
     *   "data": [],
     *   "remitente": { "nombre": "...", "banco": "...", "documento": "...", "cuenta": "..." }
     * }
     */
    public function webhook(Request $request)
    {
        $movimientoId = $request->input('movimiento_id');
        $estado       = $request->input('estado');

        if (!$movimientoId || !$estado) {
            return response()->json(['message' => 'Datos incompletos'], 400);
        }

        $pago = Pago::where('referencia_externa', (string) $movimientoId)
            ->where('estado_pago', 'pendiente')
            ->first();

        if (!$pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        if (strtolower($estado) === 'completado') {
            $datosAnteriores = $pago->toArray();

            $pago->update([
                'estado_pago'     => 'pagado',
                'fecha_pago'      => now(),
                'datos_remitente' => $request->input('remitente'),
            ]);

            Auditoria::registrar('webhook_pago_qr', 'pagos', $pago->id_pago, $datosAnteriores, $pago->fresh()->toArray());
        }

        return response()->json(['message' => 'OK']);
    }
}
