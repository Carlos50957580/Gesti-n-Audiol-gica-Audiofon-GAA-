<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::orderBy('name')->get();
        return view('taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('taxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:taxes,code',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_default' => 'sometimes|boolean',
        ]);

        // Manejar checkboxes
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_default'] = $request->has('is_default') ? 1 : 0;

        // Si es el impuesto por defecto, quitar el default a los demás
        if ($validated['is_default']) {
            Tax::where('is_default', 1)->update(['is_default' => 0]);
        }

        $tax = Tax::create($validated);

        return redirect()->route('taxes.index')
            ->with('success', 'Impuesto "' . $tax->name . '" creado exitosamente.');
    }

    public function edit(Tax $tax)
    {
        return view('taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('taxes')->ignore($tax->id)],
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_default' => 'sometimes|boolean',
        ]);

        // Manejar checkboxes
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_default'] = $request->has('is_default') ? 1 : 0;

        // Si es el impuesto por defecto, quitar el default a los demás
        if ($validated['is_default']) {
            Tax::where('is_default', 1)->where('id', '!=', $tax->id)->update(['is_default' => 0]);
        }

        $tax->update($validated);

        return redirect()->route('taxes.index')
            ->with('success', 'Impuesto "' . $tax->name . '" actualizado exitosamente.');
    }

    public function destroy(Tax $tax)
    {
        // Verificar si tiene servicios asociados
        if ($tax->services()->count() > 0) {
            return redirect()->route('taxes.index')
                ->with('error', 'No se puede eliminar el impuesto porque está asociado a ' . $tax->services()->count() . ' servicios.');
        }

        $tax->delete();

        return redirect()->route('taxes.index')
            ->with('success', 'Impuesto eliminado exitosamente.');
    }

    // API: Obtener todos los impuestos activos
    public function getActiveTaxes()
    {
        $taxes = Tax::where('is_active', 1)->get(['id', 'name', 'code', 'rate']);
        return response()->json($taxes);
    }
}