<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::withCount('services')->get();
        return view('service-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('service-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
        ]);

        // ✅ CORREGIDO: Usar input() en lugar de has()
        $validated['requires_clinical_record'] = $request->input('requires_clinical_record', 0);
        $validated['is_active'] = $request->input('is_active', 0);

        $category = ServiceCategory::create($validated);

        return redirect()
            ->route('service-categories.index')
            ->with('success', 'Categoría "' . $category->name . '" creada exitosamente.');
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        return view('service-categories.edit', compact('serviceCategory'));
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('service_categories')->ignore($serviceCategory->id)],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
        ]);

        // ✅ CORREGIDO: Usar input() en lugar de has()
        $validated['requires_clinical_record'] = $request->input('requires_clinical_record', 0);
        $validated['is_active'] = $request->input('is_active', 0);

        $serviceCategory->update($validated);

        return redirect()
            ->route('service-categories.index')
            ->with('success', 'Categoría "' . $serviceCategory->name . '" actualizada exitosamente.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
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