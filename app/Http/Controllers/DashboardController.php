<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ProductInvoice;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Branch;
use App\Models\Service;
use App\Models\ClinicalRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';
        $isReceptionist = $user->role->name === 'recepcionista';
        $isDoctor = $user->is_doctor == 1;
        $branchId = $user->branch_id;

        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // ── PRIORIDAD: Si es ADMIN ──────────────────────────────
        if ($isAdmin) {
            return $this->adminDashboard($user, $today, $thisMonth, $lastMonth, $lastMonthEnd, $branchId);
        }

        // ── MÉDICO ──────────────────────────────────────────────
        if ($isDoctor) {
            return $this->doctorDashboard($user, $today, $thisMonth, $lastMonth, $lastMonthEnd);
        }

        // ── RECEPCIONISTA ───────────────────────────────────────
        return $this->receptionistDashboard($user, $today, $thisMonth, $lastMonth, $lastMonthEnd, $branchId);
    }

    /**
     * Dashboard para Administradores
     */
    private function adminDashboard($user, $today, $thisMonth, $lastMonth, $lastMonthEnd, $branchId)
    {
        $isAdmin = true;
        $isReceptionist = false;
        $isDoctor = false;

        $invQ = Invoice::query();
        $prodInvQ = ProductInvoice::query();
        $apptQ = Appointment::query();
        $patQ = Patient::query();

        // ═══════════════════════════════════════════════════════
        // INGRESOS DE SERVICIOS
        // ═══════════════════════════════════════════════════════
        $revenueThisMonth = (clone $invQ)->where('status', 'pagada')
            ->whereBetween('created_at', [$thisMonth, now()])->sum('total');

        $revenueLastMonth = (clone $invQ)->where('status', 'pagada')
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->sum('total');

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        // ═══════════════════════════════════════════════════════
        // INGRESOS DE PRODUCTOS
        // ═══════════════════════════════════════════════════════
        // Ingresos de productos = suma de lo COBRADO (paid_amount) del mes
        $productRevenueThisMonth = (clone $prodInvQ)
            ->whereBetween('created_at', [$thisMonth, now()])
            ->where('status', '!=', 'cancelada')
            ->sum('paid_amount');

        $productRevenueLastMonth = (clone $prodInvQ)
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->where('status', '!=', 'cancelada')
            ->sum('paid_amount');

        $productRevenueGrowth = $productRevenueLastMonth > 0
            ? round((($productRevenueThisMonth - $productRevenueLastMonth) / $productRevenueLastMonth) * 100, 1)
            : ($productRevenueThisMonth > 0 ? 100 : 0);

        // Facturado (pendiente de cobro)
        $productInvoicedThisMonth = (clone $prodInvQ)
            ->whereBetween('created_at', [$thisMonth, now()])
            ->where('status', '!=', 'cancelada')
            ->sum('total');

        $productPendingInvoices = (clone $prodInvQ)
            ->whereIn('status', ['pendiente', 'pagada_parcial'])
            ->count();

        $productPendingAmount = (clone $prodInvQ)
            ->whereIn('status', ['pendiente', 'pagada_parcial'])
            ->sum('balance');

        $productInvoicesThisMonth = (clone $prodInvQ)
            ->whereBetween('created_at', [$thisMonth, now()])
            ->where('status', '!=', 'cancelada')
            ->count();

        // Total ingresos combinados (servicios + productos cobrados)
        $totalRevenueThisMonth = $revenueThisMonth + $productRevenueThisMonth;

        // ═══════════════════════════════════════════════════════
        // FACTURAS DE SERVICIOS
        // ═══════════════════════════════════════════════════════
        $invoicesThisMonth = (clone $invQ)->whereBetween('created_at', [$thisMonth, now()])->count();
        $pendingInvoices = (clone $invQ)->where('status', 'pendiente')->count();
        $pendingAmount = (clone $invQ)->where('status', 'pendiente')->sum('total');

        // ═══════════════════════════════════════════════════════
        // CITAS
        // ═══════════════════════════════════════════════════════
        $apptToday = (clone $apptQ)->whereDate('appointment_date', $today)->count();
        $apptThisMonth = (clone $apptQ)->whereBetween('appointment_date', [$thisMonth, now()])->count();
        $apptCompleted = (clone $apptQ)->whereBetween('appointment_date', [$thisMonth, now()])
            ->where('status', 'completada')->count();
        $apptPending = (clone $apptQ)->where('status', 'programada')
            ->where('appointment_date', '>=', $today)->count();

        // ═══════════════════════════════════════════════════════
        // PACIENTES
        // ═══════════════════════════════════════════════════════
        $totalPatients = (clone $patQ)->count();
        $newPatientsMonth = (clone $patQ)->whereBetween('created_at', [$thisMonth, now()])->count();
        $patientsWithIns = (clone $patQ)->whereNotNull('insurance_id')->count();

        // ═══════════════════════════════════════════════════════
        // GRÁFICO: Ingresos últimos 14 días (servicios + productos)
        // ═══════════════════════════════════════════════════════
        $rawServiceRevenue = (clone $invQ)->where('status', 'pagada')
            ->where('created_at', '>=', Carbon::now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')->orderBy('date')
            ->pluck('total', 'date')->toArray();

        $rawProductRevenue = (clone $prodInvQ)
            ->where('status', '!=', 'cancelada')
            ->where('created_at', '>=', Carbon::now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(paid_amount) as total')
            ->groupBy('date')->orderBy('date')
            ->pluck('total', 'date')->toArray();

        $last14Days = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->format('Y-m-d');
            $last14Days[] = [
                'label'    => Carbon::now()->subDays($i)->locale('es')->isoFormat('D MMM'),
                'services' => (float) ($rawServiceRevenue[$d] ?? 0),
                'products' => (float) ($rawProductRevenue[$d] ?? 0),
                'total'    => (float) (($rawServiceRevenue[$d] ?? 0) + ($rawProductRevenue[$d] ?? 0)),
            ];
        }

        // ═══════════════════════════════════════════════════════
        // GRÁFICO: Citas por estado
        // ═══════════════════════════════════════════════════════
        $apptByStatus = (clone $apptQ)
            ->whereBetween('appointment_date', [$thisMonth, now()])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();

        // ═══════════════════════════════════════════════════════
        // TOP 5 SERVICIOS
        // ═══════════════════════════════════════════════════════
        $topServices = DB::table('invoice_items')
            ->join('services', 'invoice_items.service_id', '=', 'services.id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.created_at', '>=', $thisMonth)
            ->where('invoices.status', '!=', 'cancelada')
            ->selectRaw('services.name, SUM(invoice_items.subtotal) as revenue, SUM(invoice_items.quantity) as qty')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('revenue')
            ->limit(5)->get();

        $maxServiceRevenue = $topServices->max('revenue') ?: 1;

        // ═══════════════════════════════════════════════════════
        // TOP 5 PRODUCTOS MÁS VENDIDOS (por ingresos)
        // ═══════════════════════════════════════════════════════
        $topProducts = DB::table('product_invoice_items')
            ->join('products', 'product_invoice_items.product_id', '=', 'products.id')
            ->join('product_invoices', 'product_invoice_items.product_invoice_id', '=', 'product_invoices.id')
            ->where('product_invoices.created_at', '>=', $thisMonth)
            ->where('product_invoices.status', '!=', 'cancelada')
            ->selectRaw('
                products.id,
                products.code,
                products.name,
                SUM(product_invoice_items.quantity) as qty,
                SUM(product_invoice_items.total_with_tax) as revenue
            ')
            ->groupBy('products.id', 'products.code', 'products.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $maxProductRevenue = $topProducts->max('revenue') ?: 1;

        // ═══════════════════════════════════════════════════════
        // CITAS DE HOY
        // ═══════════════════════════════════════════════════════
        $todayAppointments = (clone $apptQ)
            ->with(['patient', 'doctor'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->limit(8)->get();

        // ═══════════════════════════════════════════════════════
        // FACTURAS RECIENTES (servicios)
        // ═══════════════════════════════════════════════════════
        $recentInvoices = (clone $invQ)
            ->with(['patient', 'branch'])
            ->latest()->limit(7)->get();

        // ═══════════════════════════════════════════════════════
        // FACTURAS DE PRODUCTOS RECIENTES
        // ═══════════════════════════════════════════════════════
        $recentProductInvoices = (clone $prodInvQ)
            ->with(['patient', 'branch'])
            ->latest()->limit(7)->get();

        // ═══════════════════════════════════════════════════════
        // STATS POR SUCURSAL
        // ═══════════════════════════════════════════════════════
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

        // ═══════════════════════════════════════════════════════
        // TOP MÉDICOS
        // ═══════════════════════════════════════════════════════
        $topDoctors = User::where('is_doctor', 1)
            ->withCount(['appointments as appts_month' => fn($q) =>
                $q->whereBetween('appointment_date', [$thisMonth, now()])
            ])
            ->withCount(['appointments as appts_completed' => fn($q) =>
                $q->whereBetween('appointment_date', [$thisMonth, now()])->where('status', 'completada')
            ])
            ->orderByDesc('appts_month')
            ->limit(4)->get();

        $maxAppts = $topDoctors->max('appts_month') ?: 1;

        // ═══════════════════════════════════════════════════════
        // HISTORIAS CLÍNICAS
        // ═══════════════════════════════════════════════════════
        $clinicalRecords = ClinicalRecord::count();
        $clinicalRecordsMonth = ClinicalRecord::whereBetween('created_at', [$thisMonth, now()])->count();

        return view('dashboard', compact(
            'user', 'isAdmin', 'isReceptionist', 'isDoctor',
            // Servicios
            'revenueThisMonth', 'revenueLastMonth', 'revenueGrowth',
            'invoicesThisMonth', 'pendingInvoices', 'pendingAmount',
            // Productos
            'productRevenueThisMonth', 'productRevenueLastMonth', 'productRevenueGrowth',
            'productInvoicedThisMonth', 'productPendingInvoices', 'productPendingAmount',
            'productInvoicesThisMonth', 'totalRevenueThisMonth',
            // Citas
            'apptToday', 'apptThisMonth', 'apptCompleted', 'apptPending',
            'apptByStatus', 'todayAppointments',
            // Pacientes
            'totalPatients', 'newPatientsMonth', 'patientsWithIns',
            // Gráficos y tops
            'last14Days', 'topServices', 'maxServiceRevenue',
            'topProducts', 'maxProductRevenue',
            // Facturas recientes
            'recentInvoices', 'recentProductInvoices',
            // Extras
            'branchStats', 'topDoctors', 'maxAppts',
            'clinicalRecords', 'clinicalRecordsMonth'
        ));
    }

    /**
     * Dashboard para Recepcionistas
     */
    private function receptionistDashboard($user, $today, $thisMonth, $lastMonth, $lastMonthEnd, $branchId)
    {
        $isAdmin = false;
        $isReceptionist = true;
        $isDoctor = false;

        $invQ = Invoice::where('branch_id', $branchId);
        $prodInvQ = ProductInvoice::where('branch_id', $branchId);
        $apptQ = Appointment::where('branch_id', $branchId);
        $patQ = Patient::where('branch_id', $branchId);

        // ═══════════════════════════════════════════════════════
        // INGRESOS DE SERVICIOS
        // ═══════════════════════════════════════════════════════
        $revenueThisMonth = (clone $invQ)->where('status', 'pagada')
            ->whereBetween('created_at', [$thisMonth, now()])->sum('total');

        $revenueLastMonth = (clone $invQ)->where('status', 'pagada')
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->sum('total');

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        // ═══════════════════════════════════════════════════════
        // INGRESOS DE PRODUCTOS
        // ═══════════════════════════════════════════════════════
        $productRevenueThisMonth = (clone $prodInvQ)
            ->whereBetween('created_at', [$thisMonth, now()])
            ->where('status', '!=', 'cancelada')
            ->sum('paid_amount');

        $productRevenueLastMonth = (clone $prodInvQ)
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->where('status', '!=', 'cancelada')
            ->sum('paid_amount');

        $productRevenueGrowth = $productRevenueLastMonth > 0
            ? round((($productRevenueThisMonth - $productRevenueLastMonth) / $productRevenueLastMonth) * 100, 1)
            : ($productRevenueThisMonth > 0 ? 100 : 0);

        $productPendingInvoices = (clone $prodInvQ)
            ->whereIn('status', ['pendiente', 'pagada_parcial'])
            ->count();

        $productPendingAmount = (clone $prodInvQ)
            ->whereIn('status', ['pendiente', 'pagada_parcial'])
            ->sum('balance');

        $totalRevenueThisMonth = $revenueThisMonth + $productRevenueThisMonth;

        // ═══════════════════════════════════════════════════════
        // FACTURAS DE SERVICIOS
        // ═══════════════════════════════════════════════════════
        $invoicesThisMonth = (clone $invQ)->whereBetween('created_at', [$thisMonth, now()])->count();
        $pendingInvoices = (clone $invQ)->where('status', 'pendiente')->count();
        $pendingAmount = (clone $invQ)->where('status', 'pendiente')->sum('total');

        // ═══════════════════════════════════════════════════════
        // CITAS
        // ═══════════════════════════════════════════════════════
        $apptToday = (clone $apptQ)->whereDate('appointment_date', $today)->count();
        $apptThisMonth = (clone $apptQ)->whereBetween('appointment_date', [$thisMonth, now()])->count();
        $apptCompleted = (clone $apptQ)->whereBetween('appointment_date', [$thisMonth, now()])
            ->where('status', 'completada')->count();
        $apptPending = (clone $apptQ)->where('status', 'programada')
            ->where('appointment_date', '>=', $today)->count();

        // ═══════════════════════════════════════════════════════
        // PACIENTES
        // ═══════════════════════════════════════════════════════
        $totalPatients = (clone $patQ)->count();
        $newPatientsMonth = (clone $patQ)->whereBetween('created_at', [$thisMonth, now()])->count();
        $patientsWithIns = (clone $patQ)->whereNotNull('insurance_id')->count();

        // ═══════════════════════════════════════════════════════
        // GRÁFICO: Ingresos últimos 14 días
        // ═══════════════════════════════════════════════════════
        $rawServiceRevenue = (clone $invQ)->where('status', 'pagada')
            ->where('created_at', '>=', Carbon::now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')->orderBy('date')
            ->pluck('total', 'date')->toArray();

        $rawProductRevenue = (clone $prodInvQ)
            ->where('status', '!=', 'cancelada')
            ->where('created_at', '>=', Carbon::now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(paid_amount) as total')
            ->groupBy('date')->orderBy('date')
            ->pluck('total', 'date')->toArray();

        $last14Days = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->format('Y-m-d');
            $last14Days[] = [
                'label'    => Carbon::now()->subDays($i)->locale('es')->isoFormat('D MMM'),
                'services' => (float) ($rawServiceRevenue[$d] ?? 0),
                'products' => (float) ($rawProductRevenue[$d] ?? 0),
                'total'    => (float) (($rawServiceRevenue[$d] ?? 0) + ($rawProductRevenue[$d] ?? 0)),
            ];
        }

        // ═══════════════════════════════════════════════════════
        // GRÁFICO: Citas por estado
        // ═══════════════════════════════════════════════════════
        $apptByStatus = (clone $apptQ)
            ->whereBetween('appointment_date', [$thisMonth, now()])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();

        // ═══════════════════════════════════════════════════════
        // TOP 5 SERVICIOS
        // ═══════════════════════════════════════════════════════
        $topServices = DB::table('invoice_items')
            ->join('services', 'invoice_items.service_id', '=', 'services.id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.branch_id', $branchId)
            ->where('invoices.created_at', '>=', $thisMonth)
            ->where('invoices.status', '!=', 'cancelada')
            ->selectRaw('services.name, SUM(invoice_items.subtotal) as revenue, SUM(invoice_items.quantity) as qty')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('revenue')
            ->limit(5)->get();

        $maxServiceRevenue = $topServices->max('revenue') ?: 1;

        // ═══════════════════════════════════════════════════════
        // TOP 5 PRODUCTOS VENDIDOS (de la sucursal)
        // ═══════════════════════════════════════════════════════
        $topProducts = DB::table('product_invoice_items')
            ->join('products', 'product_invoice_items.product_id', '=', 'products.id')
            ->join('product_invoices', 'product_invoice_items.product_invoice_id', '=', 'product_invoices.id')
            ->where('product_invoices.branch_id', $branchId)
            ->where('product_invoices.created_at', '>=', $thisMonth)
            ->where('product_invoices.status', '!=', 'cancelada')
            ->selectRaw('
                products.id,
                products.code,
                products.name,
                SUM(product_invoice_items.quantity) as qty,
                SUM(product_invoice_items.total_with_tax) as revenue
            ')
            ->groupBy('products.id', 'products.code', 'products.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $maxProductRevenue = $topProducts->max('revenue') ?: 1;

        // ═══════════════════════════════════════════════════════
        // CITAS DE HOY
        // ═══════════════════════════════════════════════════════
        $todayAppointments = (clone $apptQ)
            ->with(['patient', 'doctor'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->limit(8)->get();

        // ═══════════════════════════════════════════════════════
        // FACTURAS RECIENTES
        // ═══════════════════════════════════════════════════════
        $recentInvoices = (clone $invQ)
            ->with(['patient', 'branch'])
            ->latest()->limit(7)->get();

        $recentProductInvoices = (clone $prodInvQ)
            ->with(['patient', 'branch'])
            ->latest()->limit(7)->get();

        // ═══════════════════════════════════════════════════════
        // TOP MÉDICOS DE LA SUCURSAL
        // ═══════════════════════════════════════════════════════
        $topDoctors = User::where('is_doctor', 1)
            ->where('branch_id', $branchId)
            ->withCount(['appointments as appts_month' => fn($q) =>
                $q->whereBetween('appointment_date', [$thisMonth, now()])
            ])
            ->withCount(['appointments as appts_completed' => fn($q) =>
                $q->whereBetween('appointment_date', [$thisMonth, now()])->where('status', 'completada')
            ])
            ->orderByDesc('appts_month')
            ->limit(4)->get();

        $maxAppts = $topDoctors->max('appts_month') ?: 1;

        // Historias clínicas (0 para recepcionista)
        $clinicalRecords = 0;
        $clinicalRecordsMonth = 0;

        return view('dashboard', compact(
            'user', 'isAdmin', 'isReceptionist', 'isDoctor',
            // Servicios
            'revenueThisMonth', 'revenueLastMonth', 'revenueGrowth',
            'invoicesThisMonth', 'pendingInvoices', 'pendingAmount',
            // Productos
            'productRevenueThisMonth', 'productRevenueLastMonth', 'productRevenueGrowth',
            'productPendingInvoices', 'productPendingAmount',
            'totalRevenueThisMonth',
            // Citas
            'apptToday', 'apptThisMonth', 'apptCompleted', 'apptPending',
            'apptByStatus', 'todayAppointments',
            // Pacientes
            'totalPatients', 'newPatientsMonth', 'patientsWithIns',
            // Gráficos y tops
            'last14Days', 'topServices', 'maxServiceRevenue',
            'topProducts', 'maxProductRevenue',
            // Facturas recientes
            'recentInvoices', 'recentProductInvoices',
            // Extras
            'topDoctors', 'maxAppts',
            'clinicalRecords', 'clinicalRecordsMonth'
        ));
    }

    /**
     * Dashboard para Médicos
     */
    private function doctorDashboard($user, $today, $thisMonth, $lastMonth, $lastMonthEnd)
    {
        $isDoctor = true;
        $isAdmin = false;
        $isReceptionist = false;

        // ── Citas ──────────────────────────────────────────────
        $apptToday = Appointment::where('doctor_id', $user->id)
            ->whereDate('appointment_date', $today)->count();

        $apptThisMonth = Appointment::where('doctor_id', $user->id)
            ->whereBetween('appointment_date', [$thisMonth, now()])->count();

        $apptCompleted = Appointment::where('doctor_id', $user->id)
            ->whereBetween('appointment_date', [$thisMonth, now()])
            ->where('status', 'completada')->count();

        $apptPending = Appointment::where('doctor_id', $user->id)
            ->where('status', 'programada')
            ->where('appointment_date', '>=', $today)->count();

        // ── Pacientes ──────────────────────────────────────────
        $patientsAttended = Appointment::where('doctor_id', $user->id)
            ->where('status', 'completada')
            ->distinct('patient_id')->count('patient_id');

        $newPatientsMonth = Appointment::where('doctor_id', $user->id)
            ->whereBetween('appointment_date', [$thisMonth, now()])
            ->where('status', 'completada')
            ->distinct('patient_id')->count('patient_id');

        // ── Historias Clínicas ─────────────────────────────────
        $clinicalRecords = ClinicalRecord::where('doctor_id', $user->id)->count();

        $clinicalRecordsMonth = ClinicalRecord::where('doctor_id', $user->id)
            ->whereBetween('consultation_date', [$thisMonth, now()])->count();

        // ── Citas de hoy ───────────────────────────────────────
        $todayAppointments = Appointment::where('doctor_id', $user->id)
            ->whereDate('appointment_date', $today)
            ->with(['patient', 'branch'])
            ->orderBy('appointment_time')
            ->limit(8)->get();

        // ── Próximas citas ─────────────────────────────────────
        $upcomingAppointments = Appointment::where('doctor_id', $user->id)
            ->where('appointment_date', '>=', $today)
            ->where('status', 'programada')
            ->with(['patient', 'branch'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(5)->get();

        // ── Pacientes recientes ────────────────────────────────
        $recentPatients = Appointment::where('doctor_id', $user->id)
            ->where('status', 'completada')
            ->with(['patient', 'branch'])
            ->orderBy('updated_at', 'desc')
            ->limit(7)->get();

        // ── Citas por estado ───────────────────────────────────
        $apptByStatus = Appointment::where('doctor_id', $user->id)
            ->whereBetween('appointment_date', [$thisMonth, now()])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();

        // ── Top servicios ──────────────────────────────────────
        $topServices = DB::table('appointment_service')
            ->join('appointments', 'appointment_service.appointment_id', '=', 'appointments.id')
            ->join('services', 'appointment_service.service_id', '=', 'services.id')
            ->where('appointments.doctor_id', $user->id)
            ->whereBetween('appointments.appointment_date', [$thisMonth, now()])
            ->where('appointments.status', 'completada')
            ->selectRaw('services.name, COUNT(appointment_service.service_id) as total')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total')
            ->limit(5)->get();

        $maxServices = $topServices->max('total') ?: 1;

        // ── Citas completadas por día ──────────────────────────
        $appointmentsByDay = Appointment::where('doctor_id', $user->id)
            ->where('appointment_date', '>=', Carbon::now()->subDays(6))
            ->where('status', 'completada')
            ->selectRaw('DATE(appointment_date) as date, COUNT(*) as count')
            ->groupBy('date')->orderBy('date')
            ->pluck('count', 'date')->toArray();

        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->format('Y-m-d');
            $last7Days[] = [
                'label' => Carbon::now()->subDays($i)->locale('es')->isoFormat('D MMM'),
                'total' => (int) ($appointmentsByDay[$d] ?? 0),
            ];
        }

        return view('dashboard-doctor', compact(
            'user', 'isDoctor', 'isAdmin', 'isReceptionist',
            'apptToday', 'apptThisMonth', 'apptCompleted', 'apptPending',
            'patientsAttended', 'newPatientsMonth',
            'clinicalRecords', 'clinicalRecordsMonth',
            'todayAppointments', 'upcomingAppointments', 'recentPatients',
            'apptByStatus', 'topServices', 'maxServices', 'last7Days'
        ));
    }
}