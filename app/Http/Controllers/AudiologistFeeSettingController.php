<?php
// app/Http/Controllers/AudiologistFeeSettingController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AudiologistFeeSetting;
use Illuminate\Http\Request;

class AudiologistFeeSettingController extends Controller
{
    public function index()
    {
        $settings = AudiologistFeeSetting::with('audiologist')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // ✅ CORREGIDO: Obtener audiólogos por role_id
        $audiologistRole = Role::where('name', 'audiologist')->orWhere('name', 'audiologo')->first();
        $audiologists = collect();
        
        if ($audiologistRole) {
            $audiologists = User::where('role_id', $audiologistRole->id)
                ->orderBy('name')
                ->get();
        }
            
        return view('audiologist-fees.settings', compact('settings', 'audiologists'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'audiologist_id' => 'required|exists:users,id|unique:audiologist_fees_settings,audiologist_id',
            'calculation_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        
        if ($request->calculation_type === 'percentage' && $request->value > 100) {
            return response()->json([
                'errors' => ['value' => ['El porcentaje no puede ser mayor a 100']]
            ], 422);
        }
        
        $setting = AudiologistFeeSetting::create($request->all());
        
        return response()->json([
            'message' => 'Configuración guardada correctamente',
            'setting' => $setting
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $setting = AudiologistFeeSetting::findOrFail($id);
        
        $request->validate([
            'calculation_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        
        if ($request->calculation_type === 'percentage' && $request->value > 100) {
            return response()->json([
                'errors' => ['value' => ['El porcentaje no puede ser mayor a 100']]
            ], 422);
        }
        
        $setting->update($request->all());
        
        return response()->json([
            'message' => 'Configuración actualizada correctamente',
            'setting' => $setting
        ]);
    }
    
    public function destroy($id)
    {
        $setting = AudiologistFeeSetting::findOrFail($id);
        $setting->delete();
        
        return response()->json([
            'message' => 'Configuración eliminada correctamente'
        ]);
    }
    
    public function getSetting($audiologistId)
    {
        $setting = AudiologistFeeSetting::where('audiologist_id', $audiologistId)->first();
        return response()->json($setting);
    }
}