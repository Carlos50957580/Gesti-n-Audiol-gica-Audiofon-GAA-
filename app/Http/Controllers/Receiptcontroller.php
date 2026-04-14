<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $user    = auth()->user();
        $isAdmin = $user->role->name === 'admin';
        $tab     = $request->get('tab', 'pending');

        $invoicesQuery = Invoice::with(['patient', 'branch', 'insurance'])
            ->where('status', 'pendiente')
            ->orderBy('created_at', 'asc');

        if (!$isAdmin) {
            $invoicesQuery->where('branch_id', $user->branch_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $invoicesQuery->where(function ($sq) use ($q) {
                $sq->whereHas('patient', fn($pq) =>
                    $pq->where('first_name', 'like', "%{$q}%")
                       ->orWhere('last_name',  'like', "%{$q}%")
                       ->orWhere('cedula',     'like', "%{$q}%")
                       ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$q}%"])
                );
            });
        }

        if ($isAdmin && $request->filled('branch_id')) {
            $invoicesQuery->where('branch_id', $request->branch_id);
        }

        $invoices = $invoicesQuery->paginate(20, ['*'], 'ppage')->withQueryString();

        $totalAmount = Invoice::where('status', 'pendiente')
            ->when(!$isAdmin, fn($q) => $q->where('branch_id', $user->branch_id))
            ->sum('total');

        $receiptsQuery = Receipt::with([
                'invoice.patient',
                'invoice.insurance',
                'branch',
                'user',
            ])
            ->whereHas('invoice.patient')
            ->orderBy('created_at', 'desc');

        if (!$isAdmin) {
            $receiptsQuery->where('branch_id', $user->branch_id);
        }

        if ($request->filled('rq')) {
            $rq = $request->rq;
            $receiptsQuery->where(function ($sq) use ($rq) {
                $sq->whereHas('invoice.patient', fn($pq) =>
                    $pq->where('first_name', 'like', "%{$rq}%")
                       ->orWhere('last_name',  'like', "%{$rq}%")
                       ->orWhere('cedula',     'like', "%{$rq}%")
                       ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$rq}%"])
                );
            });
        }

        if ($request->filled('rfrom')) {
            $receiptsQuery->whereDate('created_at', '>=', $request->rfrom);
        }

        if ($request->filled('rto')) {
            $receiptsQuery->whereDate('created_at', '<=', $request->rto);
        }

        if ($isAdmin && $request->filled('rbranch_id')) {
            $receiptsQuery->where('branch_id', $request->rbranch_id);
        }

        $receipts = $receiptsQuery->paginate(20, ['*'], 'rpage')->withQueryString();

        $totalCollected = Receipt::when(!$isAdmin, fn($q) => $q->where('branch_id', $user->branch_id))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at',  now()->year)
            ->sum('total_paid');

        $branches = $isAdmin ? Branch::orderBy('name')->get() : collect();

        return view('receipts.index', compact(
            'invoices', 'receipts', 'branches',
            'isAdmin', 'totalAmount', 'totalCollected', 'tab'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'         => 'required|exists:invoices,id',
            'cash_amount'        => 'nullable|numeric|min:0',
            'card_amount'        => 'nullable|numeric|min:0',
            'transfer_amount'    => 'nullable|numeric|min:0',
            'card_reference'     => 'nullable|string|max:100',
            'transfer_reference' => 'nullable|string|max:100',
            'notes'              => 'nullable|string|max:500',
        ]);

        $invoice = Invoice::where('status', 'pendiente')->findOrFail($request->invoice_id);

        $user = auth()->user();
        if ($user->role->name !== 'admin' && $invoice->branch_id !== $user->branch_id) {
            abort(403);
        }

        $cash     = (float) ($request->cash_amount     ?? 0);
        $card     = (float) ($request->card_amount     ?? 0);
        $transfer = (float) ($request->transfer_amount ?? 0);
        $total    = round($cash + $card + $transfer, 2);
$invoiceTotal = (float) $invoice->total;

// ✅ CASO: FACTURA EN 0
if ($invoiceTotal <= 0) {
    $cash = $card = $transfer = 0;
    $totalPaid = 0;
} else {

    // ❌ No permitir negativos
    if ($total < 0) {
        return back()->withErrors(['payment' => 'El monto no puede ser negativo.'])->withInput();
    }

    // ❌ Tarjeta + transferencia no pueden exceder
    if (($card + $transfer) > $invoiceTotal + 0.01) {
        return back()
            ->withErrors(['payment' => 'Tarjeta y/o transferencia no pueden exceder el total de la factura.'])
            ->withInput();
    }

    $nonCash    = $card + $transfer;
    $cashNeeded = max(0, $invoiceTotal - $nonCash);

    if ($cash < $cashNeeded - 0.01) {
        return back()
            ->withErrors(['payment' => 'El monto en efectivo es insuficiente para cubrir la diferencia.'])
            ->withInput();
    }

    $totalPaid = $invoiceTotal;
}

        if (($card + $transfer) > (float) $invoice->total + 0.01) {
            return back()
                ->withErrors(['payment' => 'Tarjeta y/o transferencia no pueden exceder el total de la factura.'])
                ->withInput();
        }

        $nonCash    = $card + $transfer;
        $cashNeeded = max(0, (float) $invoice->total - $nonCash);
        if ($cash < $cashNeeded - 0.01) {
            return back()
                ->withErrors(['payment' => 'El monto en efectivo es insuficiente para cubrir la diferencia.'])
                ->withInput();
        }

        $totalPaid = (float) $invoice->total;

        DB::beginTransaction();
        try {
            $receipt = Receipt::create([
                'invoice_id'         => $invoice->id,
                'user_id'            => $user->id,
                'branch_id'          => $invoice->branch_id,
                'cash_amount'        => $cash     > 0 ? $cash     : null,
                'card_amount'        => $card     > 0 ? $card     : null,
                'transfer_amount'    => $transfer > 0 ? $transfer : null,
                'total_paid'         => $totalPaid,
                'card_reference'     => $request->card_reference     ?: null,
                'transfer_reference' => $request->transfer_reference ?: null,
                'notes'              => $request->notes              ?: null,
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

    public function show(Receipt $receipt)
    {
        $receipt->load(['invoice.patient', 'invoice.items.service', 'invoice.insurance', 'user', 'branch']);
        return view('receipts.show', compact('receipt'));
    }

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