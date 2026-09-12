<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Branch;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    protected $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
    }

    public function index(Request $request)
    {
        $query = StockMovement::with(['branch', 'supplier', 'user', 'items.product']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        $movements = $query->latest()->paginate(15);
        $branches = Branch::where('is_active', 1)->get();

        return view('inventory.movements.index', compact('movements', 'branches'));
    }

    public function createEntry()
    {
        $products = Product::active()->with('stocks')->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();
        $branches = Branch::where('is_active', 1)->get();

        return view('inventory.movements.create-entry', compact('products', 'suppliers', 'branches'));
    }

    public function storeEntry(Request $request)
    {
        $data = $request->validate([
            'branch_id'     => 'required|exists:branches,id',
            'supplier_id'   => 'nullable|exists:suppliers,id',
            'movement_date' => 'required|date',
            'notes'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $total = collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']);
        $data['total'] = $total;

        $movement = $this->inventory->registerEntry($data);

        return redirect()->route('stock-movements.show', $movement)
            ->with('success', 'Entrada registrada. Confirma para aplicar cambios al stock.');
    }

    public function createExit()
    {
        $products = Product::active()->with('stocks')->orderBy('name')->get();
        $branches = Branch::where('is_active', 1)->get();

        return view('inventory.movements.create-exit', compact('products', 'branches'));
    }

    public function storeExit(Request $request)
    {
        $data = $request->validate([
            'branch_id'     => 'required|exists:branches,id',
            'movement_date' => 'required|date',
            'notes'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        try {
            $movement = $this->inventory->registerExit($data);
            return redirect()->route('stock-movements.show', $movement)
                ->with('success', 'Salida registrada. Confirma para aplicar cambios al stock.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function createTransfer()
    {
        $products = Product::active()->with('stocks')->orderBy('name')->get();
        $branches = Branch::where('is_active', 1)->get();

        return view('inventory.movements.create-transfer', compact('products', 'branches'));
    }

    public function storeTransfer(Request $request)
    {
        $data = $request->validate([
            'branch_id'             => 'required|exists:branches,id',
            'destination_branch_id' => 'required|exists:branches,id|different:branch_id',
            'movement_date'         => 'required|date',
            'notes'                 => 'nullable|string',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
        ]);

        try {
            $movement = $this->inventory->transfer($data);
            return redirect()->route('stock-movements.show', $movement)
                ->with('success', 'Transferencia registrada. Confirma para aplicar cambios.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['branch', 'destinationBranch', 'supplier', 'user', 'items.product']);
        return view('inventory.movements.show', compact('stockMovement'));
    }

    public function confirm(StockMovement $stockMovement)
    {
        try {
            $this->inventory->confirmMovement($stockMovement);
            return redirect()->route('stock-movements.show', $stockMovement)
                ->with('success', 'Movimiento confirmado. Stock actualizado.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(StockMovement $stockMovement)
    {
        try {
            $this->inventory->cancelMovement($stockMovement);
            return redirect()->route('stock-movements.show', $stockMovement)
                ->with('success', 'Movimiento cancelado.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * API: Obtener stock de un producto en una sucursal
     */
    public function getProductStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'branch_id'  => 'required|exists:branches,id',
        ]);

        $stock = $this->inventory->getOrCreateStock($request->product_id, $request->branch_id);

        return response()->json([
            'product_id' => $stock->product_id,
            'branch_id'  => $stock->branch_id,
            'quantity'   => $stock->quantity,
            'available'  => $stock->available_quantity,
        ]);
    }
}