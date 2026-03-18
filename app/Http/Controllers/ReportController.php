<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\ClinicalRecord;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use App\Models\Invoice as InvoiceModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Vista principal de reportes
     */
    public function index()
    {
        $branches     = Branch::orderBy('name')->get();
        $audiologists = User::whereHas('role', fn($q) => $q->where('name', 'audiologo'))
            ->orderBy('name')->get();

        return view('reports.index', compact('branches', 'audiologists'));
    }

    /**
     * JSON — datos de facturación
     */
    public function invoices(Request $request): JsonResponse
    {
        $query = Invoice::query()
            ->when($request->filled('branch_id'),    fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->filled('audiologist_id'), fn($q) => $q->where('audiologist_id', $request->audiologist_id))
            ->when($request->filled('date_from'),    fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),      fn($q) => $q->whereDate('created_at', '<=', $request->date_to));

        // KPIs
        $total      = (clone $query)->count();
        $pagadas    = (clone $query)->where('status', 'pagada')->count();
        $pendientes = (clone $query)->where('status', 'pendiente')->count();
        $canceladas = (clone $query)->where('status', 'cancelada')->count();
        $ingresos   = (clone $query)->where('status', 'pagada')->sum('total');
        $descuentos = (clone $query)->where('status', 'pagada')->sum('insurance_discount');

        // Facturación por mes (últimos 12 meses)
        $byMonth = (clone $query)
            ->where('status', 'pagada')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as total, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        // Por sucursal
        $byBranch = (clone $query)
            ->where('status', 'pagada')
            ->join('branches', 'invoices.branch_id', '=', 'branches.id')
            ->selectRaw('branches.name as branch, SUM(invoices.total) as total, COUNT(*) as count')
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('total')
            ->get();

        // Por seguro
        $byInsurance = (clone $query)
            ->where('status', 'pagada')
            ->whereNotNull('insurance_id')
            ->join('insurances', 'invoices.insurance_id', '=', 'insurances.id')
            ->selectRaw('insurances.name as insurance, SUM(invoices.total) as total, COUNT(*) as count')
            ->groupBy('insurances.id', 'insurances.name')
            ->orderByDesc('total')
            ->get();

        // Servicios más facturados
        $topServices = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('services', 'invoice_items.service_id', '=', 'services.id')
            ->where('invoices.status', 'pagada')
            ->when($request->filled('branch_id'),     fn($q) => $q->where('invoices.branch_id', $request->branch_id))
            ->when($request->filled('audiologist_id'), fn($q) => $q->where('invoices.audiologist_id', $request->audiologist_id))
            ->when($request->filled('date_from'),     fn($q) => $q->whereDate('invoices.created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),       fn($q) => $q->whereDate('invoices.created_at', '<=', $request->date_to))
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

    /**
     * JSON — datos de citas
     */
    public function appointments(Request $request): JsonResponse
    {
        $query = Appointment::query()
            ->when($request->filled('branch_id'),      fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->filled('audiologist_id'), fn($q) => $q->where('audiologist_id', $request->audiologist_id))
            ->when($request->filled('date_from'),      fn($q) => $q->whereDate('appointment_date', '>=', $request->date_from))
            ->when($request->filled('date_to'),        fn($q) => $q->whereDate('appointment_date', '<=', $request->date_to));

        // KPIs
        $total      = (clone $query)->count();
        $completadas= (clone $query)->where('status', 'completada')->count();
        $pendientes = (clone $query)->where('status', 'pendiente')->count();
        $canceladas = (clone $query)->where('status', 'cancelada')->count();
        $confirmadas= (clone $query)->where('status', 'confirmada')->count();

        // Por mes
        $byMonth = (clone $query)
            ->selectRaw("DATE_FORMAT(appointment_date, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        // Por sucursal
        $byBranch = (clone $query)
            ->join('branches', 'appointments.branch_id', '=', 'branches.id')
            ->selectRaw('branches.name as branch, COUNT(*) as count')
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('count')
            ->get();

        // Top audiólogos por citas
        $topAudiologists = (clone $query)
            ->join('users', 'appointments.audiologist_id', '=', 'users.id')
            ->selectRaw('users.name as audiologist, COUNT(*) as count,
                SUM(CASE WHEN appointments.status = "completada" THEN 1 ELSE 0 END) as completadas')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Por día de la semana
        $byWeekday = (clone $query)
            ->selectRaw("DAYOFWEEK(appointment_date) as dow, COUNT(*) as count")
            ->groupBy('dow')
            ->orderBy('dow')
            ->get()
            ->keyBy('dow');

        $weekdays = [];
        $days = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
        for ($i = 1; $i <= 7; $i++) {
            $weekdays[] = [
                'day'   => $days[$i - 1],
                'count' => $byWeekday[$i]->count ?? 0,
            ];
        }

        return response()->json([
            'kpis' => [
                'total'      => $total,
                'completadas'=> $completadas,
                'pendientes' => $pendientes,
                'canceladas' => $canceladas,
                'confirmadas'=> $confirmadas,
                'tasa'       => $total > 0 ? round(($completadas / $total) * 100, 1) : 0,
            ],
            'by_month'        => $byMonth,
            'by_branch'       => $byBranch,
            'top_audiologists'=> $topAudiologists,
            'by_weekday'      => $weekdays,
        ]);
    }

    /**
     * JSON — datos de historias clínicas
     */
    public function clinicalRecords(Request $request): JsonResponse
    {
        $query = ClinicalRecord::query()
            ->when($request->filled('branch_id'),      fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->filled('audiologist_id'), fn($q) => $q->where('audiologist_id', $request->audiologist_id))
            ->when($request->filled('date_from'),      fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),        fn($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $total      = (clone $query)->count();
        $completadas= (clone $query)->where('status', 'completada')->count();
        $pendientes = (clone $query)->where('status', 'pendiente')->count();

        // Top audiólogos por HCs completadas
        $topAudiologists = (clone $query)
            ->where('status', 'completada')
            ->join('users', 'clinical_records.audiologist_id', '=', 'users.id')
            ->selectRaw('users.name as audiologist, COUNT(*) as count')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Por sucursal
        $byBranch = (clone $query)
            ->join('branches', 'clinical_records.branch_id', '=', 'branches.id')
            ->selectRaw('branches.name as branch,
                COUNT(*) as total,
                SUM(CASE WHEN clinical_records.status = "completada" THEN 1 ELSE 0 END) as completadas,
                SUM(CASE WHEN clinical_records.status = "pendiente"  THEN 1 ELSE 0 END) as pendientes')
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('total')
            ->get();

        // Por mes
        $byMonth = (clone $query)
            ->where('status', 'completada')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        return response()->json([
            'kpis' => [
                'total'      => $total,
                'completadas'=> $completadas,
                'pendientes' => $pendientes,
                'tasa'       => $total > 0 ? round(($completadas / $total) * 100, 1) : 0,
            ],
            'top_audiologists'=> $topAudiologists,
            'by_branch'       => $byBranch,
            'by_month'        => $byMonth,
        ]);
    }

    /**
     * JSON — datos de pacientes
     */
    public function patients(Request $request): JsonResponse
    {
        $query = Patient::query()
            ->when($request->filled('branch_id'), fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $total    = (clone $query)->count();
        $conSeguro= (clone $query)->whereNotNull('insurance_id')->count();
        $sinSeguro= $total - $conSeguro;
        $masculino= (clone $query)->where('gender', 'M')->count();
        $femenino = (clone $query)->where('gender', 'F')->count();

        // Nuevos por mes
        $byMonth = (clone $query)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        // Por sucursal
        $byBranch = (clone $query)
            ->join('branches', 'patients.branch_id', '=', 'branches.id')
            ->selectRaw('branches.name as branch, COUNT(*) as count')
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('count')
            ->get();

        // Por seguro
        $byInsurance = (clone $query)
            ->whereNotNull('insurance_id')
            ->join('insurances', 'patients.insurance_id', '=', 'insurances.id')
            ->selectRaw('insurances.name as insurance, COUNT(*) as count')
            ->groupBy('insurances.id', 'insurances.name')
            ->orderByDesc('count')
            ->get();

        // Top pacientes con más facturas
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