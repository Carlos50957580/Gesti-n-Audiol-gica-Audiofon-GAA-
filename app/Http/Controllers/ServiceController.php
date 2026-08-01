<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Insurance;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['category', 'taxes']);

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por estado
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filtro por historia clínica
        if ($request->filled('requires_clinical_record')) {
            if ($request->requires_clinical_record == '1') {
                $query->requiresClinicalRecord();
            } else {
                $query->where('requires_clinical_record', 0);
            }
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $services = $query->orderBy('category_id')->orderBy('name')->paginate(15);
        $categories = ServiceCategory::where('is_active', 1)->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('is_active', 1)->get();
        $insurances = Insurance::where('active', 1)->get();
        $taxes = Tax::where('is_active', 1)->get();
        
        return view('services.create', compact('categories', 'insurances', 'taxes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:service_categories,id',
            'code' => 'nullable|string|max:50|unique:services,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
            'insurance_coverage' => 'nullable|array',
            'insurance_coverage.*.insurance_id' => 'required|exists:insurances,id',
            'insurance_coverage.*.coverage_percentage' => 'nullable|numeric|min:0|max:100',
            'insurance_coverage.*.fixed_amount' => 'nullable|numeric|min:0',
            'taxes' => 'nullable|array',
            'taxes.*' => 'exists:taxes,id',
        ]);

        // ✅ Manejar checkboxes correctamente (1 = activado, 0 = desactivado)
        $validated['requires_authorization'] = $request->has('requires_authorization') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Si la categoría requiere historia clínica, forzar el valor a 1
        if ($request->filled('category_id')) {
            $category = ServiceCategory::find($request->category_id);
            if ($category && $category->requires_clinical_record) {
                $validated['requires_clinical_record'] = 1;
            } else {
                $validated['requires_clinical_record'] = $request->has('requires_clinical_record') ? 1 : 0;
            }
        } else {
            $validated['requires_clinical_record'] = $request->has('requires_clinical_record') ? 1 : 0;
        }

        $service = Service::create($validated);

        // ✅ Asociar impuestos al servicio
        if ($request->has('taxes')) {
            $taxes = $request->taxes;
            $syncData = [];
            foreach ($taxes as $taxId) {
                $syncData[$taxId] = ['is_required' => 1];
            }
            $service->taxes()->sync($syncData);
        }

        // Guardar cobertura por seguro
        if ($request->has('insurance_coverage')) {
            foreach ($request->insurance_coverage as $coverage) {
                if (!empty($coverage['insurance_id'])) {
                    $service->insuranceCoverage()->create([
                        'insurance_id' => $coverage['insurance_id'],
                        'coverage_percentage' => $coverage['coverage_percentage'] ?? 0,
                        'fixed_amount' => $coverage['fixed_amount'] ?? null,
                        'requires_authorization' => isset($coverage['requires_authorization']) ? 1 : 0,
                        'is_active' => 1
                    ]);
                }
            }
        }

        return redirect()->route('services.index')
            ->with('success', 'Servicio "' . $service->name . '" creado exitosamente.');
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::where('is_active', 1)->get();
        $insurances = Insurance::where('active', 1)->get();
        $taxes = Tax::where('is_active', 1)->get();
        $service->load(['insuranceCoverage', 'taxes']);
        $selectedTaxes = $service->taxes->pluck('id')->toArray();
        
        return view('services.edit', compact('service', 'categories', 'insurances', 'taxes', 'selectedTaxes'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:service_categories,id',
            'code' => ['nullable', 'string', 'max:50', Rule::unique('services')->ignore($service->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
            'insurance_coverage' => 'nullable|array',
            'insurance_coverage.*.id' => 'nullable|exists:service_insurance_coverage,id',
            'insurance_coverage.*.insurance_id' => 'required|exists:insurances,id',
            'insurance_coverage.*.coverage_percentage' => 'nullable|numeric|min:0|max:100',
            'insurance_coverage.*.fixed_amount' => 'nullable|numeric|min:0',
            'taxes' => 'nullable|array',
            'taxes.*' => 'exists:taxes,id',
        ]);

        // ✅ Manejar checkboxes correctamente (1 = activado, 0 = desactivado)
        $validated['requires_authorization'] = $request->has('requires_authorization') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Si la categoría requiere historia clínica, forzar el valor a 1
        if ($request->filled('category_id')) {
            $category = ServiceCategory::find($request->category_id);
            if ($category && $category->requires_clinical_record) {
                $validated['requires_clinical_record'] = 1;
            } else {
                $validated['requires_clinical_record'] = $request->has('requires_clinical_record') ? 1 : 0;
            }
        } else {
            $validated['requires_clinical_record'] = $request->has('requires_clinical_record') ? 1 : 0;
        }

        $service->update($validated);

        // ✅ Actualizar impuestos del servicio
        if ($request->has('taxes')) {
            $taxes = $request->taxes;
            $syncData = [];
            foreach ($taxes as $taxId) {
                $syncData[$taxId] = ['is_required' => 1];
            }
            $service->taxes()->sync($syncData);
        } else {
            // Si no hay impuestos seleccionados, eliminar todos
            $service->taxes()->detach();
        }

        // Actualizar cobertura por seguro
        if ($request->has('insurance_coverage')) {
            $existingIds = $service->insuranceCoverage()->pluck('id')->toArray();
            $updatedIds = [];

            foreach ($request->insurance_coverage as $coverage) {
                if (!empty($coverage['insurance_id'])) {
                    if (isset($coverage['id']) && in_array($coverage['id'], $existingIds)) {
                        // Actualizar existente
                        $service->insuranceCoverage()->where('id', $coverage['id'])->update([
                            'coverage_percentage' => $coverage['coverage_percentage'] ?? 0,
                            'fixed_amount' => $coverage['fixed_amount'] ?? null,
                            'requires_authorization' => isset($coverage['requires_authorization']) ? 1 : 0,
                            'is_active' => 1
                        ]);
                        $updatedIds[] = $coverage['id'];
                    } else {
                        // Crear nueva
                        $new = $service->insuranceCoverage()->create([
                            'insurance_id' => $coverage['insurance_id'],
                            'coverage_percentage' => $coverage['coverage_percentage'] ?? 0,
                            'fixed_amount' => $coverage['fixed_amount'] ?? null,
                            'requires_authorization' => isset($coverage['requires_authorization']) ? 1 : 0,
                            'is_active' => 1
                        ]);
                        $updatedIds[] = $new->id;
                    }
                }
            }

            // Eliminar los que no están en el array
            $toDelete = array_diff($existingIds, $updatedIds);
            if (!empty($toDelete)) {
                $service->insuranceCoverage()->whereIn('id', $toDelete)->delete();
            }
        }

        return redirect()->route('services.index')
            ->with('success', 'Servicio "' . $service->name . '" actualizado exitosamente.');
    }

    public function destroy(Service $service)
    {
        // Verificar si tiene facturas asociadas
        if ($service->invoiceItems()->count() > 0) {
            return redirect()->route('services.index')
                ->with('error', 'No se puede eliminar el servicio porque tiene facturas asociadas.');
        }

        // Eliminar relaciones antes de eliminar el servicio
        $service->taxes()->detach();
        $service->insuranceCoverage()->delete();
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Servicio eliminado exitosamente.');
    }

    // API: Obtener servicios por categoría
    public function getByCategory($categoryId)
    {
        $services = Service::where('category_id', $categoryId)
            ->where('is_active', 1)
            ->with('taxes')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'requires_clinical_record', 'duration_minutes']);

        return response()->json($services);
    }

    // API: Obtener detalle de un servicio (con impuestos y cobertura)
    public function getDetail($id)
    {
        $service = Service::with([
            'category', 
            'insuranceCoverage.insurance',
            'taxes' => function($query) {
                $query->where('is_active', 1);
            }
        ])->findOrFail($id);

        // Calcular impuestos para el precio del servicio
        $taxCalculation = $service->calculateTaxes($service->price);

        return response()->json([
            'service' => $service,
            'taxes' => $taxCalculation
        ]);
    }

    // Obtener cobertura de un servicio para un seguro específico
    public function getCoverage($serviceId, $insuranceId)
    {
        $service = Service::findOrFail($serviceId);
        $coverage = $service->getCoverageForInsurance(Insurance::findOrFail($insuranceId));

        if ($coverage) {
            $calculation = $coverage->calculateCoverage($service->price);
            return response()->json([
                'has_coverage' => true,
                'coverage_percentage' => $calculation['percentage'],
                'insurance_amount' => $calculation['insurance_amount'],
                'patient_amount' => $calculation['patient_amount'],
                'requires_authorization' => $coverage->requires_authorization
            ]);
        }

        return response()->json([
            'has_coverage' => false,
            'coverage_percentage' => 0,
            'insurance_amount' => 0,
            'patient_amount' => $service->price,
            'requires_authorization' => 0
        ]);
    }

    // ✅ NUEVO: Obtener impuestos de un servicio
    public function getTaxes($serviceId)
    {
        $service = Service::with('taxes')->findOrFail($serviceId);
        $taxCalculation = $service->calculateTaxes($service->price);
        
        return response()->json($taxCalculation);
    }
}