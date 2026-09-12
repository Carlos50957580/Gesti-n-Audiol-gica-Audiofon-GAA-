<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::withCount('products')->latest()->paginate(15);
        return view('inventory.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('inventory.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50|unique:product_categories,code',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:20',
            'icon'        => 'nullable|string|max:50',
            'is_active'   => 'boolean',
        ]);

        ProductCategory::create($data);

        return redirect()->route('product-categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(ProductCategory $productCategory)
    {
        return view('inventory.categories.edit', compact('productCategory'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50|unique:product_categories,code,' . $productCategory->id,
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:20',
            'icon'        => 'nullable|string|max:50',
            'is_active'   => 'boolean',
        ]);

        $productCategory->update($data);

        return redirect()->route('product-categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        if ($productCategory->products()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: tiene productos asociados.');
        }

        $productCategory->delete();

        return redirect()->route('product-categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}