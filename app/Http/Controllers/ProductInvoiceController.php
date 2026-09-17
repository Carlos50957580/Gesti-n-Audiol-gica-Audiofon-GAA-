<?php

namespace App\Http\Controllers;

use App\Models\ProductInvoice;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Patient;
use App\Models\Branch;
use App\Models\Setting;
use App\Services\ProductInvoiceService;
use App\Services\EF2Service;
use Illuminate\Http\Request;

class ProductInvoiceController extends Controller
{
    protected ProductInvoiceService $service;
    protected EF2Service $ef2Service;

    public function __construct(ProductInvoiceService $service, EF2Service $ef2Service)
    {
        $this->service = $service;
        $this->ef2Service = $ef2Service;
    }

    /**
     * Listado de facturas de productos
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        $query = ProductInvoice::with(['patient', 'user', 'branch', 'items.product']);

        if (!$isAdmin) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('number', 'like', "%{$s}%")
                  ->orWhereHas('patient', fn($p) =>
                      $p->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%")
                        ->orWhere('cedula', 'like', "%{$s}%")
                  );
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $invoices = $query->latest()->paginate(15);
        $branches = $isAdmin ? Branch::all() : collect();

        return view('product-invoices.index', compact('invoices', 'branches', 'isAdmin'));
    }

    /**
     * Formulario de nueva factura
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        $categories = ProductCategory::active()->orderBy('name')->get();

        $branches = $isAdmin 
            ? Branch::where('is_active', 1)->get()
            : Branch::where('id', $user->branch_id)->get();

        $patients = $isAdmin
            ? Patient::orderBy('first_name')->get()
            : Patient::where('branch_id', $user->branch_id)->orderBy('first_name')->get();

        $defaultBranchId = $isAdmin ? ($request->branch_id ?? $branches->first()->id ?? null) : $user->branch_id;

        return view('product-invoices.create', compact(
            'categories', 'branches', 'patients', 'isAdmin', 'defaultBranchId'
        ));
    }

    /**
     * Guardar factura
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'   => 'required|exists:patients,id',
            'branch_id'    => 'required|exists:branches,id',
            'items'        => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0',
            'with_ncf'     => 'boolean',
            'ncf_type'     => 'nullable|required_if:with_ncf,1|in:consumidor_final,credito_fiscal,gubernamental,regimen_especial',
            'customer_rnc' => 'nullable|string|max:255',
            'customer_business_name' => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $user = auth()->user();
        if ($user->role->name !== 'admin' && $data['branch_id'] != $user->branch_id) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'No puedes facturar en otra sucursal.'], 403);
            }
            abort(403, 'No puedes facturar en otra sucursal.');
        }

        try {
            $invoice = $this->service->createInvoice($data);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Factura de productos creada exitosamente.',
                    'redirect' => route('product-invoices.show', $invoice),
                ]);
            }

            return redirect()
                ->route('product-invoices.show', $invoice)
                ->with('success', 'Factura de productos creada exitosamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Ver detalle
     */
    public function show(ProductInvoice $productInvoice)
    {
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $productInvoice->branch_id != $user->branch_id) {
            abort(403);
        }

        $productInvoice->load([
            'patient',
            'user',
            'branch',
            'items.product',
            'receipts.user',
            'ncfSequence',
            'ecfSequence',
            'ecfDocuments' => fn($q) => $q->latest(),
        ]);

        $company = [
            'name'  => Setting::get('company_name', 'Mi Clínica'),
            'rnc'   => Setting::get('company_rnc', ''),
            'logo'  => Setting::get('company_logo', null),
            'phone' => Setting::get('company_phone', ''),
            'address' => Setting::get('company_address', ''),
        ];

        // Info de EF2 para saber si podemos mostrar el botón
        $ef2Configurado = $this->ef2Service->estaConfigurado();

        return view('product-invoices.show', compact('productInvoice', 'company', 'ef2Configurado'));
    }

    /**
     * Cancelar factura
     */
    public function cancel(ProductInvoice $productInvoice)
    {
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $productInvoice->branch_id != $user->branch_id) {
            abort(403);
        }

        if (!$productInvoice->canBeCancelled()) {
            return back()->with('error', 'Solo se pueden cancelar facturas pendientes.');
        }

        $productInvoice->update(['status' => 'cancelada']);

        return redirect()->route('product-invoices.show', $productInvoice)
            ->with('success', 'Factura cancelada.');
    }

    /**
     * Imprimir factura
     */
    public function print(ProductInvoice $productInvoice)
    {
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $productInvoice->branch_id != $user->branch_id) {
            abort(403);
        }

        $productInvoice->load([
            'patient',
            'user',
            'branch',
            'items.product',
            'receipts.user',
            'ncfSequence',
            'ecfSequence',
        ]);

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

        return view('product-invoices.print', compact('productInvoice', 'company'));
    }

    // ═══════════════════════════════════════════════════════════
    // FACTURACIÓN ELECTRÓNICA (EF2)
    // ═══════════════════════════════════════════════════════════

    /**
     * Enviar (o reenviar) la factura a EF2 manualmente.
     */
    public function enviarEf2(ProductInvoice $productInvoice)
    {
        $user = auth()->user();

        // Permisos
        if ($user->role->name !== 'admin' && $productInvoice->branch_id != $user->branch_id) {
            abort(403);
        }

        // Validar configuración
        if (!$this->ef2Service->estaConfigurado()) {
            return back()->with('error', 'La facturación electrónica no está configurada o no está activa.');
        }

        // Validar que la factura tenga tipo de comprobante
        if (!$productInvoice->ncf_type) {
            return back()->with('error', 'Esta factura no tiene un tipo de comprobante fiscal definido.');
        }

        // Validar que no esté cancelada
        if ($productInvoice->status === 'cancelada') {
            return back()->with('error', 'No se puede enviar a la DGII una factura cancelada.');
        }

        // Si ya fue aceptada, no reenviar
        if ($productInvoice->enviada_dgii && $productInvoice->estado_dgii === 'aceptado') {
            return back()->with('info', 'Esta factura ya fue aceptada por la DGII.');
        }

        try {
            $result = $this->ef2Service->enviarFacturaProductos($productInvoice);
            $this->service->guardarRespuestaEf2($productInvoice, $result);

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

    // ═══════════════════════════════════════════════════════════
    // API
    // ═══════════════════════════════════════════════════════════

    /**
     * Buscar pacientes
     */
    public function searchPatients(Request $request)
    {
        $q = trim($request->get('q', ''));
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        $query = Patient::where(function ($sq) use ($q) {
            $sq->where('first_name', 'like', "%{$q}%")
               ->orWhere('last_name', 'like', "%{$q}%")
               ->orWhere('cedula', 'like', "%{$q}%")
               ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$q}%"]);
        });

        $patients = $query->orderBy('first_name')->limit(10)->get();

        return response()->json($patients);
    }

    /**
     * Obtener productos por categoría/búsqueda y sucursal (AJAX)
     */
    public function getProductsByCategory(Request $request)
    {
        $request->validate([
            'branch_id'   => 'required|exists:branches,id',
            'category_id' => 'nullable|exists:product_categories,id',
            'q'           => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        if ($user->role->name !== 'admin' && $request->branch_id != $user->branch_id) {
            return response()->json(['error' => 'Sin acceso a esta sucursal'], 403);
        }

        $query = Product::active()
            ->with(['stocks' => fn($q) => $q->where('branch_id', $request->branch_id)]);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($sq) use ($search) {
                $sq->where('name', 'like', "%{$search}%")
                   ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')
            ->limit(50)
            ->get()
            ->map(function ($p) {
                $stock = $p->stocks->first();
                return [
                    'id'         => $p->id,
                    'code'       => $p->code,
                    'name'       => $p->name,
                    'price'      => (float) $p->sale_price,
                    'has_tax'    => (bool) $p->has_tax,
                    'unit'       => $p->unit,
                    'stock'      => $stock ? $stock->quantity : 0,
                    'available'  => $stock ? max(0, $stock->quantity - $stock->reserved_quantity) : 0,
                ];
            });

        return response()->json($products);
    }
}