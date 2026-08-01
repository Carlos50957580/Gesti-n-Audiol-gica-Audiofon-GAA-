<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ServiceCategory::withCount('services')->get();
        return view('service-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('service-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
        ]);

        // ✅ Manejar checkboxes correctamente
        $validated['requires_clinical_record'] = $request->has('requires_clinical_record') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $category = ServiceCategory::create($validated);

        return redirect()
            ->route('service-categories.index')
            ->with('success', 'Categoría "' . $category->name . '" creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCategory $serviceCategory)
    {
        return view('service-categories.edit', compact('serviceCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('service_categories')->ignore($serviceCategory->id)],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
        ]);

        // ✅ Manejar checkboxes correctamente
        $validated['requires_clinical_record'] = $request->has('requires_clinical_record') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $serviceCategory->update($validated);

        return redirect()
            ->route('service-categories.index')
            ->with('success', 'Categoría "' . $serviceCategory->name . '" actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        // Verificar si tiene servicios asociados
        if ($serviceCategory->services()->count() > 0) {
            return redirect()
                ->route('service-categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene ' . $serviceCategory->services()->count() . ' servicios asociados.');
        }

        $serviceCategory->delete();

        return redirect()
            ->route('service-categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}