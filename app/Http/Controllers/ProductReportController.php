<?php

namespace App\Http\Controllers;

use App\Models\ProductInvoice;
use App\Models\ProductReceipt;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductReportController extends Controller
{
    /**
     * Reporte general (solo admin)
     */
    public function index(Request $request)
{
    $user = auth()->user();
    if ($user->role->name !== 'admin') {
        abort(403, 'Solo administradores pueden acceder a este reporte.');
    }

    $range = $this->getRange($request);
    $branchId = $request->filled('branch_id') ? $request->branch_id : null;
    $status = $request->filled('status') ? $request->status : null;

    // ✅ Filtro por usuario (quien registró/cobró la venta)
    $userId = $request->filled('user_id') ? $request->user_id : null;

    // ── Base query ───────────────────────────────────────────
    $base = ProductInvoice::query()
        ->whereBetween('created_at', [$range['from'], $range['to']]);

    if ($branchId) {
        $base->where('branch_id', $branchId);
    }

    if ($userId) {
        $base->where('user_id', $userId);
    }

    // ── KPIs ────────────────────────────────────────────────
    $totalInvoices = (clone $base)->count();

    $byStatus = (clone $base)
        ->selectRaw('status, COUNT(*) as count, SUM(total) as total, SUM(paid_amount) as paid')
        ->groupBy('status')
        ->get()
        ->keyBy('status');

    $totalFacturado = $byStatus->sum('total');
    $totalCobrado   = $byStatus->sum('paid');
    $totalPendiente = $totalFacturado - $totalCobrado;

    // ── Cobros por método (solo de los recibos) ─────────────
    $receiptsQuery = ProductReceipt::query()
        ->whereHas('invoice', function ($q) use ($range, $branchId, $status, $userId) {
            $q->whereBetween('created_at', [$range['from'], $range['to']]);
            if ($branchId) $q->where('branch_id', $branchId);
            if ($status) $q->where('status', $status);
            if ($userId) $q->where('user_id', $userId);
        });

    $cobros = (clone $receiptsQuery)
        ->selectRaw('
            COALESCE(SUM(cash_amount), 0) as efectivo,
            COALESCE(SUM(card_amount), 0) as tarjeta,
            COALESCE(SUM(transfer_amount), 0) as transferencia,
            COALESCE(SUM(total_paid), 0) as total
        ')
        ->first();

    // ── Ventas por día ──────────────────────────────────────
    $days = Carbon::parse($range['from'])->diffInDays(Carbon::parse($range['to'])) + 1;
    $salesByDay = (clone $base)
        ->where('status', '!=', 'cancelada')
        ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total) as total')
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('total', 'date')
        ->toArray();

    $chartDays = [];
    for ($i = 0; $i < $days; $i++) {
        $d = Carbon::parse($range['from'])->addDays($i)->format('Y-m-d');
        $chartDays[] = [
            'label' => Carbon::parse($d)->locale('es')->isoFormat('D MMM'),
            'total' => (float) ($salesByDay[$d] ?? 0),
        ];
    }

    // ── Ventas por sucursal ──────────────────────────────────
    $byBranch = (clone $base)
        ->where('status', '!=', 'cancelada')
        ->with('branch')
        ->selectRaw('branch_id, COUNT(*) as count, SUM(total) as total')
        ->groupBy('branch_id')
        ->orderByDesc('total')
        ->get();

    // ── Top 10 productos más vendidos ───────────────────────
    $topProducts = DB::table('product_invoice_items')
        ->join('product_invoices', 'product_invoice_items.product_invoice_id', '=', 'product_invoices.id')
        ->join('products', 'product_invoice_items.product_id', '=', 'products.id')
        ->whereBetween('product_invoices.created_at', [$range['from'], $range['to']])
        ->where('product_invoices.status', '!=', 'cancelada')
        ->when($branchId, fn($q) => $q->where('product_invoices.branch_id', $branchId))
        ->when($userId, fn($q) => $q->where('product_invoices.user_id', $userId))
        ->selectRaw('
            products.id,
            products.code,
            products.name,
            SUM(product_invoice_items.quantity) as qty,
            SUM(product_invoice_items.total_with_tax) as revenue
        ')
        ->groupBy('products.id', 'products.code', 'products.name')
        ->orderByDesc('qty')
        ->limit(10)
        ->get();

    $maxProductQty = $topProducts->max('qty') ?: 1;

    // ── Ventas por categoría ────────────────────────────────
    $byCategory = DB::table('product_invoice_items')
        ->join('product_invoices', 'product_invoice_items.product_invoice_id', '=', 'product_invoices.id')
        ->join('products', 'product_invoice_items.product_id', '=', 'products.id')
        ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
        ->whereBetween('product_invoices.created_at', [$range['from'], $range['to']])
        ->where('product_invoices.status', '!=', 'cancelada')
        ->when($branchId, fn($q) => $q->where('product_invoices.branch_id', $branchId))
        ->when($userId, fn($q) => $q->where('product_invoices.user_id', $userId))
        ->selectRaw('
            COALESCE(product_categories.id, 0) as category_id,
            COALESCE(product_categories.name, "Sin categoría") as category_name,
            COALESCE(product_categories.color, "#6b7a99") as category_color,
            SUM(product_invoice_items.quantity) as qty,
            SUM(product_invoice_items.total_with_tax) as revenue
        ')
        ->groupBy('product_categories.id', 'product_categories.name', 'product_categories.color')
        ->orderByDesc('revenue')
        ->get();

    $branches = Branch::where('is_active', 1)->orderBy('name')->get();

    // ✅ Usuarios que pueden vender/cobrar (admin y recepcionista)
    $users = \App\Models\User::whereIn('role_id', [1, 2])->orderBy('name')->get();

    // ✅ Listado completo de ventas del período para el DataTable
    $invoices = (clone $base)
        ->with(['patient', 'branch', 'user'])
        ->latest()
        ->get();

    return view('product-reports.index', compact(
        'range', 'branchId', 'status', 'userId',
        'totalInvoices', 'byStatus',
        'totalFacturado', 'totalCobrado', 'totalPendiente',
        'cobros', 'chartDays', 'byBranch',
        'topProducts', 'maxProductQty',
        'byCategory', 'branches', 'users', 'invoices'
    ));
}
    /**
     * Cuadre de caja para recepcionista (vista de pantalla, con filtros)
     */
    public function cashier(Request $request)
    {
        $data = $this->buildCashierData($request);

        return view('product-reports.cashier', $data);
    }

    /**
     * Imprimir cuadre de caja (respeta los mismos filtros de la pantalla)
     */
    public function cashierPrint(Request $request)
    {
        $data = $this->buildCashierData($request, forPrint: true);

        return view('product-reports.cashier-print', $data);
    }

    /**
     * Lógica compartida entre la vista de pantalla y la de impresión.
     * Así evitamos que se desincronicen las variables entre ambas vistas.
     */
     private function buildCashierData(Request $request, bool $forPrint = false): array
{
    $user = auth()->user();
    if (!in_array($user->role->name, ['admin', 'recepcionista'])) {
        abort(403);
    }

    $isAdmin = $user->role->name === 'admin';
    $range = $this->getRange($request);

    $branchId = $isAdmin
        ? ($request->filled('branch_id') ? $request->branch_id : null)
        : $user->branch_id;

    $branch = $branchId ? Branch::find($branchId) : null;

    $paymentMethod = $request->filled('payment_method') ? $request->payment_method : null;

    $paymentColumnMap = [
        'efectivo'      => 'cash_amount',
        'tarjeta'       => 'card_amount',
        'transferencia' => 'transfer_amount',
    ];

    // ✅ Filtro por usuario que cobró (solo admin puede elegir; recepcionista siempre ve solo lo suyo)
    $filterUserId = $isAdmin && $request->filled('user_id') ? $request->user_id : null;

    // ── Query base de recibos con todos los filtros aplicados ──
    $query = ProductReceipt::query()
        ->whereBetween('created_at', [$range['from'], $range['to']]);

    if ($branchId) {
        $query->where('branch_id', $branchId);
    }

    if (!$isAdmin) {
        $query->where('user_id', $user->id);
    } elseif ($filterUserId) {
        $query->where('user_id', $filterUserId);
    }

    if ($paymentMethod && isset($paymentColumnMap[$paymentMethod])) {
        $query->where($paymentColumnMap[$paymentMethod], '>', 0);
    }

    $receiptsBuilder = (clone $query)->with(['invoice.patient', 'user', 'branch']);
    $receipts = $forPrint
        ? $receiptsBuilder->orderBy('created_at')->get()
        : $receiptsBuilder->latest()->get();

    $totals = (clone $query)
        ->selectRaw('
            COALESCE(SUM(cash_amount), 0) as efectivo,
            COALESCE(SUM(card_amount), 0) as tarjeta,
            COALESCE(SUM(transfer_amount), 0) as transferencia,
            COALESCE(SUM(total_paid), 0) as total
        ')
        ->first();

    $byUser = collect();
    if ($isAdmin && !$forPrint) {
        $byUser = (clone $query)
            ->with('user')
            ->selectRaw('
                user_id,
                COUNT(*) as count,
                COALESCE(SUM(cash_amount), 0) as efectivo,
                COALESCE(SUM(card_amount), 0) as tarjeta,
                COALESCE(SUM(transfer_amount), 0) as transferencia,
                COALESCE(SUM(total_paid), 0) as total
            ')
            ->groupBy('user_id')
            ->get();
    }

    $totalInvoices = $pendingInvoices = $partialInvoices = $paidInvoices = $cancelledInvoices = 0;
    if (!$forPrint) {
        $invoicesQuery = ProductInvoice::query()
            ->whereBetween('created_at', [$range['from'], $range['to']]);

        if ($branchId) $invoicesQuery->where('branch_id', $branchId);
        if (!$isAdmin) {
            $invoicesQuery->where('user_id', $user->id);
        } elseif ($filterUserId) {
            $invoicesQuery->where('user_id', $filterUserId);
        }

        $totalInvoices = (clone $invoicesQuery)->count();
        $pendingInvoices = (clone $invoicesQuery)->where('status', 'pendiente')->count();
        $partialInvoices = (clone $invoicesQuery)->where('status', 'pagada_parcial')->count();
        $paidInvoices = (clone $invoicesQuery)->where('status', 'pagada')->count();
        $cancelledInvoices = (clone $invoicesQuery)->where('status', 'cancelada')->count();
    }

    $branches = $isAdmin ? Branch::where('is_active', 1)->orderBy('name')->get() : collect();

    // ✅ Lista de usuarios que pueden cobrar (admin = role_id 1, recepcionista = role_id 2)
    $cashierUsers = $isAdmin
        ? \App\Models\User::whereIn('role_id', [1, 2])->orderBy('name')->get()
        : collect();

    return [
        'range'          => $range,
        'branchId'       => $branchId,
        'branch'         => $branch,
        'isAdmin'        => $isAdmin,
        'user'           => $user,
        'paymentMethod'  => $paymentMethod,
        'filterUserId'   => $filterUserId,
        'cashierUsers'   => $cashierUsers,
        'receipts'       => $receipts,
        'totals'         => $totals,
        'byUser'         => $byUser,
        'totalInvoices'  => $totalInvoices,
        'pendingInvoices'   => $pendingInvoices,
        'partialInvoices'   => $partialInvoices,
        'paidInvoices'      => $paidInvoices,
        'cancelledInvoices' => $cancelledInvoices,
        'branches'       => $branches,
    ];
}

    // ═══════════════════════════════════════════════════════════
    // API
    // ═══════════════════════════════════════════════════════════

    /**
     * Calcular rango de fechas
     */
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
}