<?php

namespace App\Http\Controllers;

use App\Models\InsuranceInvoice;
use App\Models\Insurance;
use App\Models\Branch;
use App\Models\Setting;
use App\Services\InsuranceInvoiceService;
use App\Services\EF2Service;
use Illuminate\Http\Request;

class InsuranceInvoiceController extends Controller
{
    protected InsuranceInvoiceService $service;
    protected EF2Service $ef2Service;

    public function __construct(InsuranceInvoiceService $service, EF2Service $ef2Service)
    {
        $this->service = $service;
        $this->ef2Service = $ef2Service;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        $query = InsuranceInvoice::with(['insurance', 'branch', 'user']);

        if (!$isAdmin) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('number', 'like', "%{$s}%")
                  ->orWhere('encf', 'like', "%{$s}%")
                  ->orWhereHas('insurance', fn($i) =>
                      $i->where('name', 'like', "%{$s}%")
                  );
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('insurance_id')) {
            $query->where('insurance_id', $request->insurance_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->latest('invoice_date')->paginate(15);
        $insurances = Insurance::where('active', 1)->orderBy('name')->get();
        $branches = $isAdmin ? Branch::all() : collect();

        return view('insurance-invoices.index', compact('invoices', 'insurances', 'branches', 'isAdmin'));
    }

    public function create()
    {
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        $insurances = Insurance::where('active', 1)->orderBy('name')->get();

        $branches = $isAdmin
            ? Branch::where('is_active', 1)->get()
            : Branch::where('id', $user->branch_id)->get();

        $defaultBranchId = $isAdmin ? $branches->first()->id ?? null : $user->branch_id;

        return view('insurance-invoices.create', compact(
            'insurances', 'branches', 'isAdmin', 'defaultBranchId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_date'           => 'required|date',
            'insurance_id'           => 'required|exists:insurances,id',
            'branch_id'              => 'required|exists:branches,id',
            'amount'                 => 'required|numeric|min:0.01',
            'with_tax'               => 'nullable|boolean',
            'ncf_type'               => 'nullable|in:credito_fiscal,consumidor_final,gubernamental,regimen_especial',
            'customer_rnc'           => 'nullable|string|max:255',
            'customer_business_name' => 'nullable|string|max:255',
            'concept'                => 'nullable|string|max:500',
            'notes'                  => 'nullable|string',
        ]);

        // RNC del seguro obligatorio para crédito fiscal o gubernamental
        $tipoNcf = $data['ncf_type'] ?? 'credito_fiscal';

        if (in_array($tipoNcf, ['credito_fiscal', 'gubernamental']) && empty($data['customer_rnc'])) {
            return back()->withErrors([
                'customer_rnc' => 'El RNC es obligatorio para este tipo de comprobante.'
            ])->withInput();
        }

        // Forzar datos del seguro si no se envían
        $insurance = Insurance::find($data['insurance_id']);
        $data['customer_business_name'] = $data['customer_business_name'] ?? $insurance->name;
        $data['ncf_type'] = $tipoNcf;

        $user = auth()->user();
        if ($user->role->name !== 'admin' && $data['branch_id'] != $user->branch_id) {
            abort(403, 'No puedes facturar en otra sucursal.');
        }

        try {
            $invoice = $this->service->createInvoice($data);

            return redirect()
                ->route('insurance-invoices.show', $invoice)
                ->with('success', 'Factura a seguro creada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(InsuranceInvoice $insuranceInvoice)
    {
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $insuranceInvoice->branch_id != $user->branch_id) {
            abort(403);
        }

        $insuranceInvoice->load([
            'insurance', 'branch', 'user',
            'ncfSequence', 'ecfSequence',
            'ecfDocuments' => fn($q) => $q->latest(),
        ]);

        $company = [
            'name'  => Setting::get('company_name', 'Mi Clínica'),
            'rnc'   => Setting::get('company_rnc', ''),
            'logo'  => Setting::get('company_logo', null),
            'phone' => Setting::get('company_phone', ''),
            'address' => Setting::get('company_address', ''),
        ];

        $ef2Configurado = $this->ef2Service->estaConfigurado();

        return view('insurance-invoices.show', compact('insuranceInvoice', 'company', 'ef2Configurado'));
    }

    public function print(InsuranceInvoice $insuranceInvoice)
    {
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $insuranceInvoice->branch_id != $user->branch_id) {
            abort(403);
        }

        $insuranceInvoice->load(['insurance', 'branch', 'user', 'ncfSequence', 'ecfSequence']);

        $company = [
            'name'  => Setting::get('company_name', 'Mi Clínica'),
            'rnc'   => Setting::get('company_rnc', ''),
            'logo'  => Setting::get('company_logo', null),
            'phone' => Setting::get('company_phone', ''),
            'address' => Setting::get('company_address', ''),
            'slogan'  => Setting::get('company_slogan', ''),
            'footer_text' => Setting::get('company_footer_text', 'Gracias por su preferencia'),
            'currency' => Setting::get('company_currency', 'DOP'),
        ];

        return view('insurance-invoices.print', compact('insuranceInvoice', 'company'));
    }

   public function cancel(Request $request, InsuranceInvoice $insuranceInvoice)
{
    $user = auth()->user();
    if ($user->role->name !== 'admin' && $insuranceInvoice->branch_id != $user->branch_id) {
        abort(403);
    }

    if (!$insuranceInvoice->canBeCancelled()) {
        return back()->with('error', 'Solo se pueden cancelar facturas pendientes.');
    }

    $request->validate([
        'reason' => 'nullable|string|max:500',
    ]);

    try {
        $result = $this->service->cancelarFactura($insuranceInvoice, $request->input('reason'));
    } catch (\Exception $e) {
        return back()->with('error', 'Error al cancelar: ' . $e->getMessage());
    }

    if (!$result['ok']) {
        return back()->with('error', $result['message']);
    }

    return redirect()
        ->route('insurance-invoices.show', $insuranceInvoice)
        ->with('success', $result['message']);
}

    public function destroy(InsuranceInvoice $insuranceInvoice)
    {
        $user = auth()->user();
        if ($user->role->name !== 'admin') {
            abort(403);
        }

        if ($insuranceInvoice->status !== 'pendiente') {
            return back()->with('error', 'No se puede eliminar una factura pagada o cancelada.');
        }

        $insuranceInvoice->delete();

        return redirect()->route('insurance-invoices.index')
            ->with('success', 'Factura eliminada.');
    }

    // ═══════════════════════════════════════════════════════════
    // FACTURACIÓN ELECTRÓNICA (reenvío manual)
    // ═══════════════════════════════════════════════════════════

    public function enviarEf2(InsuranceInvoice $insuranceInvoice)
    {
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $insuranceInvoice->branch_id != $user->branch_id) {
            abort(403);
        }

        if (!$this->ef2Service->estaConfigurado()) {
            return back()->with('error', 'La facturación electrónica no está configurada.');
        }

        if ($insuranceInvoice->enviada_dgii && $insuranceInvoice->estado_dgii === 'aceptado') {
            return back()->with('info', 'Esta factura ya fue aceptada por la DGII.');
        }

        try {
            $result = $this->ef2Service->enviarFacturaSeguro($insuranceInvoice);
            $this->service->guardarRespuestaEf2($insuranceInvoice, $result);

            if (!empty($result['success'])) {
                return back()->with('success', 'Factura enviada a la DGII. e-NCF: ' . ($result['ncf'] ?? ''));
            }

            return back()->with('error', 'Error al enviar: ' . ($result['message'] ?? 'Desconocido'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}