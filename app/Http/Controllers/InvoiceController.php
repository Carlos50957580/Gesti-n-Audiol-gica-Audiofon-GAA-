<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Branch;
use App\Models\Insurance;
use App\Models\Setting;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller

{


protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    
    public function index(Request $request)
    {
        $query = Invoice::with(['patient', 'user', 'doctor', 'branch', 'insurance']);

        // Filtrar por sucursal si es recepcionista
        if (auth()->user()->role->name === 'recepcionista') {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', function($q2) use ($search) {
                      $q2->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('cedula', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id') && auth()->user()->role->name === 'admin') {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $invoices = $query->latest()->paginate(15);

        // Para el filtro, solo admin ve todas las sucursales
        $branches = Branch::all();

        if (auth()->user()->role->name === 'recepcionista') {
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
        }

        return view('invoices.index', compact('invoices', 'branches'));
    }

     public function create()
    {
        // Obtener sucursales según rol
        if (auth()->user()->role->name === 'admin') {
            $branches = Branch::all();
        } else {
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
        }

        // ✅ Obtener médicos correctamente (rol medico = id 3)
        $doctors = User::where(function($q) {
            $q->where('role_id', 3)  // ✅ Rol médico (ID 3)
              ->orWhere(function($q2) {
                  $q2->where('role_id', 1)  // Admin
                    ->where('is_doctor', 1);
              });
        })
        ->where('is_active', 1);

        if (auth()->user()->role->name === 'recepcionista') {
            $doctors->where('branch_id', auth()->user()->branch_id);
        }

        $doctors = $doctors->with('branch')->orderBy('name')->get();

        $insurances = Insurance::where('active', 1)->get();
        $categories = ServiceCategory::where('is_active', 1)->with('services')->get();
        $services = Service::where('is_active', 1)->with(['category', 'taxes'])->get();

        return view('invoices.create', compact('doctors', 'branches', 'insurances', 'categories', 'services'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'required|exists:users,id',
        'branch_id' => 'required|exists:branches,id',
        'insurance_id' => 'nullable|exists:insurances,id',
        'authorization_number' => 'nullable|string|max:255',
        'with_ncf' => 'nullable|boolean',
        'ncf_type' => 'nullable|required_if:with_ncf,1|in:consumidor_final,credito_fiscal,gubernamental,regimen_especial',
        'customer_rnc' => 'nullable|string|max:255',
        'customer_business_name' => 'nullable|string|max:255',
        'services' => 'required|array|min:1',
        'services.*.id' => 'required|exists:services,id',
        'services.*.quantity' => 'required|integer|min:1',
        'services.*.custom_price' => 'nullable|numeric|min:0',
        'services.*.cov_value' => 'nullable|numeric|min:0',
        'services.*.cov_type' => 'nullable|in:pct,amt',
    ]);

    if (auth()->user()->role->name === 'recepcionista') {
        if ($request->branch_id != auth()->user()->branch_id) {
            return redirect()->back()->withInput()
                ->with('error', 'No puedes facturar en otra sucursal.');
        }
    }

    try {
        $invoice = $this->invoiceService->createInvoice($validated);

        return redirect()
            ->route('invoices.show', $invoice->id)
            ->with('success', "Factura #{$invoice->id} creada exitosamente.");
    } catch (\Exception $e) {
        return redirect()->back()->withInput()
            ->with('error', 'Error al crear la factura: ' . $e->getMessage());
    }
}

public function consultRnc($rnc)
{
    try {

        $rnc = preg_replace('/[^0-9]/', '', $rnc);

        $response = Http::timeout(15)
            ->get('https://rnc.megaplus.com.do/api/consulta', [
                'rnc' => $rnc
            ]);

        return response()->json(
            $response->json(),
            $response->status()
        );

    } catch (\Exception $e) {

        return response()->json([
            'error' => true,
            'mensaje' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Display the specified invoice.
     */
   /**
     * Display the specified invoice.
     */
   public function show(Invoice $invoice)
{
    // Validar acceso
    if (auth()->user()->role->name === 'recepcionista') {
        if ($invoice->branch_id != auth()->user()->branch_id) {
            abort(403, 'No tienes acceso a esta factura.');
        }
    }

    // ✅ AGREGADO: 'ecfDocuments.user' y 'receipt' (singular)
    $invoice->load([
        'patient',
        'user',
        'doctor',
        'branch',
        'insurance',
        'items.service.category',
        'ecfDocuments.user',  // ← Historial DGII con quién lo envió
        'receipt',            // ← Último recibo (para mostrar pago registrado)
    ]);

    // ── Obtener configuración de la empresa ─────────────────────────────
    $company = [
        'name' => Setting::get('company_name', 'Mi Clínica'),
        'business_name' => Setting::get('company_business_name', 'Mi Clínica SRL'),
        'rnc' => Setting::get('company_rnc', ''),
        'email' => Setting::get('company_email', ''),
        'phone' => Setting::get('company_phone', ''),
        'mobile' => Setting::get('company_mobile', ''),
        'address' => Setting::get('company_address', ''),
        'slogan' => Setting::get('company_slogan', ''),
        'website' => Setting::get('company_website', ''),
        'logo' => Setting::get('company_logo', null),
        'favicon' => Setting::get('company_favicon', null),
        'footer_text' => Setting::get('company_footer_text', 'Gracias por su preferencia'),
        'currency' => Setting::get('company_currency', 'DOP'),
        'tax_rate' => Setting::get('company_tax_rate', 18),
        'invoice_prefix' => Setting::get('company_invoice_prefix', 'FAC-'),
        'receipt_prefix' => Setting::get('company_receipt_prefix', 'REC-'),
        'ncf_type' => Setting::get('company_ncf_type', 'consumidor_final'),
    ];

    return view('invoices.show', compact('invoice', 'company'));
}

    /**
     * Print the specified invoice.
     */
    public function print(Invoice $invoice)
    {
        // Validar acceso
        if (auth()->user()->role->name === 'recepcionista') {
            if ($invoice->branch_id != auth()->user()->branch_id) {
                abort(403);
            }
        }

        $invoice->load(['patient', 'doctor', 'branch', 'insurance', 'items.service.category']);

        // ── Obtener configuración de la empresa ─────────────────────────────
        $company = [
            'name' => Setting::get('company_name', 'Mi Clínica'),
            'business_name' => Setting::get('company_business_name', 'Mi Clínica SRL'),
            'rnc' => Setting::get('company_rnc', ''),
            'email' => Setting::get('company_email', ''),
            'phone' => Setting::get('company_phone', ''),
            'mobile' => Setting::get('company_mobile', ''),
            'address' => Setting::get('company_address', ''),
            'slogan' => Setting::get('company_slogan', ''),
            'website' => Setting::get('company_website', ''),
            'logo' => Setting::get('company_logo', null),
            'favicon' => Setting::get('company_favicon', null),
            'footer_text' => Setting::get('company_footer_text', 'Gracias por su preferencia'),
            'currency' => Setting::get('company_currency', 'DOP'),
            'tax_rate' => Setting::get('company_tax_rate', 18),
            'invoice_prefix' => Setting::get('company_invoice_prefix', 'FAC-'),
            'receipt_prefix' => Setting::get('company_receipt_prefix', 'REC-'),
            'ncf_type' => Setting::get('company_ncf_type', 'consumidor_final'),
        ];

        return view('invoices.print', compact('invoice', 'company'));
    }

    // ... resto de métodos ...

    /**
     * Cancel the specified invoice.
     */
    public function cancel(Invoice $invoice)
    {
        // Validar acceso
        if (auth()->user()->role->name === 'recepcionista') {
            if ($invoice->branch_id != auth()->user()->branch_id) {
                abort(403);
            }
        }

        if ($invoice->status !== 'pendiente') {
            return redirect()
                ->back()
                ->with('error', 'Solo se pueden cancelar facturas pendientes.');
        }

        $invoice->update(['status' => 'cancelada']);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', "Factura #{$invoice->id} cancelada exitosamente.");
    }

    public function destroy(Invoice $invoice)
    {
        if (auth()->user()->role->name === 'recepcionista') {
            if ($invoice->branch_id != auth()->user()->branch_id) {
                abort(403, 'No tienes acceso a esta factura.');
            }
        }

        if ($invoice->status !== 'pendiente') {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar una factura pagada o cancelada.');
        }

        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', "Factura #{$invoice->id} eliminada.");
    }

    // ============================================
    // API METHODS
    // ============================================

    public function searchPatients(Request $request)
{
    $query = $request->get('q', '');

    $patients = Patient::where(function ($q) use ($query) {
            $q->where('first_name', 'LIKE', "%{$query}%")
              ->orWhere('last_name', 'LIKE', "%{$query}%")
              ->orWhere('cedula', 'LIKE', "%{$query}%")
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"]);
        })
        ->with('insurance')
        ->limit(20)
        ->get();

    return response()->json($patients->map(function ($patient) {
        return [
            'id' => $patient->id,
            'full_name' => $patient->full_name,
            'cedula' => $patient->cedula,
            'phone' => $patient->phone,
            'insurance_id' => $patient->insurance_id,
            'insurance_name' => $patient->insurance?->name,
            'insurance_coverage' => $patient->insurance?->coverage_percentage,
        ];
    }));
}

    public function storePatient(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cedula' => 'nullable|string|max:255|unique:patients,cedula',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F',
            'address' => 'nullable|string',
            'insurance_id' => 'nullable|exists:insurances,id',
            'insurance_number' => 'nullable|string|max:255',
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;

        $patient = Patient::create($validated);

        return response()->json([
            'message' => 'Paciente creado exitosamente.',
            'patient' => [
                'id' => $patient->id,
                'full_name' => $patient->full_name,
                'cedula' => $patient->cedula,
                'phone' => $patient->phone,
                'insurance_id' => $patient->insurance_id,
                'insurance_name' => $patient->insurance?->name,
                'insurance_coverage' => $patient->insurance?->coverage_percentage,
            ]
        ], 201);
    }

    public function getServicesByCategory($categoryId)
    {
        $services = Service::where('category_id', $categoryId)
            ->where('is_active', 1)
            ->with('taxes')
            ->get(['id', 'name', 'price', 'requires_clinical_record']);

        return response()->json($services);
    }

    /**
 * Obtener médicos para el select de facturación
 */
public function getDoctors(Request $request)
{
    // ✅ CORREGIDO: role_id = 3 (medico) en lugar de 4
    $query = User::where(function($q) {
        $q->where('role_id', 3)  // ✅ Rol médico (ID 3)
          ->orWhere(function($q2) {
              $q2->where('role_id', 1)  // Admin
                ->where('is_doctor', 1);
          });
    })
    ->where('is_active', 1);

    if (auth()->user()->role->name === 'recepcionista') {
        $query->where('branch_id', auth()->user()->branch_id);
    } elseif ($request->filled('branch_id') && auth()->user()->role->name === 'admin') {
        $query->where('branch_id', $request->branch_id);
    }

    $doctors = $query->with('branch')->orderBy('name')->get();

    return response()->json($doctors->map(fn($doctor) => [
        'id' => $doctor->id,
        'name' => $doctor->name,
        'branch_id' => $doctor->branch_id,
        'branch_name' => $doctor->branch?->name,
    ]));
}
    public function getBranches()
    {
        if (auth()->user()->role->name === 'admin') {
            $branches = Branch::all();
        } else {
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
        }

        return response()->json($branches);
    }

    public function getCoverage($serviceId, $insuranceId)
{
    $service = Service::with('insuranceCoverage')->findOrFail($serviceId);
    $insurance = Insurance::findOrFail($insuranceId);
    
    // Verificar si el servicio tiene cobertura específica
    $specificCoverage = $service->getCoverageForInsurance($insurance);
    
    if ($specificCoverage) {
        $calculation = $specificCoverage->calculateCoverage($service->price);
        return response()->json([
            'has_coverage' => true,
            'coverage_percentage' => $calculation['percentage'],
            'insurance_amount' => $calculation['insurance_amount'],
            'patient_amount' => $calculation['patient_amount'],
            'is_specific' => true,
            'source' => 'Específica del servicio',
            'requires_authorization' => $specificCoverage->requires_authorization
        ]);
    }
    
    // Si no hay cobertura específica, usar la global del seguro
    $globalCoverage = $insurance->coverage_percentage;
    $insuranceAmount = $service->price * ($globalCoverage / 100);
    $patientAmount = $service->price - $insuranceAmount;
    
    return response()->json([
        'has_coverage' => $globalCoverage > 0,
        'coverage_percentage' => $globalCoverage,
        'insurance_amount' => $insuranceAmount,
        'patient_amount' => $patientAmount,
        'is_specific' => false,
        'source' => 'Seguro global',
        'requires_authorization' => false
    ]);
}


    /**
     * ✅ NUEVO: Enviar (o reenviar) la factura a EF2 manualmente
     */
    public function enviarEf2(Invoice $invoice)
    {
        if (auth()->user()->role->name === 'recepcionista') {
            if ($invoice->branch_id != auth()->user()->branch_id) {
                abort(403);
            }
        }

        if (!$invoice->puedeEnviarseADgii()) {
            return back()->with('error', 'Esta factura no puede enviarse a la DGII (ya fue aceptada o está cancelada).');
        }

        try {
            $result = $this->invoiceService->enviarFacturaServiciosPublic($invoice);

            if (!empty($result['success'])) {
                return back()->with(
                    'success',
                    'Factura enviada a la DGII correctamente. e-NCF: ' . ($result['ncf'] ?? '')
                );
            }

            return back()->with(
                'error',
                'Error al enviar a la DGII: ' . ($result['message'] ?? 'Desconocido')
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Error al conectar con EF2: ' . $e->getMessage());
        }
    }
}