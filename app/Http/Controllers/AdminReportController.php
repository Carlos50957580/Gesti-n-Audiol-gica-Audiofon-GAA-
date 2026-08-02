<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Receipt;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Branch;
use App\Models\Insurance;
use App\Models\DoctorFee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Reporte de Facturación
     */
    public function invoices(Request $request)
    {
        $query = Invoice::with(['patient', 'branch', 'insurance', 'user', 'doctor']);

        // Filtros
        $this->applyFilters($query, $request);

        $invoices = $query->latest()->paginate(50);

        // Estadísticas
        $stats = $this->getInvoiceStats($query, $request);

        // Métodos de pago
        $paymentMethods = $this->getPaymentMethods($request);

        // Seguros
        $insurances = $this->getInsuranceStats($request);

        // Facturación por recepcionista
        $recepcionistas = $this->getRecepcionistaStats($request);

        // Sucursales
        $branches = Branch::orderBy('name')->get();

        // Doctores
        $doctors = User::where('is_doctor', 1)->orderBy('name')->get();

        // Usuarios (recepcionistas)
        $users = User::where('role_id', 2)->orderBy('name')->get();

        // Seguros para el filtro
        $insuranceList = Insurance::where('active', 1)->orderBy('name')->get();

        return view('admin.reports.invoices', compact(
            'invoices', 'stats', 'paymentMethods', 'insurances', 'branches', 'doctors',
            'users', 'insuranceList', 'recepcionistas'
        ));
    }

    /**
     * Reporte de Citas
     */
    public function appointments(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'branch']);

        if ($request->filled('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $appointments = $query->latest()->paginate(50);

        $stats = [
            'total' => (clone $query)->count(),
            'programadas' => (clone $query)->where('status', 'programada')->count(),
            'completadas' => (clone $query)->where('status', 'completada')->count(),
            'canceladas' => (clone $query)->where('status', 'cancelada')->count(),
            'completion_rate' => $this->calculateCompletionRate($query),
        ];

        $appointmentsByDay = $this->getAppointmentsByDay($query);
        $topDoctors = $this->getTopDoctors($query);

        $branches = Branch::orderBy('name')->get();
        $doctors = User::where('is_doctor', 1)->orderBy('name')->get();

        return view('admin.reports.appointments', compact(
            'appointments', 'stats', 'appointmentsByDay', 'topDoctors', 'branches', 'doctors'
        ));
    }

    /**
     * Reporte de Pacientes
     */
    public function patients(Request $request)
    {
        $query = Patient::with(['branch', 'insurance']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('has_insurance')) {
            if ($request->has_insurance == '1') {
                $query->whereNotNull('insurance_id');
            } else {
                $query->whereNull('insurance_id');
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('cedula', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(50);

        $stats = [
            'total' => (clone $query)->count(),
            'with_insurance' => (clone $query)->whereNotNull('insurance_id')->count(),
            'without_insurance' => (clone $query)->whereNull('insurance_id')->count(),
            'male' => (clone $query)->where('gender', 'M')->count(),
            'female' => (clone $query)->where('gender', 'F')->count(),
            'new_this_month' => Patient::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $patientsByMonth = $this->getPatientsByMonth($query);
        $topInsurances = $this->getTopInsurances($query);

        $branches = Branch::orderBy('name')->get();

        return view('admin.reports.patients', compact(
            'patients', 'stats', 'patientsByMonth', 'topInsurances', 'branches'
        ));
    }

    /**
     * Reporte de Servicios
     */
    public function services(Request $request)
    {
        $query = Service::with(['category']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $services = $query->paginate(50);

        $topServices = $this->getTopServices($request);
        $revenueByCategory = $this->getRevenueByCategory($request);

        $categories = ServiceCategory::where('is_active', 1)->get();

        return view('admin.reports.services', compact(
            'services', 'topServices', 'revenueByCategory', 'categories'
        ));
    }

    /**
     * Reporte de Honorarios
     */
    public function fees(Request $request)
    {
        $query = DoctorFee::with(['doctor', 'invoice.patient', 'invoice.branch']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $fees = $query->latest()->paginate(50);

        $stats = [
            'total' => (clone $query)->count(),
            'total_amount' => (clone $query)->sum('fee_amount'),
            'paid' => (clone $query)->where('status', 'paid')->sum('fee_amount'),
            'pending' => (clone $query)->where('status', 'pending')->sum('fee_amount'),
            'cancelled' => (clone $query)->where('status', 'cancelled')->sum('fee_amount'),
        ];

        $topDoctors = $this->getTopDoctorFees($query);

        $doctors = User::where('is_doctor', 1)->orderBy('name')->get();

        return view('admin.reports.fees', compact(
            'fees', 'stats', 'topDoctors', 'doctors'
        ));
    }

    /**
     * Exportar datos a CSV
     */
    public function export(Request $request)
    {
        $type = $request->type;
        $format = $request->format ?? 'csv';

        $data = $this->getExportData($type, $request);

        if ($format === 'csv') {
            return $this->exportCSV($data, $type);
        }

        return $this->exportExcel($data, $type);
    }

    // ============================================
    // MÉTODOS PRIVADOS
    // ============================================

    /**
     * Aplicar filtros a la consulta
     */
    private function applyFilters($query, $request)
{
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('branch_id')) {
        $query->where('branch_id', $request->branch_id);
    }
    if ($request->filled('insurance_id')) {
        if ($request->insurance_id == 'null') {
            $query->whereNull('insurance_id');
        } else {
            $query->where('insurance_id', $request->insurance_id);
        }
    }
    if ($request->filled('doctor_id')) {
        $query->where('doctor_id', $request->doctor_id);
    }
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }
    if ($request->filled('with_ncf')) {
        $query->where('with_ncf', $request->with_ncf);
    }
    if ($request->filled('has_insurance')) {
        if ($request->has_insurance == '1') {
            $query->whereNotNull('insurance_id');
        } else {
            $query->whereNull('insurance_id');
        }
    }
    if ($request->filled('payment_method')) {
        $query->whereHas('receipts', function($q) use ($request) {
            if ($request->payment_method == 'cash') {
                $q->where('cash_amount', '>', 0);
            } elseif ($request->payment_method == 'card') {
                $q->where('card_amount', '>', 0);
            } elseif ($request->payment_method == 'transfer') {
                $q->where('transfer_amount', '>', 0);
            }
        });
    }
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('id', 'LIKE', "%{$search}%")
              ->orWhere('ncf', 'LIKE', "%{$search}%")
              ->orWhereHas('patient', function($q2) use ($search) {
                  $q2->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('cedula', 'LIKE', "%{$search}%");
              });
        });
    }
}

    private function getInvoiceStats($query, $request)
    {
        $baseQuery = clone $query;

        return [
            'total' => (clone $baseQuery)->count(),
            'total_amount' => (clone $baseQuery)->sum('total'),
            'total_paid' => (clone $baseQuery)->where('status', 'pagada')->sum('total'),
            'total_pending' => (clone $baseQuery)->where('status', 'pendiente')->sum('total'),
            'total_cancelled' => (clone $baseQuery)->where('status', 'cancelada')->sum('total'),
            'total_tax' => (clone $baseQuery)->sum('tax_amount'),
            'total_insurance_discount' => (clone $baseQuery)->sum('insurance_discount'),
            'with_ncf' => (clone $baseQuery)->where('with_ncf', 1)->count(),
            'without_ncf' => (clone $baseQuery)->where('with_ncf', 0)->count(),
        ];
    }

    private function getPaymentMethods($request)
    {
        $query = Receipt::query();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $receipts = $query->get();

        return [
            'cash' => $receipts->sum('cash_amount') ?? 0,
            'card' => $receipts->sum('card_amount') ?? 0,
            'transfer' => $receipts->sum('transfer_amount') ?? 0,
            'total' => $receipts->sum('total_paid') ?? 0,
            'count' => $receipts->count(),
        ];
    }

    private function getInsuranceStats($request)
    {
        $query = Invoice::query();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return Insurance::where('active', 1)
            ->withCount(['invoices' => function($q) use ($request) {
                if ($request->filled('date_from')) {
                    $q->whereDate('created_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $q->whereDate('created_at', '<=', $request->date_to);
                }
            }])
            ->withSum(['invoices' => function($q) use ($request) {
                if ($request->filled('date_from')) {
                    $q->whereDate('created_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $q->whereDate('created_at', '<=', $request->date_to);
                }
                $q->where('status', 'pagada');
            }], 'total')
            ->withSum(['invoices' => function($q) use ($request) {
                if ($request->filled('date_from')) {
                    $q->whereDate('created_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $q->whereDate('created_at', '<=', $request->date_to);
                }
            }], 'insurance_discount')
            ->having('invoices_count', '>', 0)
            ->orderByDesc('invoices_count')
            ->get();
    }

    private function getRecepcionistaStats($request)
    {
        $query = Invoice::query();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        return User::where('role_id', 2)
            ->withCount(['invoices' => function($q) use ($query, $request) {
                if ($request->filled('date_from')) {
                    $q->whereDate('created_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $q->whereDate('created_at', '<=', $request->date_to);
                }
                if ($request->filled('branch_id')) {
                    $q->where('branch_id', $request->branch_id);
                }
            }])
            ->withSum(['invoices' => function($q) use ($query, $request) {
                if ($request->filled('date_from')) {
                    $q->whereDate('created_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $q->whereDate('created_at', '<=', $request->date_to);
                }
                if ($request->filled('branch_id')) {
                    $q->where('branch_id', $request->branch_id);
                }
                $q->where('status', 'pagada');
            }], 'total')
            ->having('invoices_count', '>', 0)
            ->orderByDesc('invoices_count')
            ->get();
    }

    private function calculateCompletionRate($query)
    {
        $total = (clone $query)->count();
        $completed = (clone $query)->where('status', 'completada')->count();
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    private function getAppointmentsByDay($query)
    {
        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $result = [];

        $appointments = (clone $query)->selectRaw('DAYOFWEEK(appointment_date) as day, COUNT(*) as count')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $dayMap = [1 => 'Domingo', 2 => 'Lunes', 3 => 'Martes', 4 => 'Miércoles', 5 => 'Jueves', 6 => 'Viernes', 7 => 'Sábado'];

        foreach ($days as $day) {
            $result[$day] = 0;
        }

        foreach ($appointments as $key => $value) {
            $dayName = $dayMap[$key] ?? 'Desconocido';
            if (isset($result[$dayName])) {
                $result[$dayName] = $value->count;
            }
        }

        return $result;
    }

    private function getTopDoctors($query)
    {
        return (clone $query)
            ->select('doctor_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('doctor_id')
            ->with('doctor')
            ->groupBy('doctor_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    private function getPatientsByMonth($query)
    {
        return (clone $query)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
    }

    private function getTopInsurances($query)
    {
        return Insurance::withCount(['patients' => function($q) use ($query) {
            if ($query->getModel()->getConnection()->getQueryLog()) {
                // Aplicar filtros de fecha si existen
            }
        }])
        ->having('patients_count', '>', 0)
        ->orderByDesc('patients_count')
        ->limit(10)
        ->get();
    }

    private function getTopServices($request)
    {
        $query = DB::table('invoice_items')
            ->join('services', 'invoice_items.service_id', '=', 'services.id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', 'pagada');

        if ($request->filled('date_from')) {
            $query->whereDate('invoices.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoices.created_at', '<=', $request->date_to);
        }
        if ($request->filled('branch_id')) {
            $query->where('invoices.branch_id', $request->branch_id);
        }

        return $query->select(
            'services.id',
            'services.name',
            DB::raw('SUM(invoice_items.quantity) as total_quantity'),
            DB::raw('SUM(invoice_items.subtotal) as total_revenue')
        )
        ->groupBy('services.id', 'services.name')
        ->orderByDesc('total_revenue')
        ->limit(10)
        ->get();
    }

    private function getRevenueByCategory($request)
    {
        $query = DB::table('invoice_items')
            ->join('services', 'invoice_items.service_id', '=', 'services.id')
            ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', 'pagada');

        if ($request->filled('date_from')) {
            $query->whereDate('invoices.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoices.created_at', '<=', $request->date_to);
        }

        return $query->select(
            'service_categories.id',
            'service_categories.name',
            DB::raw('SUM(invoice_items.subtotal) as total_revenue')
        )
        ->groupBy('service_categories.id', 'service_categories.name')
        ->orderByDesc('total_revenue')
        ->get();
    }

    private function getTopDoctorFees($query)
    {
        return (clone $query)
            ->select('doctor_id', DB::raw('SUM(fee_amount) as total_fees'))
            ->where('status', 'paid')
            ->whereNotNull('doctor_id')
            ->with('doctor')
            ->groupBy('doctor_id')
            ->orderByDesc('total_fees')
            ->limit(10)
            ->get();
    }

    private function getExportData($type, $request)
    {
        switch ($type) {
            case 'invoices':
                return $this->getInvoiceExportData($request);
            case 'appointments':
                return $this->getAppointmentExportData($request);
            case 'patients':
                return $this->getPatientExportData($request);
            case 'fees':
                return $this->getFeeExportData($request);
            default:
                return [];
        }
    }

    private function getInvoiceExportData($request)
    {
        $query = Invoice::with(['patient', 'branch', 'doctor', 'insurance']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query->get()->map(function($invoice) {
            return [
                'ID' => $invoice->id,
                'Paciente' => $invoice->patient->full_name ?? 'N/A',
                'Cédula' => $invoice->patient->cedula ?? 'N/A',
                'Médico' => $invoice->doctor->name ?? 'N/A',
                'Recepcionista' => $invoice->user->name ?? 'N/A',
                'Sucursal' => $invoice->branch->name ?? 'N/A',
                'Seguro' => $invoice->insurance->name ?? 'Sin seguro',
                'Subtotal' => $invoice->subtotal,
                'Impuestos' => $invoice->tax_amount,
                'Descuento Seguro' => $invoice->insurance_discount,
                'Total' => $invoice->total,
                'Estado' => ucfirst($invoice->status),
                'Fecha' => $invoice->created_at->format('d/m/Y H:i'),
                'NCF' => $invoice->ncf ?? 'N/A',
            ];
        });
    }

    private function getAppointmentExportData($request)
    {
        $query = Appointment::with(['patient', 'doctor', 'branch']);

        if ($request->filled('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }

        return $query->get()->map(function($appointment) {
            return [
                'ID' => $appointment->id,
                'Paciente' => $appointment->patient->full_name ?? 'N/A',
                'Médico' => $appointment->doctor->name ?? 'N/A',
                'Sucursal' => $appointment->branch->name ?? 'N/A',
                'Fecha' => $appointment->appointment_date,
                'Hora' => $appointment->appointment_time,
                'Estado' => ucfirst($appointment->status),
                'Notas' => $appointment->notes ?? 'N/A',
            ];
        });
    }

    private function getPatientExportData($request)
    {
        $query = Patient::with(['branch', 'insurance']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query->get()->map(function($patient) {
            return [
                'ID' => $patient->id,
                'Nombre' => $patient->full_name,
                'Cédula' => $patient->cedula ?? 'N/A',
                'Teléfono' => $patient->phone ?? 'N/A',
                'Email' => $patient->email ?? 'N/A',
                'Género' => $patient->gender ?? 'N/A',
                'Seguro' => $patient->insurance->name ?? 'Sin seguro',
                'Sucursal' => $patient->branch->name ?? 'N/A',
                'Fecha Registro' => $patient->created_at->format('d/m/Y'),
            ];
        });
    }

    private function getFeeExportData($request)
    {
        $query = DoctorFee::with(['doctor', 'invoice.patient']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query->get()->map(function($fee) {
            return [
                'ID' => $fee->id,
                'Médico' => $fee->doctor->name ?? 'N/A',
                'Paciente' => $fee->invoice->patient->full_name ?? 'N/A',
                'Total Factura' => $fee->invoice_total,
                'Tipo' => $fee->calculation_type === 'percentage' ? 'Porcentaje' : 'Monto Fijo',
                'Valor' => $fee->calculation_type === 'percentage' ? $fee->calculation_value . '%' : $fee->calculation_value,
                'Honorario' => $fee->fee_amount,
                'Estado' => ucfirst($fee->status),
                'Fecha' => $fee->created_at->format('d/m/Y'),
            ];
        });
    }

    private function exportCSV($data, $type)
    {
        if (empty($data) || $data->isEmpty()) {
            return redirect()->back()->with('error', 'No hay datos para exportar.');
        }

        $filename = "reporte_{$type}_" . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            $firstRow = (array) $data->first();
            fputcsv($file, array_keys($firstRow), ';');

            foreach ($data as $row) {
                fputcsv($file, (array) $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($data, $type)
    {
        return $this->exportCSV($data, $type);
    }
}