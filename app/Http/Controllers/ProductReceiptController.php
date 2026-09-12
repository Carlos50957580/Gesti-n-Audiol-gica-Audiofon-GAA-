<?php

namespace App\Http\Controllers;

use App\Models\ProductInvoice;
use App\Models\ProductReceipt;
use App\Services\ProductInvoiceService;
use Illuminate\Http\Request;

class ProductReceiptController extends Controller
{
    protected $service;

    public function __construct(ProductInvoiceService $service)
    {
        $this->service = $service;
    }

    /**
     * Listado de pagos
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        $query = ProductReceipt::with(['invoice.patient', 'user', 'branch']);

        if (!$isAdmin) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('number', 'like', "%{$s}%")
                  ->orWhereHas('invoice.patient', fn($p) =>
                      $p->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%")
                  );
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $receipts = $query->latest()->paginate(15);

        return view('product-receipts.index', compact('receipts'));
    }

    /**
     * Formulario de pago (desde una factura pendiente)
     */
    public function create(ProductInvoice $productInvoice)
    {
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $productInvoice->branch_id != $user->branch_id) {
            abort(403);
        }

        if ($productInvoice->status !== 'pendiente') {
            return redirect()->route('product-invoices.show', $productInvoice)
                ->with('error', 'Esta factura no está pendiente de pago.');
        }

        $productInvoice->load(['patient', 'items.product']);

        return view('product-receipts.create', compact('productInvoice'));
    }

    /**
     * Guardar pago
     */
    public function store(Request $request, ProductInvoice $productInvoice)
    {
        $data = $request->validate([
            'cash_amount'        => 'nullable|numeric|min:0',
            'card_amount'        => 'nullable|numeric|min:0',
            'transfer_amount'    => 'nullable|numeric|min:0',
            'card_reference'     => 'nullable|string|max:100',
            'transfer_reference' => 'nullable|string|max:100',
            'notes'              => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        if ($user->role->name !== 'admin' && $productInvoice->branch_id != $user->branch_id) {
            abort(403);
        }

        try {
            $receipt = $this->service->registerPayment($productInvoice, $data);
            return redirect()->route('product-receipts.show', $receipt)
                ->with('success', 'Pago registrado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Ver recibo
     */
    public function show(ProductReceipt $productReceipt)
    {
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $productReceipt->branch_id != $user->branch_id) {
            abort(403);
        }

        $productReceipt->load(['invoice.patient', 'invoice.items.product', 'user', 'branch']);

        return view('product-receipts.show', compact('productReceipt'));
    }

    /**
     * Imprimir recibo
     */
    public function print(ProductReceipt $productReceipt)
    {
        $user = auth()->user();
        if ($user->role->name !== 'admin' && $productReceipt->branch_id != $user->branch_id) {
            abort(403);
        }

        $productReceipt->load(['invoice.patient', 'invoice.items.product', 'user', 'branch']);

        return view('product-receipts.print', compact('productReceipt'));
    }
}