<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $isAdmin  = $user->role->name === 'admin';
        $branchId = $user->branch_id;

        $today        = Carbon::today();
        $thisMonth    = Carbon::now()->startOfMonth();
        $lastMonth    = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // ── Base queries con scope de sucursal ───────────────────────────────
        $invQ  = Invoice::query()->when(!$isAdmin, fn($q) => $q->where('branch_id', $branchId));
        $apptQ = Appointment::query()->when(!$isAdmin, fn($q) => $q->where('branch_id', $branchId));
        $patQ  = Patient::query()->when(!$isAdmin, fn($q) => $q->where('branch_id', $branchId));

        // ── KPI: Ingresos ────────────────────────────────────────────────────
        $revenueThisMonth = (clone $invQ)->where('status', 'pagada')
            ->whereBetween('created_at', [$thisMonth, now()])->sum('total');
        $revenueLastMonth = (clone $invQ)->where('status', 'pagada')
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->sum('total');
        $revenueGrowth    = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        // ── KPI: Facturas ────────────────────────────────────────────────────
        $invoicesThisMonth = (clone $invQ)->whereBetween('created_at', [$thisMonth, now()])->count();
        $pendingInvoices   = (clone $invQ)->where('status', 'pendiente')->count();
        $pendingAmount     = (clone $invQ)->where('status', 'pendiente')->sum('total');

        // ── KPI: Citas ───────────────────────────────────────────────────────
        $apptToday     = (clone $apptQ)->whereDate('appointment_date', $today)->count();
        $apptThisMonth = (clone $apptQ)->whereBetween('appointment_date', [$thisMonth, now()])->count();
        $apptCompleted = (clone $apptQ)->whereBetween('appointment_date', [$thisMonth, now()])
            ->where('status', 'completada')->count();
        $apptPending   = (clone $apptQ)->where('status', 'programada')
            ->where('appointment_date', '>=', $today)->count();

        // ── KPI: Pacientes ───────────────────────────────────────────────────
        $totalPatients    = (clone $patQ)->count();
        $newPatientsMonth = (clone $patQ)->whereBetween('created_at', [$thisMonth, now()])->count();
        $patientsWithIns  = (clone $patQ)->whereNotNull('insurance_id')->count();

        // ── Gráfico: ingresos por día (últimos 14 días) ──────────────────────
        $rawRevenue = (clone $invQ)->where('status', 'pagada')
            ->where('created_at', '>=', Carbon::now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')->orderBy('date')
            ->pluck('total', 'date')->toArray();

        $last14Days = [];
        for ($i = 13; $i >= 0; $i--) {
            $d            = Carbon::now()->subDays($i)->format('Y-m-d');
            $last14Days[] = [
                'label' => Carbon::now()->subDays($i)->locale('es')->isoFormat('D MMM'),
                'total' => (float) ($rawRevenue[$d] ?? 0),
            ];
        }

        // ── Gráfico: citas por estado este mes ───────────────────────────────
        $apptByStatus = (clone $apptQ)
            ->whereBetween('appointment_date', [$thisMonth, now()])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();

        // ── Top 5 servicios por ingresos (este mes) ──────────────────────────
        $topServices = DB::table('invoice_items')
            ->join('services',  'invoice_items.service_id',  '=', 'services.id')
            ->join('invoices',  'invoice_items.invoice_id',  '=', 'invoices.id')
            ->when(!$isAdmin, fn($q) => $q->where('invoices.branch_id', $branchId))
            ->where('invoices.created_at', '>=', $thisMonth)
            ->where('invoices.status', '!=', 'cancelada')
            ->selectRaw('services.name, SUM(invoice_items.subtotal) as revenue, SUM(invoice_items.quantity) as qty')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('revenue')
            ->limit(5)->get();

        $maxServiceRevenue = $topServices->max('revenue') ?: 1;

        // ── Citas de hoy ─────────────────────────────────────────────────────
        $todayAppointments = (clone $apptQ)
            ->with(['patient', 'audiologist'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->limit(8)->get();

        // ── Facturas recientes ───────────────────────────────────────────────
        $recentInvoices = (clone $invQ)
            ->with(['patient', 'branch'])
            ->latest()->limit(7)->get();

        // ── Stats por sucursal (solo admin) ──────────────────────────────────
        $branchStats = collect();
        if ($isAdmin) {
            $branchStats = Branch::withCount(['invoices as invoices_month' => fn($q) =>
                    $q->whereBetween('created_at', [$thisMonth, now()])
                ])
                ->withSum(['invoices as revenue_month' => fn($q) =>
                    $q->whereBetween('created_at', [$thisMonth, now()])->where('status', 'pagada')
                ], 'total')
                ->withCount(['appointments as appts_today' => fn($q) =>
                    $q->whereDate('appointment_date', $today)
                ])
                ->withCount(['patients as total_patients'])
                ->orderByDesc('revenue_month')
                ->get();
        }

        // ── Top audiólogos este mes ───────────────────────────────────────────
        $topAudiologists = User::whereHas('role', fn($q) => $q->where('name', 'audiologo'))
            ->when(!$isAdmin, fn($q) => $q->where('branch_id', $branchId))
            ->withCount(['appointments as appts_month' => fn($q) =>
                $q->whereBetween('appointment_date', [$thisMonth, now()])
            ])
            ->withCount(['appointments as appts_completed' => fn($q) =>
                $q->whereBetween('appointment_date', [$thisMonth, now()])->where('status', 'completada')
            ])
            ->orderByDesc('appts_month')
            ->limit(4)->get();

        $maxAppts = $topAudiologists->max('appts_month') ?: 1;

        return view('dashboard', compact(
            'isAdmin',
            'revenueThisMonth', 'revenueLastMonth', 'revenueGrowth',
            'invoicesThisMonth', 'pendingInvoices', 'pendingAmount',
            'apptToday', 'apptThisMonth', 'apptCompleted', 'apptPending',
            'totalPatients', 'newPatientsMonth', 'patientsWithIns',
            'last14Days', 'apptByStatus',
            'topServices', 'maxServiceRevenue',
            'todayAppointments', 'recentInvoices',
            'branchStats', 'topAudiologists', 'maxAppts'
        ));
    }
}