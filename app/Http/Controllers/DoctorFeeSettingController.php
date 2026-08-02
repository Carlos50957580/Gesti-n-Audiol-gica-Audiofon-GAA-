<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\DoctorFeeSetting;
use Illuminate\Http\Request;

class DoctorFeeSettingController extends Controller
{
    public function index()
    {
        $settings = DoctorFeeSetting::with(['doctor', 'category', 'service'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Obtener médicos (usuarios con is_doctor = 1)
        $doctors = User::where('is_doctor', 1)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        // Obtener categorías y servicios para los selects
        $categories = ServiceCategory::where('is_active', 1)
            ->orderBy('name')
            ->get();

        $services = Service::where('is_active', 1)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('doctor-fees.settings', compact('settings', 'doctors', 'categories', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:service_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'calculation_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->calculation_type === 'percentage' && $request->value > 100) {
            return response()->json([
                'errors' => ['value' => ['El porcentaje no puede ser mayor a 100']]
            ], 422);
        }

        // ✅ Validar que no exista una configuración duplicada con el mismo alcance
        $exists = DoctorFeeSetting::where('doctor_id', $request->doctor_id)
            ->where('category_id', $request->category_id)
            ->where('service_id', $request->service_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe una configuración para este médico con el mismo alcance'
            ], 422);
        }

        // ✅ Validar que no exista una configuración general si se intenta crear otra
        if ($request->category_id === null && $request->service_id === null) {
            $existsGeneral = DoctorFeeSetting::where('doctor_id', $request->doctor_id)
                ->whereNull('category_id')
                ->whereNull('service_id')
                ->exists();

            if ($existsGeneral) {
                return response()->json([
                    'message' => 'Ya existe una configuración general para este médico. Solo puede tener una configuración general.'
                ], 422);
            }
        }

        // ✅ Validar que no exista una configuración por categoría si se intenta crear otra para la misma categoría
        if ($request->category_id !== null && $request->service_id === null) {
            $existsCategory = DoctorFeeSetting::where('doctor_id', $request->doctor_id)
                ->where('category_id', $request->category_id)
                ->whereNull('service_id')
                ->exists();

            if ($existsCategory) {
                return response()->json([
                    'message' => 'Ya existe una configuración para esta categoría. Solo puede tener una por categoría.'
                ], 422);
            }
        }

        // ✅ Validar que no exista una configuración por servicio si se intenta crear otra para el mismo servicio
        if ($request->service_id !== null) {
            $existsService = DoctorFeeSetting::where('doctor_id', $request->doctor_id)
                ->where('service_id', $request->service_id)
                ->exists();

            if ($existsService) {
                return response()->json([
                    'message' => 'Ya existe una configuración para este servicio específico.'
                ], 422);
            }
        }

        $setting = DoctorFeeSetting::create($request->all());

        return response()->json([
            'message' => 'Configuración guardada correctamente',
            'setting' => $setting
        ]);
    }

    public function update(Request $request, $id)
    {
        $setting = DoctorFeeSetting::findOrFail($id);

        $request->validate([
            'category_id' => 'nullable|exists:service_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'calculation_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->calculation_type === 'percentage' && $request->value > 100) {
            return response()->json([
                'errors' => ['value' => ['El porcentaje no puede ser mayor a 100']]
            ], 422);
        }

        // ✅ Validar que no exista otra configuración con el mismo alcance (excluyendo la actual)
        $exists = DoctorFeeSetting::where('doctor_id', $setting->doctor_id)
            ->where('category_id', $request->category_id)
            ->where('service_id', $request->service_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe otra configuración para este médico con el mismo alcance'
            ], 422);
        }

        $setting->update($request->all());

        return response()->json([
            'message' => 'Configuración actualizada correctamente',
            'setting' => $setting
        ]);
    }

     public function show($id)
    {
        $setting = DoctorFeeSetting::with(['doctor', 'category', 'service'])->findOrFail($id);
        return response()->json($setting);
    }

    public function destroy($id)
    {
        $setting = DoctorFeeSetting::findOrFail($id);
        $setting->delete();

        return response()->json([
            'message' => 'Configuración eliminada correctamente'
        ]);
    }

    public function getSetting($doctorId)
    {
        $setting = DoctorFeeSetting::where('doctor_id', $doctorId)->first();
        return response()->json($setting);
    }
}