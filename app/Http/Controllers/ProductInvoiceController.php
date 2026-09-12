<?php

namespace App\Http\Controllers;

use App\Models\ProductInvoice;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Patient;
use App\Models\Branch;
use App\Models\Setting;
use App\Services\ProductInvoiceService;
use Illuminate\Http\Request;

class ProductInvoiceController extends Controller
{
    protected $service;

    public function __construct(ProductInvoiceService $service)
    {
        $this->service = $service;
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

        // Categorías de productos activas
        $categories = ProductCategory::active()->orderBy('name')->get();

        // Sucursales
        $branches = $isAdmin 
            ? Branch::where('is_active', 1)->get()
            : Branch::where('id', $user->branch_id)->get();

        // Pacientes (solo de su sucursal si no es admin)
        $patients = $isAdmin
            ? Patient::orderBy('first_name')->get()
            : Patient::where('branch_id', $user->branch_id)->orderBy('first_name')->get();

        // Sucursal por defecto
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
            'ncf'          => 'nullable|string|max:255',
            'ncf_type'     => 'nullable|in:consumidor_final,credito_fiscal,gubernamental,regimen_especial',
            'customer_rnc' => 'nullable|string|max:255',
            'customer_business_name' => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        // Validar acceso por sucursal
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $data['branch_id'] != $user->branch_id) {
            abort(403, 'No puedes facturar en otra sucursal.');
        }

        try {
            $invoice = $this->service->createInvoice($data);
            return redirect()
                ->route('product-invoices.show', $invoice)
                ->with('success', 'Factura de productos creada exitosamente.');
        } catch (\Exception $e) {
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

        $productInvoice->load(['patient', 'user', 'branch', 'items.product', 'receipt']);

        $company = [
            'name'  => Setting::get('company_name', 'Mi Clínica'),
            'rnc'   => Setting::get('company_rnc', ''),
            'logo'  => Setting::get('company_logo', null),
            'phone' => Setting::get('company_phone', ''),
            'address' => Setting::get('company_address', ''),
        ];

        return view('product-invoices.show', compact('productInvoice', 'company'));
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

        $productInvoice->load(['patient', 'user', 'branch', 'items.product', 'receipt']);

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

        if (!$isAdmin) {
            $query->where('branch_id', $user->branch_id);
        }

        $patients = $query->orderBy('first_name')->limit(10)->get();

        return response()->json($patients);
    }

    /**
     * Obtener productos por categoría y sucursal
     */
    public function getProductsByCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'branch_id'   => 'required|exists:branches,id',
        ]);

        $user = auth()->user();
        if ($user->role->name !== 'admin' && $request->branch_id != $user->branch_id) {
            return response()->json(['error' => 'Sin acceso a esta sucursal'], 403);
        }

        $products = Product::active()
            ->where('category_id', $request->category_id)
            ->with(['stocks' => fn($q) => $q->where('branch_id', $request->branch_id)])
            ->orderBy('name')
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