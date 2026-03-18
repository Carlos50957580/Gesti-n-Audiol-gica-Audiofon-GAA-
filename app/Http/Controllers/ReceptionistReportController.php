<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
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
        $branchId = auth()->user()->branch_id;
        $range    = $this->getRange($request);

        // ── Base invoices ────────────────────────────────────
        $base = fn() => Invoice::where('invoices.branch_id', $branchId)
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']]);

        $totalFacturas  = $base()->count();
        $pagadas        = $base()->where('invoices.status', 'pagada')->count();
        $pendientes     = $base()->where('invoices.status', 'pendiente')->count();
        $canceladas     = $base()->where('invoices.status', 'cancelada')->count();
        $subtotal       = $base()->where('invoices.status', 'pagada')->sum('invoices.subtotal') ?: 0;
        $descuentos     = $base()->where('invoices.status', 'pagada')->sum('invoices.insurance_discount') ?: 0;
        $totalFacturado = $base()->where('invoices.status', 'pagada')->sum('invoices.total') ?: 0;

        // ── Cobros desde receipts via JOIN ───────────────────
        $cobros = DB::table('invoices')
            ->leftJoin('receipts', 'invoices.id', '=', 'receipts.invoice_id')
            ->where('invoices.branch_id', $branchId)
            ->where('invoices.status', 'pagada')
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']])
            ->selectRaw('
                COALESCE(SUM(receipts.cash_amount), 0)     as efectivo,
                COALESCE(SUM(receipts.card_amount), 0)     as tarjeta,
                COALESCE(SUM(receipts.transfer_amount), 0) as transferencia,
                COALESCE(SUM(receipts.total_paid), 0)      as total_cobrado
            ')
            ->first();

        // ── Por hora ─────────────────────────────────────────
        $byHour = DB::table('invoices')
            ->where('branch_id', $branchId)
            ->where('status', 'pagada')
            ->whereBetween('created_at', [$range['from'], $range['to']])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(total) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // ── Por audiólogo ────────────────────────────────────
        $byAudiologist = DB::table('invoices')
            ->join('users', 'invoices.audiologist_id', '=', 'users.id')
            ->where('invoices.branch_id', $branchId)
            ->where('invoices.status', 'pagada')
            ->whereNotNull('invoices.audiologist_id')
            ->whereBetween('invoices.created_at', [$range['from'], $range['to']])
            ->selectRaw('users.name as audiologist, COUNT(*) as count, SUM(invoices.total) as total')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'kpis' => [
                'total_facturas'  => $totalFacturas,
                'pagadas'         => $pagadas,
                'pendientes'      => $pendientes,
                'canceladas'      => $canceladas,
                'subtotal'        => number_format($subtotal, 2),
                'descuentos'      => number_format($descuentos, 2),
                'total_facturado' => number_format($totalFacturado, 2),
                'efectivo'        => number_format($cobros->efectivo, 2),
                'tarjeta'         => number_format($cobros->tarjeta, 2),
                'transferencia'   => number_format($cobros->transferencia, 2),
                'total_cobrado'   => number_format($cobros->total_cobrado, 2),
            ],
            'by_hour'        => $byHour,
            'by_audiologist' => $byAudiologist,
        ]);
    }

    public function invoices(Request $request): JsonResponse
    {
        $branchId = auth()->user()->branch_id;
        $range    = $this->getRange($request);

        $invoices = Invoice::with(['patient', 'audiologist', 'receipt', 'items.service'])
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$range['from'], $range['to']])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'invoices' => $invoices->map(function($inv) {
                return [
                    'id'            => $inv->id,
                    'number'        => 'FAC-' . str_pad($inv->id, 6, '0', STR_PAD_LEFT),
                    'patient'       => $inv->patient->first_name . ' ' . $inv->patient->last_name,
                    'cedula'        => $inv->patient->cedula,
                    'audiologist'   => $inv->audiologist?->name ?? '—',
                    'status'        => $inv->status,
                    'subtotal'      => number_format($inv->subtotal, 2),
                    'descuento'     => number_format($inv->insurance_discount, 2),
                    'total'         => number_format($inv->total, 2),
                    'efectivo'      => number_format($inv->receipt?->cash_amount     ?? 0, 2),
                    'tarjeta'       => number_format($inv->receipt?->card_amount     ?? 0, 2),
                    'transferencia' => number_format($inv->receipt?->transfer_amount ?? 0, 2),
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
        $branchId = auth()->user()->branch_id;
        $range    = $this->getRange($request);

        $services = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('services', 'invoice_items.service_id', '=', 'services.id')
            ->where('invoices.branch_id', $branchId)
            ->where('invoices.status', 'pagada')
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
}