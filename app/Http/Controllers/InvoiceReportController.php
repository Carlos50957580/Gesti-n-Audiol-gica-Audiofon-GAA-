<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class InvoiceReportController extends Controller
{
    /**
     * Cuadre de caja de facturas normales (pantalla)
     */
    public function cashier(Request $request)
    {
        $data = $this->buildCashierData($request);

        return view('invoice-reports.cashier', $data);
    }

    /**
     * Imprimir cuadre de caja (respeta los mismos filtros de la pantalla)
     */
    public function cashierPrint(Request $request)
    {
        $data = $this->buildCashierData($request, forPrint: true);

        return view('invoice-reports.cashier-print', $data);
    }

    /**
     * Lógica compartida entre pantalla e impresión
     */
    private function buildCashierData(Request $request, bool $forPrint = false): array
    {
        $user = auth()->user();

        if (!in_array($user->role->name, ['admin', 'recepcionista'])) {
            abort(403);
        }

        $isAdmin = $user->role->name === 'admin';
        $range   = $this->getRange($request);

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

        // Solo admin puede filtrar por cajero; recepcionista siempre ve solo lo suyo
        $filterUserId = $isAdmin && $request->filled('user_id') ? $request->user_id : null;

        // ── Query base de recibos (tabla `receipts`) ──
        $query = Receipt::query()
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

        // Resumen por recepcionista (solo admin, solo pantalla)
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

        // Conteo de facturas (solo pantalla, no se muestran en el blade actual)
        $totalInvoices = $pendingInvoices = $paidInvoices = $cancelledInvoices = 0;
        if (!$forPrint) {
            $invoicesQuery = Invoice::query()
                ->whereBetween('created_at', [$range['from'], $range['to']]);

            if ($branchId) $invoicesQuery->where('branch_id', $branchId);

            if (!$isAdmin) {
                $invoicesQuery->where('user_id', $user->id);
            } elseif ($filterUserId) {
                $invoicesQuery->where('user_id', $filterUserId);
            }

            $totalInvoices     = (clone $invoicesQuery)->count();
            $pendingInvoices   = (clone $invoicesQuery)->where('status', 'pendiente')->count();
            $paidInvoices      = (clone $invoicesQuery)->where('status', 'pagada')->count();
            $cancelledInvoices = (clone $invoicesQuery)->where('status', 'cancelada')->count();
        }

        $branches = $isAdmin
            ? Branch::where('is_active', 1)->orderBy('name')->get()
            : collect();

        $cashierUsers = $isAdmin
            ? User::whereIn('role_id', [1, 2])->orderBy('name')->get()
            : collect();

        return [
            'range'             => $range,
            'branchId'          => $branchId,
            'branch'            => $branch,
            'isAdmin'           => $isAdmin,
            'user'              => $user,
            'paymentMethod'     => $paymentMethod,
            'filterUserId'      => $filterUserId,
            'cashierUsers'      => $cashierUsers,
            'receipts'          => $receipts,
            'totals'            => $totals,
            'byUser'            => $byUser,
            'totalInvoices'     => $totalInvoices,
            'pendingInvoices'   => $pendingInvoices,
            'paidInvoices'      => $paidInvoices,
            'cancelledInvoices' => $cancelledInvoices,
            'branches'          => $branches,
        ];
    }

    /**
     * Calcular rango de fechas + hora
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