<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStock;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'stocks']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(15);
        $categories = ProductCategory::active()->orderBy('name')->get();

        return view('inventory.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ProductCategory::active()->orderBy('name')->get();
        $branches = Branch::where('is_active', 1)->get();

        return view('inventory.products.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'  => 'nullable|exists:product_categories,id',
            'code'         => 'required|string|max:50|unique:products,code',
            'barcode'      => 'nullable|string|max:100|unique:products,barcode',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'unit'         => 'required|string|max:20',
            'cost_price'   => 'required|numeric|min:0',
            'sale_price'   => 'required|numeric|min:0',
            'min_stock'    => 'required|integer|min:0',
            'max_stock'    => 'nullable|integer|min:0',
            'has_tax'      => 'boolean',
            'is_active'    => 'boolean',
            'image'        => 'nullable|image|max:2048',
            'initial_stocks' => 'nullable|array', // [branch_id => quantity]
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        // Crear stocks iniciales por sucursal
        if (!empty($data['initial_stocks'])) {
            foreach ($data['initial_stocks'] as $branchId => $quantity) {
                if ($quantity > 0) {
                    ProductStock::create([
                        'product_id' => $product->id,
                        'branch_id'  => $branchId,
                        'quantity'   => $quantity,
                    ]);
                }
            }
        }

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'stocks.branch', 'suppliers']);
        return view('inventory.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::active()->orderBy('name')->get();
        $branches = Branch::where('is_active', 1)->get();
        $stocks = $product->stocks->keyBy('branch_id');

        return view('inventory.products.edit', compact('product', 'categories', 'branches', 'stocks'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id'  => 'nullable|exists:product_categories,id',
            'code'         => 'required|string|max:50|unique:products,code,' . $product->id,
            'barcode'      => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'unit'         => 'required|string|max:20',
            'cost_price'   => 'required|numeric|min:0',
            'sale_price'   => 'required|numeric|min:0',
            'min_stock'    => 'required|integer|min:0',
            'max_stock'    => 'nullable|integer|min:0',
            'has_tax'      => 'boolean',
            'is_active'    => 'boolean',
            'image'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}                                                  