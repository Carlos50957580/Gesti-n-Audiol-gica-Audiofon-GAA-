<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReceptionistReportController extends Controller
{
    public function index()
    {
        return view('reports.receptionist');
    }

    private function getRange(Request $request): array
    {
        $dateFrom = $request->filled('date_from') ? $request->date_from : now()->toDateString();
        $dateTo   = $request->filled('date_to')   ? $request->date_to   : now()->toDateString();
        $timeFrom = $request->filled('time_from') ? substr($request->time_from, 0, 5) : '00:00';
        $timeTo   = $request->filled('time_to')   ? substr($request->time_to,   0, 5) : '23:59';

        return [
            'from' => $dateFrom . ' ' . $timeFrom . ':00',
            'to'   => $dateTo   . ' ' . $timeTo   . ':59',
        ];
    }

    public function summary(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $branchId = auth()->user()->branch_id;
        $range = $this->getRange($request);

        // ── Base invoices filtrada por usuario que cobró ────────────────────
        // Una factura es "cobrada por" el usuario que creó el receipt
        $base = fn() => Invoice::where('invoices.branch_id', $branchId)
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']])
            ->whereHas('receipts', function($q) use ($userId) {
                $q->where('user_id', $userId);
            });

        // ── Todas las facturas del período (para contar totales) ────────────
        $allInvoices = Invoice::where('branch_id', $branchId)
            ->whereBetween('created_at', [$range['from'], $range['to']]);

        $totalFacturas  = $allInvoices->count();
        $pagadas        = $allInvoices->where('status', 'pagada')->count();
        $pendientes     = $allInvoices->where('status', 'pendiente')->count();
        $canceladas     = $allInvoices->where('status', 'cancelada')->count();

        // ── Solo facturas pagadas por este usuario ──────────────────────────
        $subtotal       = $base()->sum('invoices.subtotal') ?: 0;
        $descuentos     = $base()->sum('invoices.insurance_discount') ?: 0;
        $totalFacturado = $base()->sum('invoices.total') ?: 0;

        // ── Cobros desde receipts de este usuario ───────────────────────────
        $cobros = DB::table('invoices')
            ->join('receipts', 'invoices.id', '=', 'receipts.invoice_id')
            ->where('invoices.branch_id', $branchId)
            ->where('invoices.status', 'pagada')
            ->where('receipts.user_id', $userId)
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']])
            ->selectRaw('
                COALESCE(SUM(receipts.cash_amount), 0)     as efectivo,
                COALESCE(SUM(receipts.card_amount), 0)     as tarjeta,
                COALESCE(SUM(receipts.transfer_amount), 0) as transferencia,
                COALESCE(SUM(receipts.total_paid), 0)      as total_cobrado
            ')
            ->first();

        // ── Por hora (solo facturas cobradas por este usuario) ─────────────
        $byHour = DB::table('invoices')
            ->join('receipts', 'invoices.id', '=', 'receipts.invoice_id')
            ->where('invoices.branch_id', $branchId)
            ->where('invoices.status', 'pagada')
            ->where('receipts.user_id', $userId)
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']])
            ->selectRaw('HOUR(invoices.created_at) as hour, COUNT(*) as count, SUM(invoices.total) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // ── Por doctor (solo facturas cobradas por este usuario) ────────────
        $byDoctor = DB::table('invoices')
            ->join('users', 'invoices.doctor_id', '=', 'users.id')
            ->join('receipts', 'invoices.id', '=', 'receipts.invoice_id')
            ->where('invoices.branch_id', $branchId)
            ->where('invoices.status', 'pagada')
            ->where('receipts.user_id', $userId)
            ->whereNotNull('invoices.doctor_id')
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']])
            ->selectRaw('users.name as doctor, COUNT(*) as count, SUM(invoices.total) as total')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->get();

        // ── Por método de pago ──────────────────────────────────────────────
        $byPaymentMethod = DB::table('receipts')
            ->join('invoices', 'receipts.invoice_id', '=', 'invoices.id')
            ->where('invoices.branch_id', $branchId)
            ->where('invoices.status', 'pagada')
            ->where('receipts.user_id', $userId)
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']])
            ->selectRaw('
                COALESCE(SUM(receipts.cash_amount), 0) as cash,
                COALESCE(SUM(receipts.card_amount), 0) as card,
                COALESCE(SUM(receipts.transfer_amount), 0) as transfer
            ')
            ->first();

        return response()->json([
            'kpis' => [
                'total_facturas'  => $totalFacturas,
                'pagadas'         => $pagadas,
                'pendientes'      => $pendientes,
                'canceladas'      => $canceladas,
                'subtotal'        => number_format($subtotal, 2),
                'descuentos'      => number_format($descuentos, 2),
                'total_facturado' => number_format($totalFacturado, 2),
                'efectivo'        => number_format($cobros->efectivo ?? 0, 2),
                'tarjeta'         => number_format($cobros->tarjeta ?? 0, 2),
                'transferencia'   => number_format($cobros->transferencia ?? 0, 2),
                'total_cobrado'   => number_format($cobros->total_cobrado ?? 0, 2),
            ],
            'by_hour'          => $byHour,
            'by_doctor'        => $byDoctor,
            'by_payment_method' => [
                'cash'     => number_format($byPaymentMethod->cash ?? 0, 2),
                'card'     => number_format($byPaymentMethod->card ?? 0, 2),
                'transfer' => number_format($byPaymentMethod->transfer ?? 0, 2),
            ],
        ]);
    }

    public function invoices(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $branchId = auth()->user()->branch_id;
        $range = $this->getRange($request);

        $invoices = Invoice::with(['patient', 'doctor', 'receipts', 'items.service'])
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$range['from'], $range['to']])
            ->whereHas('receipts', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'invoices' => $invoices->map(function($inv) {
                $receipt = $inv->receipts->first();
                return [
                    'id'            => $inv->id,
                    'number'        => $inv->invoice_number,
                    'patient'       => $inv->patient->first_name . ' ' . $inv->patient->last_name,
                    'cedula'        => $inv->patient->cedula ?? 'N/A',
                    'doctor'        => $inv->doctor?->name ?? '—',
                    'status'        => $inv->status,
                    'subtotal'      => number_format($inv->subtotal, 2),
                    'descuento'     => number_format($inv->insurance_discount, 2),
                    'total'         => number_format($inv->total, 2),
                    'efectivo'      => number_format($receipt?->cash_amount ?? 0, 2),
                    'tarjeta'       => number_format($receipt?->card_amount ?? 0, 2),
                    'transferencia' => number_format($receipt?->transfer_amount ?? 0, 2),
                    'time'          => $inv->created_at->format('H:i'),
                    'date'          => $inv->created_at->format('d/m/Y'),
                    'services'      => $inv->items
                        ->map(fn($i) => $i->service?->name)
                        ->filter()
                        ->join(', '),
                ];
            }),
        ]);
    }

    public function services(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $branchId = auth()->user()->branch_id;
        $range = $this->getRange($request);

        $services = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('services', 'invoice_items.service_id', '=', 'services.id')
            ->join('receipts', 'invoices.id', '=', 'receipts.invoice_id')
            ->where('invoices.branch_id', $branchId)
            ->where('invoices.status', 'pagada')
            ->where('receipts.user_id', $userId)
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']])
            ->selectRaw('
                services.name                                      as service,
                SUM(invoice_items.quantity)                        as qty,
                COALESCE(SUM(invoice_items.patient_amount),   0)   as patient_total,
                COALESCE(SUM(invoice_items.insurance_amount), 0)   as insurance_total,
                SUM(invoice_items.subtotal)                        as subtotal
            ')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('subtotal')
            ->get();

        return response()->json(['services' => $services]);
    }

    /**
     * Generar PDF de cuadre de caja para impresión
     */
    public function print(Request $request)
    {
        $userId = auth()->id();
        $branchId = auth()->user()->branch_id;
        $range = $this->getRange($request);

        $branch = Branch::find($branchId);
        $user = auth()->user();

        // ── Todas las facturas del período (para totales) ──────────────────
        $allInvoices = Invoice::where('branch_id', $branchId)
            ->whereBetween('created_at', [$range['from'], $range['to']]);

        $totalFacturas  = $allInvoices->count();
        $pagadas        = $allInvoices->where('status', 'pagada')->count();
        $pendientes     = $allInvoices->where('status', 'pendiente')->count();
        $canceladas     = $allInvoices->where('status', 'cancelada')->count();

        // ── Facturas pagadas por este usuario ──────────────────────────────
        $base = fn() => Invoice::where('invoices.branch_id', $branchId)
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']])
            ->whereHas('receipts', function($q) use ($userId) {
                $q->where('user_id', $userId);
            });

        $subtotal       = $base()->sum('invoices.subtotal') ?: 0;
        $descuentos     = $base()->sum('invoices.insurance_discount') ?: 0;
        $totalFacturado = $base()->sum('invoices.total') ?: 0;

        // ── Cobros ──────────────────────────────────────────────────────────
        $cobros = DB::table('invoices')
            ->join('receipts', 'invoices.id', '=', 'receipts.invoice_id')
            ->where('invoices.branch_id', $branchId)
            ->where('invoices.status', 'pagada')
            ->where('receipts.user_id', $userId)
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']])
            ->selectRaw('
                COALESCE(SUM(receipts.cash_amount), 0)     as efectivo,
                COALESCE(SUM(receipts.card_amount), 0)     as tarjeta,
                COALESCE(SUM(receipts.transfer_amount), 0) as transferencia,
                COALESCE(SUM(receipts.total_paid), 0)      as total_cobrado
            ')
            ->first();

        // ── Facturas del período (solo las cobradas por este usuario) ──────
        $invoices = Invoice::with(['patient', 'doctor', 'receipts', 'items.service'])
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$range['from'], $range['to']])
            ->whereHas('receipts', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderBy('created_at')
            ->get();

        // ── Datos de la empresa ─────────────────────────────────────────────
        $company = [
            'name' => \App\Models\Setting::get('company_name', 'Mi Clínica'),
            'business_name' => \App\Models\Setting::get('company_business_name', 'Mi Clínica SRL'),
            'rnc' => \App\Models\Setting::get('company_rnc', ''),
            'address' => \App\Models\Setting::get('company_address', ''),
            'phone' => \App\Models\Setting::get('company_phone', ''),
            'currency' => \App\Models\Setting::get('company_currency', 'DOP'),
        ];

        return view('reports.receptionist-print', compact(
            'branch',
            'user',
            'range',
            'totalFacturas',
            'pagadas',
            'pendientes',
            'canceladas',
            'subtotal',
            'descuentos',
            'totalFacturado',
            'cobros',
            'invoices',
            'company'
        ));
    }
}