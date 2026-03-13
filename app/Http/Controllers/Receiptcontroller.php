<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    // ── INDEX — Facturas pendientes de pago ───────────────────────────────────
    public function index(Request $request)
    {
        $user    = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        $query = Invoice::with(['patient', 'branch', 'receipt'])
            ->where('status', 'pendiente')
            ->orderBy('created_at', 'asc');   // más antiguas primero

        // Scope por sucursal para recepcionista
        if (!$isAdmin) {
            $query->where('branch_id', $user->branch_id);
        }

        // Búsqueda
        if ($request->filled('q')) {
            $q         = $request->q;
            $numericId = null;
            if (preg_match('/(?:FAC-?)?(\d+)/i', $q, $m)) {
                $numericId = (int) $m[1];
            }
            $query->where(function ($sq) use ($q, $numericId) {
                if ($numericId) $sq->orWhere('id', $numericId);
                $sq->orWhereHas('patient', fn($pq) =>
                    $pq->where('first_name', 'like', "%{$q}%")
                       ->orWhere('last_name',  'like', "%{$q}%")
                       ->orWhere('cedula',     'like', "%{$q}%")
                       ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$q}%"])
                );
            });
        }

        // Filtro de sucursal (solo admin)
        if ($isAdmin && $request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $invoices = $query->paginate(20)->withQueryString();

        // Stats rápidas
        $totalPending = (clone $query->getQuery())->count();   // ya filtrado
        $totalAmount  = Invoice::where('status', 'pendiente')
            ->when(!$isAdmin, fn($q) => $q->where('branch_id', $user->branch_id))
            ->sum('total');

        $branches = $isAdmin ? Branch::orderBy('name')->get() : collect();

        return view('receipts.index', compact(
            'invoices', 'branches', 'isAdmin', 'totalAmount'
        ));
    }

    // ── CREATE — Modal de pago para una factura ───────────────────────────────
    public function create(Request $request)
    {
        $invoice = Invoice::with(['patient', 'branch', 'insurance', 'items.service'])
            ->where('status', 'pendiente')
            ->findOrFail($request->invoice_id);

        // Autorización: recepcionista solo puede cobrar su sucursal
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $invoice->branch_id !== $user->branch_id) {
            abort(403);
        }

        return response()->json([
            'invoice' => [
                'id'                 => $invoice->id,
                'invoice_number'     => $invoice->invoice_number,
                'patient_name'       => $invoice->patient->first_name . ' ' . $invoice->patient->last_name,
                'patient_cedula'     => $invoice->patient->cedula,
                'branch_name'        => $invoice->branch->name,
                'insurance_name'     => $invoice->insurance?->name,
                'total'              => (float) $invoice->total,
                'subtotal'           => (float) $invoice->subtotal,
                'insurance_discount' => (float) $invoice->insurance_discount,
                'created_at'         => $invoice->created_at->format('d/m/Y'),
            ]
        ]);
    }

    // ── STORE — Registrar el pago ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'          => 'required|exists:invoices,id',
            'cash_amount'         => 'nullable|numeric|min:0',
            'card_amount'         => 'nullable|numeric|min:0',
            'transfer_amount'     => 'nullable|numeric|min:0',
            'card_reference'      => 'nullable|string|max:100',
            'transfer_reference'  => 'nullable|string|max:100',
            'notes'               => 'nullable|string|max:500',
        ]);

        $invoice = Invoice::where('status', 'pendiente')->findOrFail($request->invoice_id);

        // Autorización
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $invoice->branch_id !== $user->branch_id) {
            abort(403);
        }

        $cash     = (float) ($request->cash_amount     ?? 0);
        $card     = (float) ($request->card_amount     ?? 0);
        $transfer = (float) ($request->transfer_amount ?? 0);
        $total    = round($cash + $card + $transfer, 2);

        if ($total <= 0) {
            return back()->withErrors(['payment' => 'Debes ingresar al menos un monto de pago.'])->withInput();
        }

        // Tarjeta + transferencia no pueden exceder el total
        if (($card + $transfer) > (float) $invoice->total + 0.01) {
            return back()
                ->withErrors(['payment' => 'Tarjeta y/o transferencia no pueden exceder el total de la factura.'])
                ->withInput();
        }

        // El efectivo cubre el resto (puede ser mayor → genera vuelto)
        $nonCash    = $card + $transfer;
        $cashNeeded = max(0, (float) $invoice->total - $nonCash);
        if ($cash < $cashNeeded - 0.01) {
            return back()
                ->withErrors(['payment' => 'El monto en efectivo es insuficiente para cubrir la diferencia.'])
                ->withInput();
        }

        // total_paid siempre es el total de la factura (lo que se cobró, no lo que entregó el paciente)
        $totalPaid = (float) $invoice->total;

        DB::beginTransaction();
        try {
            $receipt = Receipt::create([
                'invoice_id'          => $invoice->id,
                'user_id'             => $user->id,
                'branch_id'           => $invoice->branch_id,
                'cash_amount'         => $cash     > 0 ? $cash     : null,   // efectivo recibido (puede incluir vuelto)
                'card_amount'         => $card     > 0 ? $card     : null,
                'transfer_amount'     => $transfer > 0 ? $transfer : null,
                'total_paid'          => $totalPaid,                          // siempre = total factura
                'card_reference'      => $request->card_reference     ?: null,
                'transfer_reference'  => $request->transfer_reference ?: null,
                'notes'               => $request->notes              ?: null,
            ]);

            $invoice->update(['status' => 'pagada']);

            DB::commit();

            return redirect()
                ->route('receipts.show', $receipt)
                ->with('success', 'Pago registrado. ' . $receipt->receipt_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['payment' => 'Error al registrar el pago: ' . $e->getMessage()])->withInput();
        }
    }

    // ── SHOW — Recibo imprimible ──────────────────────────────────────────────
    public function show(Receipt $receipt)
    {
        $receipt->load(['invoice.patient', 'invoice.items.service', 'invoice.insurance', 'user', 'branch']);
        return view('receipts.show', compact('receipt'));
    }

    // ── AJAX: datos de factura para el modal ──────────────────────────────────
    public function invoiceData(Invoice $invoice)
    {
        if ($invoice->status !== 'pendiente') {
            return response()->json(['error' => 'Esta factura no está pendiente.'], 422);
        }

        $user = auth()->user();
        if ($user->role->name !== 'admin' && $invoice->branch_id !== $user->branch_id) {
            return response()->json(['error' => 'Sin autorización.'], 403);
        }

        $invoice->load(['patient', 'branch', 'insurance', 'items.service']);

        return response()->json([
            'id'                 => $invoice->id,
            'invoice_number'     => $invoice->invoice_number,
            'patient_name'       => $invoice->patient->first_name . ' ' . $invoice->patient->last_name,
            'patient_cedula'     => $invoice->patient->cedula,
            'patient_phone'      => $invoice->patient->phone,
            'branch_name'        => $invoice->branch->name,
            'insurance_name'     => $invoice->insurance?->name,
            'total'              => (float) $invoice->total,
            'subtotal'           => (float) $invoice->subtotal,
            'insurance_discount' => (float) $invoice->insurance_discount,
            'created_at'         => $invoice->created_at->format('d/m/Y'),
            'items'              => $invoice->items->map(fn($it) => [
                'name'     => $it->service->name,
                'qty'      => $it->quantity,
                'subtotal' => (float) $it->subtotal,
            ]),
        ]);
    }
}