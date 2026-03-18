<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\ClinicalRecord;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $branches     = Branch::orderBy('name')->get();
        $audiologists = User::whereHas('role', fn($q) => $q->where('name', 'audiologo'))
            ->orderBy('name')->get();

        return view('reports.index', compact('branches', 'audiologists'));
    }

    public function invoices(Request $request): JsonResponse
    {
        $query = Invoice::query()
            ->when($request->filled('branch_id'),      fn($q) => $q->where('invoices.branch_id', $request->branch_id))
            ->when($request->filled('audiologist_id'), fn($q) => $q->where('invoices.audiologist_id', $request->audiologist_id))
            ->when($request->filled('date_from'),      fn($q) => $q->whereDate('invoices.created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),        fn($q) => $q->whereDate('invoices.created_at', '<=', $request->date_to));

        $total      = (clone $query)->count();
        $pagadas    = (clone $query)->where('invoices.status', 'pagada')->count();
        $pendientes = (clone $query)->where('invoices.status', 'pendiente')->count();
        $canceladas = (clone $query)->where('invoices.status', 'cancelada')->count();
        $ingresos   = (clone $query)->where('invoices.status', 'pagada')->sum('invoices.total');
        $descuentos = (clone $query)->where('invoices.status', 'pagada')->sum('invoices.insurance_discount');

        $byMonth = (clone $query)
            ->where('invoices.status', 'pagada')
            ->selectRaw("DATE_FORMAT(invoices.created_at, '%Y-%m') as month, SUM(invoices.total) as total, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        // JOIN queries usan DB::table para evitar ambigüedad
        $byBranch = DB::table('invoices')
            ->join('branches', 'invoices.branch_id', '=', 'branches.id')
            ->where('invoices.status', 'pagada')
            ->when($request->filled('branch_id'),      fn($q) => $q->where('invoices.branch_id', $request->branch_id))
            ->when($request->filled('audiologist_id'), fn($q) => $q->where('invoices.audiologist_id', $request->audiologist_id))
            ->when($request->filled('date_from'),      fn($q) => $q->whereDate('invoices.created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),        fn($q) => $q->whereDate('invoices.created_at', '<=', $request->date_to))
            ->selectRaw('branches.name as branch, SUM(invoices.total) as total, COUNT(*) as count')
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('total')
            ->get();

        $byInsurance = DB::table('invoices')
            ->join('insurances', 'invoices.insurance_id', '=', 'insurances.id')
            ->where('invoices.status', 'pagada')
            ->whereNotNull('invoices.insurance_id')
            ->when($request->filled('branch_id'),      fn($q) => $q->where('invoices.branch_id', $request->branch_id))
            ->when($request->filled('audiologist_id'), fn($q) => $q->where('invoices.audiologist_id', $request->audiologist_id))
            ->when($request->filled('date_from'),      fn($q) => $q->whereDate('invoices.created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),        fn($q) => $q->whereDate('invoices.created_at', '<=', $request->date_to))
            ->selectRaw('insurances.name as insurance, SUM(invoices.total) as total, COUNT(*) as count')
            ->groupBy('insurances.id', 'insurances.name')
            ->orderByDesc('total')
            ->get();

        $topServices = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('services', 'invoice_items.service_id', '=', 'services.id')
            ->where('invoices.status', 'pagada')
            ->when($request->filled('branch_id'),      fn($q) => $q->where('invoices.branch_id', $request->branch_id))
            ->when($request->filled('audiologist_id'), fn($q) => $q->where('invoices.audiologist_id', $request->audiologist_id))
            ->when($request->filled('date_from'),      fn($q) => $q->whereDate('invoices.created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),        fn($q) => $q->whereDate('invoices.created_at', '<=', $request->date_to))
            ->selectRaw('services.name as service, SUM(invoice_items.quantity) as qty, SUM(invoice_items.subtotal) as total')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('qty')
            ->limit(8)
            ->get();

        return response()->json([
            'kpis' => [
                'total'      => $total,
                'pagadas'    => $pagadas,
                'pendientes' => $pendientes,
                'canceladas' => $canceladas,
                'ingresos'   => number_format($ingresos, 2),
                'descuentos' => number_format($descuentos, 2),
            ],
            'by_month'    => $byMonth,
            'by_branch'   => $byBranch,
            'by_insurance'=> $byInsurance,
            'top_services'=> $topServices,
        ]);
    }

    public function appointments(Request $request): JsonResponse
    {
        // ── KPIs — todo via DB::table con prefijos explícitos ──
        $baseFilters = function($q) use ($request) {
            $q->when($request->filled('branch_id'),      fn($q) => $q->where('appointments.branch_id', $request->branch_id))
              ->when($request->filled('audiologist_id'), fn($q) => $q->where('appointments.audiologist_id', $request->audiologist_id))
              ->when($request->filled('date_from'),      fn($q) => $q->whereDate('appointments.appointment_date', '>=', $request->date_from))
              ->when($request->filled('date_to'),        fn($q) => $q->whereDate('appointments.appointment_date', '<=', $request->date_to));
        };

        $total       = DB::table('appointments')->tap($baseFilters)->count();
        $completadas = DB::table('appointments')->tap($baseFilters)->where('appointments.status', 'completada')->count();
        $programadas = DB::table('appointments')->tap($baseFilters)->where('appointments.status', 'programada')->count();
        $canceladas  = DB::table('appointments')->tap($baseFilters)->where('appointments.status', 'cancelada')->count();

        // Por mes
        $byMonth = DB::table('appointments')
            ->tap($baseFilters)
            ->selectRaw("DATE_FORMAT(appointments.appointment_date, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        // Por sucursal (donut en vista)
        $byBranch = DB::table('appointments')
            ->join('branches', 'appointments.branch_id', '=', 'branches.id')
            ->tap($baseFilters)
            ->selectRaw('branches.name as branch, COUNT(*) as count')
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('count')
            ->get();

        // Top audiólogos
        $topAudiologists = DB::table('appointments')
            ->join('users', 'appointments.audiologist_id', '=', 'users.id')
            ->tap($baseFilters)
            ->selectRaw('users.name as audiologist, COUNT(*) as count,
                SUM(CASE WHEN appointments.status = "completada" THEN 1 ELSE 0 END) as completadas')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Por día de la semana
        $byWeekdayRaw = DB::table('appointments')
            ->tap($baseFilters)
            ->selectRaw("DAYOFWEEK(appointments.appointment_date) as dow, COUNT(*) as count")
            ->groupBy('dow')
            ->orderBy('dow')
            ->get()
            ->keyBy('dow');

        $days     = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
        $weekdays = [];
        for ($i = 1; $i <= 7; $i++) {
            $weekdays[] = ['day' => $days[$i-1], 'count' => $byWeekdayRaw[$i]->count ?? 0];
        }

        return response()->json([
            'kpis' => [
                'total'       => $total,
                'completadas' => $completadas,
                'programadas' => $programadas,
                'canceladas'  => $canceladas,
                'tasa'        => $total > 0 ? round(($completadas / $total) * 100, 1) : 0,
            ],
            'by_month'        => $byMonth,
            'by_branch'       => $byBranch,
            'top_audiologists'=> $topAudiologists,
            'by_weekday'      => $weekdays,
        ]);
    }

    public function clinicalRecords(Request $request): JsonResponse
    {
        $baseFilters = function($q) use ($request) {
            $q->when($request->filled('branch_id'),      fn($q) => $q->where('clinical_records.branch_id', $request->branch_id))
              ->when($request->filled('audiologist_id'), fn($q) => $q->where('clinical_records.audiologist_id', $request->audiologist_id))
              ->when($request->filled('date_from'),      fn($q) => $q->whereDate('clinical_records.created_at', '>=', $request->date_from))
              ->when($request->filled('date_to'),        fn($q) => $q->whereDate('clinical_records.created_at', '<=', $request->date_to));
        };

        $total       = DB::table('clinical_records')->tap($baseFilters)->count();
        $completadas = DB::table('clinical_records')->tap($baseFilters)->where('clinical_records.status', 'completada')->count();
        $pendientes  = DB::table('clinical_records')->tap($baseFilters)->where('clinical_records.status', 'pendiente')->count();

        $topAudiologists = DB::table('clinical_records')
            ->join('users', 'clinical_records.audiologist_id', '=', 'users.id')
            ->tap($baseFilters)
            ->where('clinical_records.status', 'completada')
            ->selectRaw('users.name as audiologist, COUNT(*) as count')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $byBranch = DB::table('clinical_records')
            ->join('branches', 'clinical_records.branch_id', '=', 'branches.id')
            ->tap($baseFilters)
            ->selectRaw('branches.name as branch,
                COUNT(*) as total,
                SUM(CASE WHEN clinical_records.status = "completada" THEN 1 ELSE 0 END) as completadas,
                SUM(CASE WHEN clinical_records.status = "pendiente"  THEN 1 ELSE 0 END) as pendientes')
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('total')
            ->get();

        $byMonth = DB::table('clinical_records')
            ->tap($baseFilters)
            ->where('clinical_records.status', 'completada')
            ->selectRaw("DATE_FORMAT(clinical_records.created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        return response()->json([
            'kpis' => [
                'total'       => $total,
                'completadas' => $completadas,
                'pendientes'  => $pendientes,
                'tasa'        => $total > 0 ? round(($completadas / $total) * 100, 1) : 0,
            ],
            'top_audiologists'=> $topAudiologists,
            'by_branch'       => $byBranch,
            'by_month'        => $byMonth,
        ]);
    }

    public function patients(Request $request): JsonResponse
    {
        $baseFilters = function($q) use ($request) {
            $q->when($request->filled('branch_id'), fn($q) => $q->where('patients.branch_id', $request->branch_id))
              ->when($request->filled('date_from'), fn($q) => $q->whereDate('patients.created_at', '>=', $request->date_from))
              ->when($request->filled('date_to'),   fn($q) => $q->whereDate('patients.created_at', '<=', $request->date_to));
        };

        $total     = DB::table('patients')->tap($baseFilters)->count();
        $conSeguro = DB::table('patients')->tap($baseFilters)->whereNotNull('patients.insurance_id')->count();
        $sinSeguro = $total - $conSeguro;
        $masculino = DB::table('patients')->tap($baseFilters)->where('patients.gender', 'M')->count();
        $femenino  = DB::table('patients')->tap($baseFilters)->where('patients.gender', 'F')->count();

        $byMonth = DB::table('patients')
            ->tap($baseFilters)
            ->selectRaw("DATE_FORMAT(patients.created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        $byBranch = DB::table('patients')
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->tap($baseFilters)
            ->selectRaw('branches.name as branch, COUNT(*) as count')
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('count')
            ->get();

        $byInsurance = DB::table('patients')
            ->join('insurances', 'patients.insurance_id', '=', 'insurances.id')
            ->tap($baseFilters)
            ->whereNotNull('patients.insurance_id')
            ->selectRaw('insurances.name as insurance, COUNT(*) as count')
            ->groupBy('insurances.id', 'insurances.name')
            ->orderByDesc('count')
            ->get();

        $topPatients = DB::table('invoices')
            ->join('patients', 'invoices.patient_id', '=', 'patients.id')
            ->where('invoices.status', 'pagada')
            ->when($request->filled('branch_id'), fn($q) => $q->where('invoices.branch_id', $request->branch_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('invoices.created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('invoices.created_at', '<=', $request->date_to))
            ->selectRaw("CONCAT(patients.first_name,' ',patients.last_name) as patient,
                patients.cedula, COUNT(*) as visits, SUM(invoices.total) as total")
            ->groupBy('patients.id', 'patients.first_name', 'patients.last_name', 'patients.cedula')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        return response()->json([
            'kpis' => [
                'total'     => $total,
                'conSeguro' => $conSeguro,
                'sinSeguro' => $sinSeguro,
                'masculino' => $masculino,
                'femenino'  => $femenino,
            ],
            'by_month'    => $byMonth,
            'by_branch'   => $byBranch,
            'by_insurance'=> $byInsurance,
            'top_patients'=> $topPatients,
        ]);
    }
}