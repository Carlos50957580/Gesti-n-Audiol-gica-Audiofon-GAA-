<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('rnc', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $suppliers = $query->latest()->paginate(15);

        return view('inventory.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('inventory.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'rnc'          => 'nullable|string|max:20|unique:suppliers,rnc',
            'contact_name' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:20',
            'mobile'       => 'nullable|string|max:20',
            'address'      => 'nullable|string',
            'website'      => 'nullable|url|max:255',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        Supplier::create($data);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['movements' => fn($q) => $q->latest()->limit(10)]);
        return view('inventory.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('inventory.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'rnc'          => 'nullable|string|max:20|unique:suppliers,rnc,' . $supplier->id,
            'contact_name' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:20',
            'mobile'       => 'nullable|string|max:20',
            'address'      => 'nullable|string',
            'website'      => 'nullable|url|max:255',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $supplier->update($data);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }
}